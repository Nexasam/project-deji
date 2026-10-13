# Presentation feedback action register — 13 October 2026

This register translates the 10 October presentation feedback into testable product decisions.

## Confirmed decisions

- Bathrooms accept 0.5 increments; bedrooms remain whole numbers.
- A business receives no pre-created staff roles. The owner may adopt a platform role template or create a role from scratch.
- Platform administrators and host staff do not enter the guest workspace by default. Hidden navigation is backed by route-level authorization.
- Cleaner availability controls automatic routing. Cleaning, deep-cleaning, maintenance and inspection work can exist independently of check-in or checkout.
- Operational issues accept photo evidence when the task is reported and when it is completed.
- Host onboarding verifies NIN only. BVN is deferred to a later, separately designed finance or settlement step.
- Become a host opens the host-account registration path.
- Country selection presets currency and time zone during onboarding.
- The dummy map flow is removed. The owner enters a street address and can hide the exact public location.
- The registration modal only chooses Guest or Host; the complete, validated registration happens on one dedicated page.
- Typography, base size, accent colour, platform name and primary logo asset are controlled from Platform Configuration → Appearance.
- “Find it m” was disregarded as instructed.

## Verification focus

1. Attempt direct guest URLs as a platform administrator and as cleaner, accountant and maintenance staff; expect 403.
2. Create a new business and confirm only the owner role is active.
3. Adopt one global template, create one custom role, configure both and assign them.
4. Mark two cleaners available, create work, and confirm the least-loaded eligible cleaner is selected. Mark that cleaner unavailable and repeat.
5. Create a maintenance task with mobile camera evidence and complete it with after-work evidence.
6. Enter 2.5 bathrooms and confirm it survives save, review and marketplace display.
7. Hide an exact address and confirm the public listing shows only area/state while authorised operational views retain the address.
8. Change Appearance settings and confirm guest, owner, staff and platform pages use the same typography and accent treatment.
