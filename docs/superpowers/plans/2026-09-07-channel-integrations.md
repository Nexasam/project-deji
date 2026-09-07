# Property Channel Integrations

## Current state

The property wizard stores Airbnb and Booking.com iCal URLs plus a WhatsApp booking number in `property_channel_connections`. New records remain `pending`; no external request is made during property setup.

## Recommended delivery order

1. **iCal ingestion foundation**
   - Validate URLs and encrypt sensitive connection metadata.
   - Add sync timestamps, failure details, retry counters, and a disabled state.
   - Fetch feeds through queued jobs with strict timeouts, size limits, and SSRF protection.
   - Normalize external reservations into calendar blocks and make imports idempotent by provider event identifier.

2. **Airbnb and Booking.com calendar sync**
   - Implement provider adapters behind one calendar-import contract.
   - Schedule periodic imports and allow an owner-triggered refresh.
   - Surface `pending`, `connected`, `syncing`, `failed`, and `disconnected` states.
   - Record sync runs and show the last successful import on the property page.

3. **WhatsApp-assisted bookings**
   - Keep manual booking entry as the first release.
   - Pre-fill the saved business number and property context.
   - Add Meta WhatsApp Cloud API only after webhook verification, consent, message-template, and credential-storage requirements are confirmed.

## Schema additions expected later

- `property_channel_connections`: encrypted credentials/URLs, `last_synced_at`, `last_successful_sync_at`, `last_error`, `retry_count`, and provider-specific settings.
- `property_channel_sync_runs`: provider, start/end timestamps, outcome, imported/updated/skipped counts, and sanitized error context.
- External identifiers on imported availability blocks or bookings, protected by a unique provider/property/external-id constraint.

## Safety and acceptance criteria

- Never expose full iCal URLs or provider credentials in logs or owner-facing HTML.
- Reject private-network and unsupported URLs before fetching.
- Re-importing the same feed creates no duplicate calendar blocks.
- Removing a connection stops future jobs without deleting historical bookings.
- Provider failures do not block property creation or publication review.
- Every sync is business-scoped, observable, retryable, and covered by adapter contract tests.
