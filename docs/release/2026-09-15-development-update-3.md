# VERIFIED SHORTLET — DEVELOPMENT UPDATE 3

Good evening team,

Following Development Update 2, we have completed the staging deployment and expanded several areas of the MVP, particularly platform administration, role-based business access, property-owner presentation, calendar visibility and deployment operations.

## Confirmation of Development Update 2

The functionality reported in Development Update 2 remains implemented across all 13 areas:

1. Property onboarding improvements.
2. Platform property review.
3. Marketplace experience improvements.
4. Booking confirmation and receipts.
5. iCal and channel-calendar synchronisation.
6. Complete booking lifecycle.
7. Cleaning and property operations.
8. Guest support during a stay.
9. Verified guest reviews and owner responses.
10. Finance and basic reporting.
11. Roles, permissions and team management.
12. Notifications and operational alerts.
13. Dashboard and workspace improvements.

These areas are backed by automated tests and are included in the systematic browser-presentation runbook. The remaining work is live acceptance of external services and the full staging browser rehearsal—not redevelopment of the underlying flows.

## 1. Expanded platform administration

Since the previous update, the platform-administrator workspace has grown beyond property publishing:

✅ Platform dashboard with actionable verification, moderation, dispute, restricted-user and operational-health indicators  
✅ Business directory using existing business, property, booking and team records  
✅ Business verification, rejection, suspension and reactivation workflows  
✅ Business suspension removes affected supply from new marketplace discovery and booking while preserving records  
✅ User and security directory  
✅ Account lock and unlock controls with session invalidation and administrator safety restrictions  
✅ Review and owner-response moderation  
✅ Reviews can be hidden and restored without deleting the original content or history  
✅ Platform dispute creation, assignment, priority, status transition and resolution records  
✅ Disputes retain their linked booking, guest, property, business and financial context  
✅ Controlled platform-configuration catalogue  
✅ Platform-wide audit workspace for administrative decisions  
✅ Specialised platform roles for Verification & Moderation and Support & Disputes  
✅ Platform navigation and direct routes are restricted by each administrator's permissions  

This means the platform workspace is no longer only a property-publishing console. It now covers the core MVP trust, safety, moderation, security and escalation functions.

## 2. Business team and module-level permissions

The `/owner/team` workspace now represents two independent access boundaries:

✅ Role permissions control which modules and actions a team member may use  
✅ Property assignments restrict which property records are visible inside permitted modules  
✅ Business owners can invite, assign roles, define property scope, update access and deactivate memberships  
✅ Property managers can manage assigned property operations without receiving the Finance workspace  
✅ Reception can manage reservations, manual bookings, date changes and check-in/out without receiving Finance or the management Operations board  
✅ Accountants receive the Finance workspace and booking-payment context without unrelated operational control  
✅ Operations managers can coordinate property tasks without receiving financial administration  
✅ Customer Support can manage permitted booking and guest-service records without financial access  
✅ Cleaners, maintenance technicians and inspectors use a dedicated assigned-task workspace  
✅ Task-only staff cannot access the owner Finance or Bookings workspaces  
✅ Direct unauthorised URLs return `403 Forbidden`; access is not secured only by hiding navigation  
✅ Property-scoped staff cannot view or mutate another property  
✅ Multi-business team members can switch active businesses safely  

For cleaners specifically, the owner controls the employee's role, property assignment and work allocation. The cleaner sees only assigned task context, checklist, deadlines, notes and evidence controls—not the business's bookings, finances or wider administration.

## 3. Property-owner workspace improvements

The live owner workspace has been brought closer to the accepted property-owner visual direction while retaining real database behaviour:

✅ Property lists display representative property images with fallbacks  
✅ Property portfolio supports search and list/grid presentation  
✅ Location summary cards display live property counts, assigned managers and occupancy information  
✅ Property details now combine media, amenities, inventory, documents, pricing, status explanations and calendar-sync information  
✅ Owner dashboard property cards display real property images  
✅ Published, pending, rejected, setup and operational states use clearer labels and colours  
✅ Notification and calendar pages use the lighter owner-workspace visual language instead of the unnecessary dark presentation block  
✅ Availability-block forms have clearer date, property and operational-reason presentation  

## 4. Calendar and availability presentation

✅ Owner calendar ranges now visually span the complete occupied period instead of appearing as disconnected dates  
✅ Confirmed, checked-in and checked-out stays remain connected to active availability  
✅ Completed historical stays remain visible as historical calendar records without blocking future availability  
✅ Owner-created availability blocks span their complete start-to-available-again range  
✅ Calendar filters and navigation retain live property and booking context  
✅ Public guests can browse availability up to 18 months ahead  
✅ Marketplace, manual booking, owner blocks and imported iCal events use the same conflict checks  

