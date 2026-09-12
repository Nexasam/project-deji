# Verified Shortlet MVP Release Candidate

Date: 10 September 2026  
Scope: Monday–Friday owner workspace plus serviced-apartment marketplace MVP

## Release status

- Fresh, forward-only MySQL installation: passed in `project_nexa_26_rc_20260910b`.
- Migrations: all 31 migrations passed from an empty database.
- Seeds: access control, amenities, workflows, two operators, and ten marketplace apartments passed.
- SQLite regression: 100 tests and 604 assertions passed.
- Blade cache, route cache, config cache, Vite production build, and `git diff --check`: passed.
- Main database migration preview: nothing pending.
- MySQL PHPUnit release gate: 100 tests and 604 assertions passed in the dedicated `project_nexa_26_tests_20260910a` database.
- Browser smoke test: passed on desktop 1440×900 and mobile 390×844 using system Chrome through Playwright.

## Browser evidence

The release-candidate database was used through `http://127.0.0.1:8002`.

- Landing page, password-reset page, guest registration, and verification redirect rendered.
- A signed-in guest completed simulated Paystack checkout for Banana Island Harbour Flat.
- The database recorded the booking as `confirmed`, payment as `paid`, and provider payment as `completed`.
- Owner login and dashboard, properties, property wizard step 1, bookings, calendar, finance, and operations rendered.
- Refresh, Back, and Forward preserved the authenticated owner workspace.
- Mobile pages rendered at 390px without document-level horizontal overflow.
- No browser console errors or HTTP 500 responses were observed.

Screenshots are intentionally outside version control:

- `/tmp/nexa-release-landing-desktop.png`
- `/tmp/nexa-release-operations-desktop.png`
- `/tmp/nexa-release-mobile.png`

## Test access

- Lagoon Stays owner: `owner@lagoonstays.test` / `Password123!`
- Coastline Residences owner: `owner@coastlineresidences.test` / `Password123!`

These credentials are demonstration data only and must not be reused in production.

## Backup and recovery

Verified application backup:

`storage/app/backups/project_nexa_26-pre-rc-20260910-1718-no-routines.sql`

SHA-256:

`839a9a940dff54c7e678b92fb24186cb9dfc684a64b675fc5185aacc4369ac07`

Restore rehearsal: passed in the new `project_nexa_26_restore_20260910a` database. It restored 31 migration records, 5 businesses, 15 properties, 3 bookings, and 3 payments from the source backup.

The XAMPP MariaDB installation has a system-table version mismatch that prevents routine export. The application defines no stored procedures, functions, or triggers in source, so the verified backup contains the application schema and data while explicitly skipping stored routines. Run `mysql_upgrade` during planned local database maintenance before requiring routine-inclusive dumps.

An earlier `...1715.sql` attempt is incomplete and must not be used for recovery.

Recovery rehearsal should always restore into a new database first:

1. Verify the backup checksum.
2. Create a new empty recovery database with `utf8mb4_unicode_ci`.
3. Import the verified `...1718-no-routines.sql` file into that new database.
4. point a temporary application environment to the recovery database.
5. Run `php artisan migrate:status`, smoke-test login and key totals, and compare row counts.
6. Switch an environment to the restored database only after acceptance. Never overwrite the source database without a separately approved recovery operation.

## Security and integrity coverage

Automated coverage verifies authentication redirects, safe intended URLs, CSRF middleware paths, request validation, tenant-scoped property/booking/calendar/finance/task access, booking overlap prevention, idempotent checkout/payment references, immutable finance history, and allowed lifecycle transitions.

Uploads are stored on private application storage where required; receipt and wizard request validation restrict accepted type and size. Production web-server upload and request-size limits must match the Laravel limits.

## Known limitations

- Paystack is represented by the real gateway boundary, but success is simulated until production credentials and webhooks are configured.
- Airbnb, Booking.com, WhatsApp, iCal, Google Calendar, and Outlook synchronization remain connection-pending.
- Multiple independently bookable units and “Another flat in a property” remain deferred.
- AI concierge and pricing copy are presentation-only; no production AI provider is connected.
- Disabled sidebar modules (advanced reports, documents, staffing/settings) are outside this MVP.
- Email delivery and password-reset links require a production mail provider and queue configuration.
- Chrome desktop/mobile was exercised; Safari and Firefox remain untested.

## Deployment checklist

1. Configure production secrets outside source control.
2. Create a new database backup and verify its checksum.
3. Run `php artisan migrate --pretend` and review SQL.
4. Run `php artisan migrate --force`.
5. Run `php artisan optimize` and build/deploy versioned Vite assets.
6. Exercise login, marketplace booking, owner calendar, finance, operations, and dashboard.
7. Monitor Laravel, queue, web-server, and payment-webhook logs.
8. Do not create a release tag until the dedicated MySQL PHPUnit gate passes and the shared dirty worktree has been reviewed and committed intentionally.
