# MVP 1 Production Runbook

This runbook covers the owner-operated pilot with simulated payments. It does not approve taking real card payments.

## Namecheap staging with presentation data

Use a dedicated staging subdomain and database. The subdomain document root must be the Laravel `public/` directory, not the repository root; otherwise `.env`, private storage and application source may be exposed.

### First deployment from the `dev` branch

The exact home-directory path depends on the Namecheap account. Replace the placeholders below after confirming them in cPanel or SSH.

```bash
cd /home/CPANEL_USER/repositories/project-nexa-26
git fetch origin
git checkout dev
git pull --ff-only origin dev
composer install --no-dev --prefer-dist --optimize-autoloader
cp .env.example .env
php artisan key:generate
npm ci
npm run build
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
php artisan optimize
```

Do not run `migrate:fresh`, `db:wipe` or import a local database dump into staging. Those operations can delete or replace staging records. `php artisan db:seed --force` is designed to be repeatable, but it intentionally restores the named presentation records—including **Eko Pearl Executive Residence** in its pending-review state.

`public/build` is ignored by Git. Therefore, the server must successfully run `npm ci && npm run build`, or the deployment pipeline must publish a built release artifact that includes `public/build`. A Git pull by itself is insufficient.

Before running Artisan for the first time, replace `.env` values with the staging domain, staging database and staging-only secrets:

```dotenv
APP_NAME="Verified Shortlet Staging"
APP_ENV=staging
APP_DEBUG=false
APP_URL=https://staging.example.com
APP_KEY=

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=<staging_database>
DB_USERNAME=<staging_database_user>
DB_PASSWORD=<strong_database_password>

SESSION_DRIVER=database
SESSION_SECURE_COOKIE=true
CACHE_STORE=database
QUEUE_CONNECTION=database
FILESYSTEM_DISK=local

MAIL_MAILER=smtp
MAIL_HOST=<smtp_host>
MAIL_PORT=587
MAIL_USERNAME=<smtp_username>
MAIL_PASSWORD=<smtp_password>
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=no-reply@staging.example.com
MAIL_FROM_NAME="Verified Shortlet Staging"

PLATFORM_ADMIN_EMAIL=<private_super_admin_email>
PLATFORM_ADMIN_PASSWORD=<unique_staging_admin_password>
```

Never commit `.env`. Use a unique staging super-admin password. The specialised presentation administrators and business personas are demonstration accounts, so protect the staging subdomain with cPanel directory privacy/HTTP authentication or an equivalent access restriction and remove the demo accounts before a public production launch.

### Later deployments

Back up the database and uploads first, then run:

```bash
cd /home/CPANEL_USER/repositories/project-nexa-26
php artisan down --retry=60
git fetch origin
git checkout dev
git pull --ff-only origin dev
composer install --no-dev --prefer-dist --optimize-autoloader
npm ci
npm run build
php artisan migrate --force
php artisan db:seed --force
php artisan optimize
php artisan queue:restart
php artisan up
```

Omit `php artisan db:seed --force` from later deployments when you want to preserve changes made to the named presentation fixtures, especially the Eko Pearl review/publication state. Migrations do not require reseeding on every deployment.

### Seeded staging personas

All business staff use `DemoPassword123!`. The standard presentation records are:

| Persona | Email | Password |
|---|---|---|
| Guest | `guest.demo@verifiedshortlet.test` | `DemoPassword123!` |
| Coastline owner | `owner@coastlineresidences.test` | `Password123!` |
| Lagoon owner | `owner@lagoonstays.test` | `Password123!` |
| Property manager | `manager.demo@verifiedshortlet.test` | `DemoPassword123!` |
| Reception | `reception.demo@verifiedshortlet.test` | `DemoPassword123!` |
| Accountant | `accountant.demo@verifiedshortlet.test` | `DemoPassword123!` |
| Operations manager | `operations.demo@verifiedshortlet.test` | `DemoPassword123!` |
| Cleaner | `cleaner.demo@verifiedshortlet.test` | `DemoPassword123!` |
| Maintenance technician | `maintenance.demo@verifiedshortlet.test` | `DemoPassword123!` |
| Inspector | `inspector.demo@verifiedshortlet.test` | `DemoPassword123!` |
| Customer support | `support.demo@verifiedshortlet.test` | `DemoPassword123!` |
| Verification administrator | `verification.admin@verifiedshortlet.test` | `DemoPassword123!` |
| Disputes administrator | `disputes.admin@verifiedshortlet.test` | `DemoPassword123!` |

The super-administrator uses the `PLATFORM_ADMIN_EMAIL` and `PLATFORM_ADMIN_PASSWORD` configured in staging. Business personas log in at `/login`; platform administrators log in at `/admin/login`.

### Seed verification

Run these read-only checks after seeding:

```bash
php artisan migrate:status
php artisan about
php artisan schedule:list
php artisan tinker --execute="dump([\App\Models\Business::count(), \App\Models\Property::count(), \App\Models\User::count()]);"
```

Then verify in isolated browser sessions:

1. `/` displays seeded marketplace properties and their images.
2. `/login` accepts the owner, cleaner, accountant and guest accounts.
3. `/admin/login` accepts the configured super-admin and specialised demo admins.
4. `/owner/team` shows the seeded team and Chevron property scopes.
5. The cleaner enters `/staff/tasks` and receives `403` from `/owner/finance` and `/owner/bookings`.
6. The accountant opens `/owner/finance` and receives `403` from `/owner/calendar`.
7. `/admin/properties` contains the pending Eko Pearl presentation record.
8. Uploaded/public property media loads over HTTPS with no mixed-content or missing-resource errors.
9. One controlled external `.ics` URL imports, blocks its full date range and is rejected by a conflicting marketplace/manual booking.