## 5. Client demonstration and seeded personas

✅ Repeatable presentation seeding creates two operators, marketplace properties and all required personas  
✅ Seeded guest, owner, property manager, reception, accountant, operations manager, cleaner, maintenance, inspector and support accounts are available  
✅ Seeded specialised platform-administrator accounts are available  
✅ A pending Eko Pearl property is prepared for the platform-review demonstration  
✅ A review-moderation case and an open dispute case are prepared for platform administration  
✅ Reseeding restores documented demo credentials and presentation fixtures without duplicating the named marketplace properties  
✅ A comprehensive browser runbook now maps every persona, URL, action, expected result, access boundary and recovery step  

## 6. Staging deployment completed

✅ The application is deployed over HTTPS at `https://shortlet.emiplug.com`  
✅ The domain document root is restricted to Laravel's `public/` directory  
✅ Direct web access to `.env`, `composer.json` and application logs returns `403 Forbidden`  
✅ Production Vite assets are compiled locally and deployed with the Git release because the shared host does not provide Node.js  
✅ The public homepage, guest login, admin login, property page, CSS and JavaScript assets return successful HTTP responses  
✅ MySQL/MariaDB deployments now explicitly use InnoDB for compatible index support  
✅ Database migrations and repeatable presentation seeders are deployed  
✅ Public storage is linked  
✅ Laravel caches are built for the hosted environment  
✅ Shared-hosting operation uses synchronous application jobs instead of an unsupported permanent queue worker  
✅ The missing standard queue infrastructure migration has been added for future database-queue operation  

## 7. Live scheduler verified

The Namecheap cPanel cron now invokes Laravel's scheduler every five minutes. Hosted logs confirm successful execution of:

✅ `calendars:sync` every 15 minutes  
✅ `calendars:notify-stale` every 15 minutes  
✅ `operations:notify-overdue` every five minutes  
✅ `bookings:notify-upcoming-arrivals` hourly  

The staging environment uses `QUEUE_CONNECTION=sync`, which is appropriate for the current shared-hosting demonstration. A persistent supervised queue worker remains the preferred production approach when the application moves to infrastructure that supports background processes.

## 8. Verification evidence

✅ Core SQLite suite previously passed with 166 tests and 975 assertions  
✅ MySQL suite previously passed with 165 tests and 966 assertions  
✅ Dedicated concurrent double-booking proof passed: exactly one overlapping request confirmed and the other was rejected  
✅ The latest focused defence suite passed 75 tests with 474 assertions  
✅ That focused suite covers role permissions, property scope, cleaner task isolation, platform administration, iCal, checkout protection, booking lifecycle and verified reviews  
✅ Platform-administrator cache-safe seeding regression passed 4 tests with 52 assertions  
✅ Vite production build passed and the generated manifest/assets now load from staging  
✅ Live public smoke checks passed for the homepage, login pages, Chevron property page and compiled assets  
✅ Live security smoke checks confirmed that sensitive application files are not web-accessible  

## Current MVP position

The connected MVP loop remains:

**Property setup → platform review → marketplace publication → protected booking → simulated payment → shared availability → pre-arrival work → check-in → guest support → checkout → turnover cleaning → inspection → completed stay → verified review.**

The additions since Update 2 strengthen the surrounding control environment:

**Business verification → user security → property moderation → review moderation → dispute escalation → platform configuration → audited administration.**

## Remaining acceptance activities before declaring MVP 1 ready

⏳ Complete the full multi-browser role-by-role rehearsal on staging  
⏳ Import one genuine external Airbnb or Booking.com-compatible `.ics` feed and confirm its dates cannot be booked  
⏳ Validate the Verified Shortlet outbound `.ics` URL from an external calendar platform  
⏳ Configure and test real staging SMTP delivery; current local/development mail configuration may log rather than deliver messages  
⏳ Test media/document upload, preview and access restrictions directly on staging  
⏳ Complete desktop and mobile acceptance checks and record any priority defects  
⏳ Back up the staging database/private uploads and rehearse restoration  

## Explicitly deferred beyond this simulated-payment MVP

- Real payment-provider charges, signed webhooks, settlements and payouts.
- Native mobile applications.
- Google, Outlook and native-calendar integrations beyond standards-based iCal.
- Advanced accounting, tax, reconciliation and enterprise reporting.
- Subscription billing administration.
- Audited business impersonation.
- Production-scale background workers, monitoring and high-availability infrastructure.

The system is ready for structured client acceptance testing on staging. Final MVP approval should follow the external iCal, SMTP, upload/security, browser/mobile and backup/restore checks above.
