# Manual iCal Synchronization Design

## Goal

Allow a property owner to manually connect Airbnb or Booking.com calendars by pasting the provider's exported iCal URL into Verified Shortlet, while Verified Shortlet generates a secure `.ics` URL that the owner pastes back into the provider. Imported reservations block availability locally; native Verified Shortlet reservations are exported for external platforms to import.

## Scope

This version supports Airbnb and Booking.com using standard iCalendar feeds. It does not use either provider's authenticated API and therefore does not promise instant synchronization. Connections are property-specific and business-scoped.

The owner flow is:

1. Select Airbnb or Booking.com for a property.
2. Paste that provider's HTTPS calendar-export URL.
3. Save the connection and run an explicit first sync.
4. See sync status, imported event count, last successful sync, and any failure message.
5. Copy the secure Verified Shortlet `.ics` URL.
6. Paste the Verified Shortlet URL into the provider's calendar-import screen.
7. Use “Sync now” when needed; Laravel also refreshes active feeds every 15 minutes.

## Existing system of record

- `external_calendar_connections` stores one inbound feed per property/provider and its sync status.
- `external_calendar_sync_runs` and `external_calendar_sync_items` retain append-only operational history.
- `property_availability_blocks` stores one active or released block per imported external event.
- `bookings` remains the source of native Verified Shortlet stays.
- `property_channel_connections` remains the owner's selected-channel record and keeps `connection_status = pending` until a valid iCal feed has synchronized, after which it becomes `connected`.

No external event becomes a native booking. This prevents imported placeholders from polluting finance, guest, or booking lifecycle records.

## Additive schema

Create `property_calendar_exports`:

- UUID `id`
- tenant-safe `business_id` and `property_id`
- encrypted `plain_token` so an authenticated owner can copy the current feed URL
- SHA-256 `token_hash` for constant-time public-feed authentication
- `generated_at`, `last_accessed_at`, `revoked_at`, `status`
- audit columns and timestamps
- unique active export per property

Add a unique constraint for `(external_calendar_connection_id, source_reference)` on imported availability blocks so repeated syncs update the same external event instead of duplicating it.

All schema corrections are new migrations; completed migration history is not edited.

## Application boundaries

### Connection management

`ManageExternalCalendarConnection` validates tenant ownership and provider, saves or disables an inbound URL, ensures the property has one export token, and updates the related selected-channel record. Replacing a feed URL resets sync state but preserves prior sync history.

Only `https` URLs are accepted. The fetcher rejects credentials embedded in URLs, literal IP hosts, localhost/private/reserved network resolutions, non-calendar response sizes above 5 MB, redirects, and responses that do not contain an iCalendar payload. Provider selection and URL host are stored separately; no provider credentials are stored.

### Import

`ImportExternalCalendar` fetches through a dedicated safe HTTP client, parses unfolded iCalendar lines, and accepts all-day `VEVENT` records with `UID`, `DTSTART`, and exclusive `DTEND`. Cancelled events are released. Invalid events are recorded as failed sync items and do not block inventory.

For every valid event:

- Upsert an external `property_availability_block` using connection plus UID.
- Store only a neutral reason such as `Airbnb reservation`; do not expose external guest names.
- Mark the interval `[starts_on, ends_on)` as blocking.
- Reject or record conflicts with native bookings instead of modifying the native booking.
- Release previously imported active blocks no longer present in a successful complete feed.

The complete operation is transactional after the feed has been fetched and parsed. Every attempt creates a sync run with received, created, updated, released/skipped, conflict, and failed counts.

### Export

`BuildPropertyCalendarFeed` produces RFC-style iCalendar text from:

- native bookings whose status blocks availability and whose payment has not failed;
- active owner-created manual blocks.

It deliberately excludes blocks imported from external calendars to avoid provider-to-provider loops. Dates are all-day values with exclusive checkout/end dates. UIDs are stable and contain no guest email, phone, or name. The public endpoint requires the property-export UUID and matching secret token, is rate-limited, returns `text/calendar`, and updates `last_accessed_at`.

Regenerating the export link revokes the old token immediately.

## UI

The property channel setup and property details page show Airbnb and Booking.com cards with:

- selected checkbox;
- external iCal URL field;
- Save connection action;
- Sync now action;
- status badge: Not configured, Pending, Synced, or Failed;
- last sync time and imported count;
- failure explanation without secrets;
- generated Verified Shortlet `.ics` URL with Copy link;
- concise two-step instructions for copying the external URL in and the Verified Shortlet URL out.

The calendar renders imported blocks with their provider label. The owner cannot manually delete an imported block; it is released by a successful provider sync, connection disable, or explicit connection removal.

## Scheduling and operations

- `SyncExternalCalendarConnection` is a queueable job with overlap protection per connection.
- `calendars:sync` dispatches jobs for active configured connections.
- Laravel scheduler runs it every 15 minutes without overlapping.
- Manual sync dispatches synchronously for immediate UI feedback in this MVP.
- Fetch timeout is 10 seconds with one retry for connection failures only.
- A failed fetch leaves existing blocks active because absence cannot be trusted from an incomplete feed.
- Three consecutive failures mark the connection failed but do not release inventory.

## Authorization and security

- Owner routes require authentication, active business context, and business-owner middleware.
- Every property and connection mutation is resolved through the active business.
- Public export tokens are random, revocable, stored encrypted plus hashed, compared in constant time, and never written to logs.
- External feed URLs are encrypted at rest using the existing `credentials` encrypted cast; a masked host-only value is used in UI after save.
- Feed content, external summaries, and URLs are treated as untrusted input and escaped in UI/log output.
- No external HTTP request may reach loopback, link-local, private, multicast, or reserved addresses.

## Failure behavior

- Invalid URL: connection is not saved and a field-level validation error is shown.
- Fetch/parse failure: sync run is failed, prior blocks remain active, and the owner sees a safe message.
- Conflicting external event: sync item records conflict; native booking is never cancelled or overwritten.
- Missing event after a successful complete feed: corresponding external block is released.
- Disabled connection: its active imported blocks are released and scheduled syncing stops.
- Duplicate manual submission/job: locks and connection-event uniqueness make it idempotent.

## Verification

Automated tests must cover:

- business scoping and non-owner rejection;
- URL validation and SSRF denial;
- connection create/update/disable;
- parsing folded lines and all-day dates;
- initial import, idempotent repeat, changed dates, cancellation, and removal;
- conflict preservation for native bookings;
- failed fetch preserving old blocks;
- generated feed authentication, revocation, content type, stable UIDs, exclusive dates, and privacy;
- export of native bookings/manual blocks and exclusion of imported blocks;
- scheduler command selection and job dispatch;
- channel/detail UI state, Copy link, Sync now, failure state, and mobile layout;
- full suite, MySQL migration, Blade/route caches, Vite build, and desktop/mobile browser flow.

## Deferred

- OAuth or direct Airbnb/Booking.com APIs.
- Instant push notifications and provider webhooks.
- Recurring iCalendar rules and timed/non-all-day inventory events.
- Google/Outlook calendar-specific APIs.
- Automatic conflict resolution or cancellation.
- Channel-specific pricing, content, messaging, and payment synchronization.
