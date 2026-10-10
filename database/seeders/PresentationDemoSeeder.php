<?php

namespace Database\Seeders;

use App\Models\Amenity;
use App\Models\Booking;
use App\Models\BookingDispute;
use App\Models\BookingInteraction;
use App\Models\Business;
use App\Models\BusinessMembership;
use App\Models\Employee;
use App\Models\Notification;
use App\Models\OperationalTask;
use App\Models\OperationalTaskAssignment;
use App\Models\OperationalTaskChecklistItem;
use App\Models\Property;
use App\Models\Payment;
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
use App\Services\Calendar\ManageExternalCalendarConnection;
use App\Enums\OperationalTaskStatus;
use App\Enums\OperationalTaskType;
use App\Enums\TaskGenerationSource;
use App\Enums\TaskPriority;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PresentationDemoSeeder extends Seeder
{
    public const PASSWORD = 'DemoPassword123!';

    public function run(): void
    {
        $coastline = Business::query()->where('email', 'owner@coastlineresidences.test')->first();
        if (! $coastline) {
            throw new RuntimeException('Run ServicedApartmentMarketplaceSeeder before PresentationDemoSeeder.');
        }

        $chevron = $this->property($coastline, 'CSR-003');
        $admiralty = $this->property($coastline, 'LAG-001');

        DB::transaction(function () use ($coastline, $chevron, $admiralty): void {
            app(ManageExternalCalendarConnection::class)->ensureExport($chevron);
            app(ManageExternalCalendarConnection::class)->ensureExport($admiralty);
            $this->reviewProperty($coastline);
            $guest = $this->guest();
            $this->guestBookingPortfolio($guest, $chevron, $admiralty);
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

            $this->roleSpecificDemoRecords($coastline, $chevron);

        });
    }

    private function roleSpecificDemoRecords(Business $business, Property $property): void
    {
        $owner = User::query()->where('email', 'owner@coastlineresidences.test')->firstOrFail();
        $guest = User::query()->where('email', 'guest.demo@verifiedshortlet.test')->firstOrFail();

        $employees = [
            'cleaner' => $this->employee('cleaner.demo@verifiedshortlet.test', $business),
            'maintenance' => $this->employee('maintenance.demo@verifiedshortlet.test', $business),
            'inspector' => $this->employee('inspector.demo@verifiedshortlet.test', $business),
            'operations' => $this->employee('operations.demo@verifiedshortlet.test', $business),
            'reception' => $this->employee('reception.demo@verifiedshortlet.test', $business),
            'support' => $this->employee('support.demo@verifiedshortlet.test', $business),
        ];

        $upcoming = Booking::query()
            ->where('business_id', $business->id)
            ->where('reference', 'VS-DEMO-UPCOMING-01')
            ->firstOrFail();

        $checkedIn = Booking::query()
            ->where('business_id', $business->id)
            ->where('reference', 'VS-DEMO-INSTAY-01')
            ->first();

        $case = Booking::query()
            ->where('business_id', $business->id)
            ->where('reference', 'VS-DEMO-CASE01')
            ->firstOrFail();

        $this->task($business, $property, $upcoming, $employees['cleaner'], $owner, [
            'reference' => 'TASK-DEMO-CLEAN-CHECKOUT',
            'title' => 'Turnover clean after Zainab checkout',
            'task_type' => OperationalTaskType::Cleaning->value,
            'priority' => TaskPriority::High->value,
            'status' => OperationalTaskStatus::Assigned->value,
            'due_at' => today()->addDays(14)->setTime(12, 0),
            'notes' => 'Prepare bedrooms, bathrooms and kitchen for same-day availability.',
            'checklist' => [
                'Strip linen and send to laundry',
                'Sanitise bathrooms and guest-touch surfaces',
                'Restock towels, toiletries and bottled water',
                'Upload final room photos before handoff',
            ],
        ]);

        $this->task($business, $property, $upcoming, $employees['maintenance'], $owner, [
            'reference' => 'TASK-DEMO-MAINT-AC',
            'title' => 'Inspect master-bedroom AC before arrival',
            'task_type' => OperationalTaskType::Maintenance->value,
            'priority' => TaskPriority::Normal->value,
            'status' => OperationalTaskStatus::Assigned->value,
            'due_at' => today()->addDays(9)->setTime(15, 0),
            'notes' => 'Guest requested reliable cooling. Check filters, thermostat and generator switchover.',
            'checklist' => [
                'Clean AC filter',
                'Run cooling test for 20 minutes',
                'Confirm generator switchover',
            ],
        ]);

        $this->task($business, $property, $case, $employees['inspector'], $owner, [
            'reference' => 'TASK-DEMO-INSPECT-CASE',
            'title' => 'Post-stay inspection for moderation case',
            'task_type' => OperationalTaskType::Inspection->value,
            'priority' => TaskPriority::Urgent->value,
            'status' => OperationalTaskStatus::InProgress->value,
            'due_at' => today()->setTime(16, 0),
            'notes' => 'Capture condition evidence for platform review and dispute resolution.',
            'checklist' => [
                'Photograph living room, kitchen and bathrooms',
                'Confirm reported issue against listing standard',
                'Submit inspection outcome for owner review',
            ],
        ]);

        $this->task($business, $property, $upcoming, $employees['reception'], $owner, [
            'reference' => 'TASK-DEMO-RECEPTION-WELCOME',
            'title' => 'Send arrival note and gate instructions',
            'task_type' => OperationalTaskType::GuestWelcome->value,
            'priority' => TaskPriority::Normal->value,
            'status' => OperationalTaskStatus::Pending->value,
            'due_at' => today()->addDays(9)->setTime(10, 0),
            'notes' => 'Confirm ETA, guest phone number and access instructions.',
            'checklist' => [
                'Confirm guest arrival time',
                'Share estate gate code and check-in contact',
                'Mark pre-arrival confirmation complete',
            ],
        ]);

        if ($checkedIn) {
            $this->task($business, $checkedIn->property, $checkedIn, $employees['support'], $owner, [
                'reference' => 'TASK-DEMO-SUPPORT-INSTAY',
                'title' => 'Resolve in-stay Wi-Fi support request',
                'task_type' => OperationalTaskType::Repair->value,
                'priority' => TaskPriority::High->value,
                'status' => OperationalTaskStatus::Assigned->value,
                'due_at' => today()->setTime(18, 0),
                'notes' => 'Guest currently checked in asked for router restart and backup network details.',
                'checklist' => [
                    'Call guest to acknowledge request',
                    'Restart router or share backup network',
                    'Record resolution note on booking',
                ],
            ]);
        }

        $this->bookingMessage($business, $upcoming, $guest, $owner, 'guest-arrival-question', 'Hello, can we check in around 1pm if the apartment is ready?', 'Guest asked about early check-in.', 'inbound', now()->subHours(4));
        $this->bookingMessage($business, $upcoming, $owner, $guest, 'owner-arrival-reply', 'Thanks Zainab. We will confirm after cleaning, but 2pm remains the guaranteed check-in time.', 'Owner replied with check-in guidance.', 'outbound', now()->subHours(3));
        $this->bookingMessage($business, $case, $guest, $owner, 'guest-case-followup', 'Please confirm when the review case is resolved. I added photos in the thread.', 'Guest followed up on review case.', 'inbound', now()->subDay());

        $this->notification($business, $owner, 'test_payment_received', 'Payment received', 'A ₦420,000 test payment is attached to VS-DEMO-UPCOMING-01.', ['url' => route('owner.finance')]);
        $this->notification($business, User::query()->where('email', 'accountant.demo@verifiedshortlet.test')->firstOrFail(), 'demo_finance_queue', 'Finance queue ready', 'Review paid, unpaid and refunded demo bookings in the Finance workspace.', ['url' => route('owner.finance')]);
        $this->notification($business, User::query()->where('email', 'cleaner.demo@verifiedshortlet.test')->firstOrFail(), 'demo_cleaning_task', 'Cleaning task assigned', 'Turnover clean for Chevron Family Residence is ready on your staff task board.', ['url' => route('staff.tasks.index')]);
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
            'is_test' => true,
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

    private function guest(): User
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

        return $guest;
    }

    private function guestBookingPortfolio(User $guest, Property $chevron, Property $admiralty): void
    {
        $secondaryCoastline = Property::query()
            ->where('business_id', $chevron->business_id)
            ->whereKeyNot($chevron->id)
            ->where('publication_status', 'published')
            ->first() ?? $chevron;

        $records = [
            ['VS-DEMO-UPCOMING-01', $chevron, today()->addDays(10), today()->addDays(14), 'confirmed', 'paid', 4, 420000, 'Upcoming confirmed stay'],
            ['VS-DEMO-PENDING-01', $admiralty, today()->addDays(22), today()->addDays(27), 'awaiting_payment', 'unpaid', 2, 475000, 'Upcoming stay awaiting payment'],
            ['VS-DEMO-INSTAY-01', $secondaryCoastline, today()->subDay(), today()->addDays(2), 'checked_in', 'paid', 2, 270000, 'Guest currently checked in'],
            ['VS-DEMO-CANCELLED-01', $admiralty, today()->subDays(35), today()->subDays(31), 'cancelled', 'refunded', 3, 380000, 'Cancelled demonstration stay'],
        ];

        foreach ($records as [$reference, $property, $arrival, $departure, $status, $paymentStatus, $guests, $total, $note]) {
            Booking::query()->updateOrCreate([
                'business_id' => $property->business_id,
                'reference' => $reference,
            ], [
                'property_id' => $property->id,
                'guest_user_id' => $guest->id,
                'arrival_date' => $arrival,
                'departure_date' => $departure,
                'number_of_guests' => $guests,
                'adult_count' => $guests,
                'child_count' => 0,
                'source' => 'marketplace',
                'status' => $status,
                'payment_status' => $paymentStatus,
                'special_requests' => $note,
                'currency' => 'NGN',
                'subtotal_amount' => $total,
                'discount_amount' => 0,
                'total_amount' => $total,
                'external_reference' => (string) str($reference)->lower(),
                'created_by' => $guest->id,
            ]);

            $booking = Booking::query()
                ->where('business_id', $property->business_id)
                ->where('reference', $reference)
                ->firstOrFail();

            if (in_array($paymentStatus, ['paid', 'refunded'], true)) {
                $charge = Payment::query()->updateOrCreate([
                    'business_id' => $booking->business_id,
                    'reference' => $reference.'-PAY',
                ], [
                    'booking_id' => $booking->id,
                    'purpose' => 'balance',
                    'amount' => $total,
                    'currency' => 'NGN',
                    'method' => 'card',
                    'provider' => 'paystack',
                    'provider_reference' => $reference.'-TEST',
                    'status' => 'completed',
                    'transaction_at' => now(),
                    'verified_at' => now(),
                    'notes' => 'Test-mode presentation payment.',
                    'created_by' => $guest->id,
                ]);

                if ($paymentStatus === 'refunded') {
                    Payment::query()->updateOrCreate([
                        'business_id' => $booking->business_id,
                        'reference' => $reference.'-REFUND',
                    ], [
                        'booking_id' => $booking->id,
                        'original_payment_id' => $charge->id,
                        'purpose' => 'refund',
                        'amount' => $total,
                        'currency' => 'NGN',
                        'method' => 'card',
                        'provider' => 'paystack',
                        'provider_reference' => $reference.'-TEST-REFUND',
                        'status' => 'completed',
                        'transaction_at' => now(),
                        'verified_at' => now(),
                        'notes' => 'Test-mode presentation refund.',
                        'created_by' => $guest->id,
                    ]);
                }
            }
        }
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

    /** @param array{reference:string,title:string,task_type:string,priority:string,status:string,due_at:\Illuminate\Support\Carbon,notes:string,checklist:array<int,string>} $data */
    private function task(Business $business, Property $property, ?Booking $booking, Employee $employee, User $owner, array $data): OperationalTask
    {
        $task = OperationalTask::query()->updateOrCreate([
            'business_id' => $business->id,
            'reference' => $data['reference'],
        ], [
            'property_id' => $property->id,
            'booking_id' => $booking?->id,
            'assigned_employee_id' => $employee->id,
            'title' => $data['title'],
            'task_type' => $data['task_type'],
            'priority' => $data['priority'],
            'status' => $data['status'],
            'due_at' => $data['due_at'],
            'started_at' => $data['status'] === OperationalTaskStatus::InProgress->value ? now()->subHour() : null,
            'completed_at' => null,
            'notes' => $data['notes'],
            'generation_source' => TaskGenerationSource::Manual->value,
            'is_recurring' => false,
            'generation_metadata' => ['presentation_demo' => true],
            'created_by' => $owner->id,
            'updated_by' => $owner->id,
        ]);

        OperationalTaskAssignment::query()->updateOrCreate([
            'business_id' => $business->id,
            'operational_task_id' => $task->id,
            'employee_id' => $employee->id,
        ], [
            'assignment_role' => $employee->propertyAssignments()->where('assignment_status', 'active')->value('assignment_role') ?? 'other',
            'assignment_status' => 'accepted',
            'assigned_by' => $owner->id,
            'assigned_at' => now()->subHours(2),
            'accepted_at' => now()->subHour(),
            'status' => 'active',
        ]);

        foreach ($data['checklist'] as $index => $title) {
            OperationalTaskChecklistItem::query()->updateOrCreate([
                'business_id' => $business->id,
                'operational_task_id' => $task->id,
                'sort_order' => $index + 1,
            ], [
                'title' => $title,
                'instructions' => null,
                'is_required' => true,
                'is_completed' => false,
                'completed_by' => null,
                'completed_at' => null,
                'notes' => null,
                'status' => 'active',
            ]);
        }

        return $task;
    }

    private function bookingMessage(Business $business, Booking $booking, User $sender, User $recipient, string $key, string $content, string $summary, string $direction, mixed $occurredAt): void
    {
        BookingInteraction::query()->updateOrCreate([
            'business_id' => $business->id,
            'booking_id' => $booking->id,
            'channel' => 'platform',
            'external_message_id' => 'presentation-'.$booking->reference.'-'.$key,
        ], [
            'user_id' => $sender->id,
            'recipient_user_id' => $recipient->id,
            'interaction_type' => 'message',
            'direction' => $direction,
            'recipient_name' => $recipient->name,
            'recipient_address' => $recipient->email,
            'summary' => $summary,
            'content' => $content,
            'is_internal' => false,
            'external_thread_id' => 'presentation-thread-'.$booking->reference,
            'delivery_status' => 'sent',
            'sent_at' => $occurredAt,
            'delivered_at' => $occurredAt,
            'metadata' => ['presentation_demo' => true],
            'occurred_at' => $occurredAt,
            'status' => 'active',
        ]);
    }

    private function notification(Business $business, User $user, string $type, string $title, string $message, array $data): void
    {
        Notification::query()->updateOrCreate([
            'business_id' => $business->id,
            'user_id' => $user->id,
            'type' => $type,
        ], [
            'title' => $title,
            'message' => $message,
            'data' => $data + ['presentation_demo' => true],
            'read_at' => null,
            'status' => 'active',
        ]);
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

        Payment::query()->updateOrCreate([
            'business_id' => $booking->business_id,
            'reference' => 'VS-DEMO-CASE01-PAY',
        ], [
            'booking_id' => $booking->id,
            'purpose' => 'balance',
            'amount' => $booking->total_amount,
            'currency' => 'NGN',
            'method' => 'card',
            'provider' => 'paystack',
            'provider_reference' => 'VS-DEMO-CASE01-TEST',
            'status' => 'completed',
            'transaction_at' => now()->subDays(13),
            'verified_at' => now()->subDays(13),
            'notes' => 'Test-mode completed-stay presentation payment.',
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

    private function employee(string $email, Business $business): Employee
    {
        return Employee::query()
            ->where('business_id', $business->id)
            ->whereHas('businessMembership.user', fn ($query) => $query->where('email', $email))
            ->firstOrFail();
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
