# Property Amenities Wizard Design

## Goal

Turn property setup into a property-bound wizard whose first persisted transition is Basics to Amenities.

## Flow

Creating or updating Basics marks that stage complete and redirects to `/owner/properties/{property}/setup/amenities`. The generic `/setup` route resumes at Basics when basics are incomplete and Amenities otherwise. Amenities are loaded from the shared catalog, preselect the property's saved assignments, and persist through the `property_amenities` table.

## Security and lifecycle

Every setup route resolves only draft properties through the active business relationship. Amenity IDs are validated against active catalog entries. Saving uses a transaction, restores previously removed assignments when reselected, and soft-deletes deselected assignments so history remains recoverable.

## Scope

Only Basics and Amenities are persistent in this iteration. Amenities saves successfully and advances to the property-bound Media URL, which clearly identifies itself as the next implementation stage and does not pretend to persist uploads yet.
