# Nine-Step Property Wizard Design

The real owner flow uses one immutable property UUID across Basics, Amenities, Media, House Rules, Operations, Assets, Documents, Marketplace and Review. Every successful save advances to the next stage. Optional stages support an auditable skip action. Exit preserves previously saved work, and Continue Setup resolves the first pending step through `property_setup_steps`.

Progress is separate from publication readiness. Review evaluates live property data and blocks verification submission until Basics, a primary image, valid pricing and complete marketplace information exist. Verification submission changes the property to pending; it does not publish immediately.

