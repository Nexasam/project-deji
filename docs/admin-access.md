# Platform Admin Access

The temporary platform administration workspace is available at:

`http://127.0.0.1:8000/admin/login`

Development/demo credentials:

- Email: `admin@verifiedshortlet.test`
- Password: `AdminPassword123!`

After login, the administrator is sent to `/admin/properties`, where submitted properties can be reviewed, published, or unpublished. Publishing through this workspace updates the property and marketplace-listing records together; do not publish by editing a single database status directly.

These are local demonstration credentials. Before deployment, set `PLATFORM_ADMIN_EMAIL` and `PLATFORM_ADMIN_PASSWORD` securely for initial seeding, change the password immediately, and never commit production credentials.
