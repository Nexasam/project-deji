# Property Media Wizard Design

## Goal

Implement the real third property setup stage with secure image/video upload persistence and advance the same draft to House Rules.

## Data flow

The Media form submits multiple validated files to a property-bound endpoint. A focused service stores files on the public disk under a business/property namespace and creates `property_media` records transactionally. The first uploaded image becomes primary when no primary image exists. Removing an item soft-deletes its database record while retaining the physical file for audit recovery.

## Security

Every read, upload, and removal resolves a draft through the active business. Accepted formats are JPEG, PNG, WebP, MP4, QuickTime, and WebM, with at most ten files per request and a 50 MB limit per file.

## Navigation

Amenities advances to Media. Media advances to the property-bound House Rules handoff. Basics intro copy is condensed for both create and continuation states.