Use the separate client presentation browser runbook for the full persona-by-persona acceptance journey.

## Required environment

Set secrets in the deployment environment, never in source control.

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-production-domain.example
APP_KEY=<generated-secret>

DB_CONNECTION=mysql
DB_HOST=<private-database-host>
DB_PORT=3306
DB_DATABASE=<database-name>
DB_USERNAME=<least-privilege-user>
DB_PASSWORD=<secret>

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
FILESYSTEM_DISK=local

MAIL_MAILER=smtp
MAIL_HOST=<smtp-host>
MAIL_PORT=587
MAIL_USERNAME=<smtp-user>
MAIL_PASSWORD=<secret>
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=no-reply@your-production-domain.example
MAIL_FROM_NAME="Verified Shortlet"

PLATFORM_ADMIN_EMAIL=<initial-admin-email>
PLATFORM_ADMIN_PASSWORD=<one-time-strong-secret>
```

The web user must be able to write `storage/` and `bootstrap/cache/`. The private disk (`storage/app/private`) must not be web-accessible. Only `public/storage` should link to `storage/app/public`.

## Deploy

1. Put the application into maintenance mode: `php artisan down --retry=60`.
2. Back up the database and private uploads before changing code.
3. Install locked production dependencies: `composer install --no-dev --prefer-dist --optimize-autoloader` and `npm ci`.
4. Build assets: `npm run build`.
5. Inspect migrations: `php artisan migrate --pretend`.
6. Apply forward migrations: `php artisan migrate --force`.
7. Move any legacy public property documents: `php artisan documents:migrate-private --dry-run`, then `php artisan documents:migrate-private`.
8. Create the public-media link if absent: `php artisan storage:link`.
9. Cache production configuration: `php artisan optimize`.
10. Restart queue workers: `php artisan queue:restart`.
11. Return service: `php artisan up`.
12. Smoke-test marketplace search, an owner login, notifications, one manual calendar block, and an iCal sync.

## Scheduler

Install one cron entry under the same OS user as the application:

```cron
* * * * * cd /absolute/path/to/project-nexa-26 && php artisan schedule:run >> /dev/null 2>&1
```

Verify with `php artisan schedule:list`. The schedule queues iCal imports every 15 minutes, checks stale calendar connections every 15 minutes, and escalates overdue operational work every five minutes.

## Queue worker

Run a supervised worker; do not rely on a terminal session:

```ini
[program:project-nexa-worker]
command=php /absolute/path/to/project-nexa-26/artisan queue:work database --sleep=3 --tries=3 --backoff=60 --timeout=120 --max-time=3600
directory=/absolute/path/to/project-nexa-26
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
redirect_stderr=true
stdout_logfile=/absolute/path/to/project-nexa-26/storage/logs/worker.log
```

After installation, start it with the process supervisor and verify that `php artisan queue:monitor database:default --max=100` succeeds. Review `php artisan queue:failed` after each deployment. Retry only after correcting the cause with `php artisan queue:retry <id>`.

Email failures update `notification_deliveries` but do not remove the in-app notification. This is intentional.

## Database backup and restore rehearsal

Create an encrypted, access-restricted backup before deployment:

```bash
mysqldump --single-transaction --routines --triggers --set-gtid-purged=OFF -h DB_HOST -u DB_USERNAME -p DB_DATABASE > project-nexa-before-deploy.sql
sha256sum project-nexa-before-deploy.sql > project-nexa-before-deploy.sql.sha256
```

Rehearse restoration into a separate, explicitly named non-production database:

```bash
mysql -h DB_HOST -u DB_USERNAME -p -e "CREATE DATABASE project_nexa_restore_rehearsal"
mysql -h DB_HOST -u DB_USERNAME -p project_nexa_restore_rehearsal < project-nexa-before-deploy.sql
```

Point a temporary application instance at the restored database and run `php artisan migrate:status`, record counts for businesses/properties/bookings/payments, and exercise one read-only booking and document-access check. Drop the rehearsal database only after the results are recorded and the target name is re-verified.

## Rollback and recovery

Prefer a forward fix. Do not run a broad production `migrate:rollback` when a migration has transformed live data.

If the release fails before migrations, restore the previous code artifact, run `php artisan optimize:clear`, rebuild/cache the prior release, restart workers, and bring the application up.

If it fails after compatible additive migrations, leave the schema in place and deploy the previous application version only when it remains schema-compatible. Otherwise keep maintenance mode enabled, restore the pre-deploy database and matching private-upload snapshot to a new database/storage location, switch configuration atomically, clear caches, restart workers, and then bring service up.

## Post-release checks

- `php artisan about` reports production mode and the expected database/queue/mail drivers.
- `php artisan schedule:list` includes calendar sync, stale sync alerts, and overdue task alerts.
- The worker consumes a test notification email delivery.
- A known imported iCal night cannot be reserved by marketplace or manual booking.
- Property verification documents return 403/404 to unauthenticated, guest, and foreign-owner requests.
- Logs contain no new repeated exceptions; failed jobs and stale calendar alerts are empty or understood.
