<?php

namespace Database\Seeders;

use App\Models\Amenity;
use App\Models\Booking;
use App\Models\BookingDispute;
use App\Models\Business;
use App\Models\BusinessMembership;
use App\Models\Employee;
use App\Models\Property;
use App\Models\PropertyAmenity;
use App\Models\PropertyMarketplaceListing;
use App\Models\PropertyMedia;
use App\Models\PropertyStaffAssignment;
use App\Models\Review;
use App\Models\ReviewResponse;
use App\Models\Role;
use App\Models\User;
use App\Models\UserBusinessContext;
use App\Models\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PresentationDemoSeeder extends Seeder
{
    public const PASSWORD = 'DemoPassword123!';

    public function run(): void
    {
        $coastline = Business::query()->where('email', 'owner@coastlineresidences.test')->first();
        $lagoon = Business::query()->where('email', 'owner@lagoonstays.test')->first();
        if (! $coastline || ! $lagoon) {
            throw new RuntimeException('Run ServicedApartmentMarketplaceSeeder before PresentationDemoSeeder.');
        }

        $chevron = $this->property($coastline, 'CSR-003');
        $admiralty = $this->property($lagoon, 'LAG-001');

        DB::transaction(function () use ($coastline, $lagoon, $chevron, $admiralty): void {
            $this->reviewProperty($coastline);
            $this->guest();
            $verificationAdmin = $this->platformAdministrator('Nneka Verification Admin', 'verification.admin@verifiedshortlet.test', 'platform_verification_admin');
            $supportAdmin = $this->platformAdministrator('Tobi Support Admin', 'disputes.admin@verifiedshortlet.test', 'platform_support_admin');
            $this->moderationAndDisputeCases($coastline, $chevron, $verificationAdmin, $supportAdmin);

            $profiles = [
                ['Amina Property Manager', 'manager.demo@verifiedshortlet.test', 'property_manager', [$chevron]],
                ['Ruth Reception', 'reception.demo@verifiedshortlet.test', 'reception_officer', [$chevron]],
                ['Kemi Accountant', 'accountant.demo@verifiedshortlet.test', 'accountant', []],
                ['Femi Operations', 'operations.demo@verifiedshortlet.test', 'operations_manager', [$chevron]],
                ['Grace Cleaner', 'cleaner.demo@verifiedshortlet.test', 'cleaner', [$chevron]],
                ['Musa Maintenance', 'maintenance.demo@verifiedshortlet.test', 'maintenance_technician', [$chevron]],
                ['Ife Inspector', 'inspector.demo@verifiedshortlet.test', 'inspector', [$chevron]],
                ['Ada Customer Support', 'support.demo@verifiedshortlet.test', 'customer_support', [$chevron]],
            ];

            foreach ($profiles as [$name, $email, $role, $properties]) {
                $user = $this->user($name, $email);
                $membership = $this->membership($user, $coastline, $role, $properties);
                $this->activateContext($user, $membership);
            }

            // The property manager deliberately belongs to two businesses so the
            // presentation can demonstrate safe active-business switching.
            $manager = User::query()->where('email', 'manager.demo@verifiedshortlet.test')->firstOrFail();
            $this->membership($manager, $lagoon, 'property_manager', [$admiralty]);
            $this->activateContext(
                $manager,
                $coastline->memberships()->where('user_id', $manager->id)->firstOrFail()
            );
        });
    }

    private function reviewProperty(Business $business): void
    {
        $owner = User::query()->where('email', 'owner@coastlineresidences.test')->firstOrFail();
        $property = Property::query()->updateOrCreate(['code' => 'DEMO-REVIEW-001'], [
            'business_id' => $business->id,
            'name' => 'Eko Pearl Executive Residence',
            'address' => [
                'line_1' => '7 Eko Pearl Boulevard',
                'city' => 'Victoria Island',
                'state' => 'Lagos',
                'country_code' => 'NG',
            ],
            'property_type' => 'serviced_apartment',
            'booking_mode' => 'entire',
            'capacity' => 6,
            'bedrooms' => 3,
            'beds' => 4,
            'bathrooms' => 3.5,
            'floor_area_sqm' => 215,
            'description' => 'A polished three-bedroom executive residence with skyline views, reliable power, concierge access and generous spaces for business or family stays.',
            'default_nightly_price' => 185000,
            'pricing_currency' => 'NGN',
            'verification_status' => 'pending',
            'publication_status' => 'pending',
            'readiness_status' => 'ready',
            'operational_status' => 'available',
            'maintenance_status' => 'not_required',
            'information_completed_at' => now(),
            'media_completed_at' => now(),
            'verification_submitted_at' => now(),
            'verified_at' => null,
            'verified_by' => null,
            'published_at' => null,
            'published_by' => null,
            'status' => 'active',
            'created_by' => $owner->id,
            'updated_by' => $owner->id,
        ]);

        PropertyMarketplaceListing::withTrashed()->updateOrCreate(['property_id' => $property->id], [
            'business_id' => $business->id,
            'slug' => 'eko-pearl-executive-residence',
            'public_title' => 'Eko Pearl Executive Residence',
            'short_summary' => 'Executive three-bedroom serviced living in the heart of Victoria Island.',
            'public_description' => $property->description,
            'stay_categories' => ['victoria-island', 'business', 'family'],
            'check_in_time' => '14:00',
            'check_out_time' => '11:00',
            'instant_booking_enabled' => true,
            'publication_status' => 'draft',
            'is_publication_eligible' => false,
            'publication_eligibility_details' => ['submitted_for_platform_review' => true],
            'published_by' => null,
            'published_at' => null,
            'unpublished_by' => null,
            'unpublished_at' => null,
            'status' => 'active',
            'deleted_at' => null,
            'created_by' => $owner->id,
            'updated_by' => $owner->id,
        ]);

        foreach (['/apt1.jpg', '/apt2.jpg', '/apt3.jpg'] as $index => $url) {
            PropertyMedia::withTrashed()->updateOrCreate([
                'property_id' => $property->id,
                'sort_order' => $index + 1,
            ], [
                'business_id' => $business->id,
                'media_type' => 'image',
                'external_url' => $url,
                'title' => 'Eko Pearl Executive Residence '.($index + 1),
                'alt_text' => 'Eko Pearl Executive Residence interior and facilities',
                'is_primary' => $index === 0,
                'status' => 'active',
                'deleted_at' => null,
            ]);
        }

        $amenities = Amenity::query()->whereIn('code', [
            'wifi', 'air-conditioning', 'kitchen', 'power-backup', 'security', 'free-parking',
        ])->get();
        foreach ($amenities as $sort => $amenity) {
            PropertyAmenity::withTrashed()->updateOrCreate([
                'property_id' => $property->id,
                'amenity_id' => $amenity->id,
            ], [
                'business_id' => $business->id,
                'sort_order' => $sort,
                'status' => 'active',
                'deleted_at' => null,
            ]);
        }
    }

    private function guest(): void
    {
        $guest = $this->user('Zainab Demo Guest', 'guest.demo@verifiedshortlet.test');
        $role = Role::query()->where('system_key', 'guest')->firstOrFail();
        UserRole::query()->updateOrCreate([
            'user_id' => $guest->id,
            'role_id' => $role->id,
            'scope_key' => 'global',
        ], [
            'business_membership_id' => null,
            'status' => 'active',
            'assigned_at' => now(),
            'revoked_at' => null,
        ]);
    }

    private function platformAdministrator(string $name, string $email, string $roleKey): User
    {
        $administrator = $this->user($name, $email);
        $role = Role::query()->where('system_key', $roleKey)->firstOrFail();
        UserRole::query()->updateOrCreate([
            'user_id' => $administrator->id,
            'role_id' => $role->id,
            'scope_key' => 'global',
        ], [
            'business_membership_id' => null,
            'status' => 'active',
            'assigned_at' => now(),
            'revoked_at' => null,
        ]);

        return $administrator;
    }

    private function moderationAndDisputeCases(Business $business, Property $property, User $moderator, User $supportAdmin): void
    {
        $guest = User::query()->where('email', 'guest.demo@verifiedshortlet.test')->firstOrFail();
        $booking = Booking::query()->updateOrCreate([
            'business_id' => $business->id,
            'reference' => 'VS-DEMO-CASE01',
        ], [
            'property_id' => $property->id,
            'guest_user_id' => $guest->id,
            'arrival_date' => today()->subDays(12),
            'departure_date' => today()->subDays(9),
            'number_of_guests' => 2,
            'adult_count' => 2,
            'child_count' => 0,
            'source' => 'marketplace',
            'status' => 'completed',
            'payment_status' => 'paid',
            'currency' => 'NGN',
            'subtotal_amount' => 315000,
            'discount_amount' => 0,
            'total_amount' => 330750,
            'external_reference' => 'presentation-moderation-case',
            'created_by' => $guest->id,
        ]);

        $review = Review::query()->updateOrCreate([
            'booking_id' => $booking->id,
            'guest_user_id' => $guest->id,
        ], [
            'business_id' => $business->id,
            'property_id' => $property->id,
            'rating' => 2,
            'title' => 'Needs a moderation decision',
            'content' => 'The stay was completed, but this review is hidden so the client can see the moderation and restoration workflow.',
            'is_verified_stay' => true,
            'moderation_status' => 'hidden',
            'submitted_at' => now()->subDays(8),
            'published_at' => now()->subDays(8),
            'status' => 'active',
            'created_by' => $guest->id,
            'updated_by' => $moderator->id,
        ]);
        ReviewResponse::query()->updateOrCreate(['review_id' => $review->id], [
            'business_id' => $business->id,
            'responded_by' => User::query()->where('email', 'owner@coastlineresidences.test')->value('id'),
            'content' => 'We have reviewed the concerns and shared the case with the platform team.',
            'moderation_status' => 'approved',
            'submitted_at' => now()->subDays(7),
            'published_at' => now()->subDays(7),
            'status' => 'active',
        ]);

        BookingDispute::query()->updateOrCreate([
            'business_id' => $business->id,
            'reference' => 'DSP-DEMO-OPEN',
        ], [
            'property_id' => $property->id,
            'booking_id' => $booking->id,
            'dispute_type' => 'guest_complaint',
            'opened_by_type' => 'platform_admin',
            'opened_by' => $supportAdmin->id,
            'description' => 'Guest alleges the property condition did not match the published listing and requests a partial adjustment.',
            'priority' => 'high',
            'disputed_amount' => 75000,
            'currency' => 'NGN',
            'dispute_status' => 'open',
            'assigned_to' => $supportAdmin->id,
            'due_at' => now()->addDays(2),
            'resolution' => null,
            'approved_amount' => null,
            'resolved_by' => null,
            'opened_at' => now()->subDay(),
            'resolved_at' => null,
            'closed_at' => null,
            'status' => 'active',
            'created_by' => $supportAdmin->id,
            'updated_by' => $supportAdmin->id,
        ]);
    }

    private function user(string $name, string $email): User
    {
        return User::query()->updateOrCreate(['email' => $email], [
            'name' => $name,
            'password' => self::PASSWORD,
            'email_verified_at' => now(),
            'timezone' => 'Africa/Lagos',
            'status' => 'active',
            'failed_login_attempts' => 0,
            'locked_until' => null,
        ]);
    }

    /** @param array<int, Property> $properties */
    private function membership(User $user, Business $business, string $roleKey, array $properties): BusinessMembership
    {
        $role = Role::query()->where('system_key', $roleKey)->where('status', 'active')->firstOrFail();
        $membership = BusinessMembership::query()->updateOrCreate([
            'business_id' => $business->id,
            'user_id' => $user->id,
        ], [
            'job_title' => $role->name,
            'status' => 'active',
            'invited_at' => now(),
            'joined_at' => now(),
            'ended_at' => null,
        ]);

        $membership->roles()->where('status', 'active')->where('role_id', '!=', $role->id)
            ->update(['status' => 'revoked', 'revoked_at' => now()]);
        UserRole::query()->updateOrCreate([
            'user_id' => $user->id,
            'role_id' => $role->id,
            'scope_key' => $membership->id,
        ], [
            'business_membership_id' => $membership->id,
            'status' => 'active',
            'assigned_at' => now(),
            'revoked_at' => null,
        ]);

        $employee = Employee::query()->updateOrCreate([
            'business_membership_id' => $membership->id,
        ], [
            'business_id' => $business->id,
            'employee_code' => 'DEMO-'.str($roleKey)->upper()->replace('_', '-')->limit(24, ''),
            'employment_status' => 'active',
            'started_on' => today(),
            'ended_on' => null,
            'status' => 'active',
        ]);
        $employee->propertyAssignments()->where('assignment_status', 'active')
            ->update(['assignment_status' => 'inactive', 'status' => 'inactive']);

        foreach ($properties as $property) {
            PropertyStaffAssignment::withTrashed()->updateOrCreate([
                'business_id' => $business->id,
                'property_id' => $property->id,
                'employee_id' => $employee->id,
                'assignment_role' => $this->assignmentRole($roleKey),
            ], [
                'assignment_status' => 'active',
                'status' => 'active',
                'starts_on' => today(),
                'ends_on' => null,
                'deleted_at' => null,
            ]);
        }

        return $membership;
    }

    private function activateContext(User $user, BusinessMembership $membership): void
    {
        $assignment = $membership->roles()->where('status', 'active')->whereNull('revoked_at')->firstOrFail();
        UserBusinessContext::query()->updateOrCreate(['user_id' => $user->id], [
            'business_id' => $membership->business_id,
            'business_membership_id' => $membership->id,
            'active_user_role_id' => $assignment->id,
            'switched_at' => now(),
            'status' => 'active',
        ]);
    }

    private function property(Business $business, string $code): Property
    {
        return $business->properties()->where('code', $code)->firstOrFail();
    }

    private function assignmentRole(string $roleKey): string
    {
        return match ($roleKey) {
            'property_manager', 'operations_manager' => 'property_manager',
            'cleaner' => 'cleaner',
            'maintenance_technician' => 'maintenance_technician',
            'inspector' => 'inspector',
            'customer_support' => 'guest_support',
            'accountant' => 'accountant',
            default => 'other',
        };
    }
}
