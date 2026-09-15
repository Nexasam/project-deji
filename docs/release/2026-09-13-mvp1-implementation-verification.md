# MVP 1 Implementation Verification — 13 September 2026

## Implemented acceptance path

The application now supports the pilot flow without direct database edits:

`Property setup → admin publish → marketplace/manual booking → simulated payment → preparation → check-in → in-stay support → checkout → turnover cleaning → inspection → ready/completed`

Availability is shared by marketplace bookings, owner-created bookings, manual blocks, and imported iCal events. Booking confirmation and terminal transitions are locked, historically recorded, and emit idempotent domain events.

## Automated evidence

- SQLite full suite: **166 tests, 975 assertions, passed**.
- MySQL full suite before the final reminder/evidence assertions: **165 tests, 966 assertions, passed**.
- MySQL focused regression for the final lifecycle, finance, and staff-evidence additions: **16 tests, 89 assertions, passed**.
- Dedicated two-process MySQL double-booking proof: **1 test, 8 assertions, passed**; exactly one request confirmed and the other received the availability rejection.
- Disposable SQLite `migrate:fresh --seed`: passed.
- A second complete `db:seed` on the same disposable database: passed.
- The marketplace seeder's automated repeatability test verifies two operators, five properties each, and no duplicated marketplace properties.
- The same repeatability test ran successfully on the MySQL test database as part of the full engine suite.
- Blade view compilation: passed.
- Route cache: passed.
- Configuration cache: passed.
- Vite production build: passed.
- `git diff --check`: passed.

## Scheduled work registered

- `calendars:sync` every 15 minutes;
- `calendars:notify-stale` every 15 minutes;
- `operations:notify-overdue` every five minutes;
- `bookings:notify-upcoming-arrivals` hourly.

Email delivery uses queued jobs and a durable `notification_deliveries` record. Failed mail does not remove or roll back the in-app notification.

## Remaining physical release rehearsal

These are release-environment checks, not missing application flows:

- optionally repeat the simultaneous-request proof against the final production-shaped MySQL staging instance;
- complete owner/admin/guest/staff browser journeys on target desktop and mobile browsers;
- start the deployed supervisor worker and cron, then observe a real mail and iCal polling cycle;
- create a current pre-release database/private-upload backup and rehearse its restoration;
- record and triage any P1 findings from that rehearsal.

Real payment initialization, signed webhooks, settlement reconciliation, and production payment-provider credentials remain explicitly outside the simulated-payment MVP 1.

## Formatting note

All files added or materially changed for the MVP lifecycle were formatted with Laravel Pint. A whole-repository `pint --test` still reports older formatting debt in unrelated legacy migrations/services; it is not a functional test failure and should be handled as a separate mechanical cleanup to avoid obscuring this release diff.

## Post-verification extension — 14 September 2026

Verified-stay reviews and owner responses are now connected to the completed-booking lifecycle:

- completion creates one 30-day review invitation and guest notification;
- only the booking guest can submit one review after completion;
- public marketplace ratings and review content use approved verified-stay records;
- the correct business owner can publish one response and the guest is notified;
- incomplete stays, duplicate submissions and cross-user/business access are rejected.

Focused booking lifecycle, review and marketplace verification: **22 tests, 115 assertions, passed**. Blade compilation, route caching and `git diff --check` also passed after the extension.
