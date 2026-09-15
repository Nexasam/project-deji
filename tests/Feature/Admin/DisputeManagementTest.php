<?php

namespace Tests\Feature\Admin;

use App\Models\Booking;
use App\Models\BookingDispute;
use App\Models\Payment;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Database\Seeders\AccessControlSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DisputeManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_support_admin_creates_a_dispute_from_an_existing_booking(): void
    {
        $this->seed(AccessControlSeeder::class);
        $admin = $this->administrator('platform_support_admin', 'case-manager@example.test');
        $booking = Booking::factory()->create(['currency' => 'NGN']);

        $this->actingAs($admin)->post(route('admin.disputes.store'), [
            'booking_id' => $booking->id,
            'dispute_type' => 'guest_complaint',
            'description' => 'Guest reported that the apartment was materially different from the listing.',
            'priority' => 'high',
            'disputed_amount' => 50000,
            'assigned_to' => $admin->id,
            'due_at' => now()->addDays(2)->format('Y-m-d\TH:i'),
            'note' => 'Case opened after reviewing the guest support conversation.',
        ])->assertRedirect();

        $dispute = BookingDispute::query()->firstOrFail();
        $this->assertSame($booking->id, $dispute->booking_id);
        $this->assertSame('high', $dispute->priority);
        $this->assertSame($admin->id, $dispute->assigned_to);
        $this->assertSame('open', $dispute->dispute_status->value);
        $this->assertDatabaseHas('audit_events', ['event_type' => 'platform.dispute.created', 'auditable_id' => $dispute->id]);
    }

    public function test_dispute_assignment_and_controlled_state_flow_are_audited_without_moving_money(): void
    {
        $this->seed(AccessControlSeeder::class);
        $admin = $this->administrator('platform_support_admin', 'lead-support@example.test');
        $assignee = $this->administrator('platform_support_admin', 'assigned-support@example.test');
        $booking = Booking::factory()->create(['currency' => 'NGN']);
        Payment::factory()->create(['business_id' => $booking->business_id, 'booking_id' => $booking->id, 'currency' => 'NGN']);
        $paymentCount = Payment::query()->count();
        $dispute = $this->dispute($booking, $admin);

        $this->actingAs($admin)->patch(route('admin.disputes.assign', $dispute), [
            'assigned_to' => $assignee->id,
            'note' => 'Assigned to the support specialist handling this operator.',
        ])->assertRedirect(route('admin.disputes.show', $dispute));
        $this->assertSame($assignee->id, $dispute->fresh()->assigned_to);

        foreach ([
            ['under_review', 'Evidence review has started with the available booking records.'],
            ['awaiting_guest', 'Guest has been asked for dated photographs and supporting records.'],
            ['under_review', 'Guest evidence received and the investigation has resumed.'],
        ] as [$status, $note]) {
            $this->actingAs($admin)->patch(route('admin.disputes.transition', $dispute), compact('status', 'note'))
                ->assertRedirect(route('admin.disputes.show', $dispute));
        }

        $this->actingAs($admin)->patch(route('admin.disputes.transition', $dispute), [
            'status' => 'resolved',
            'note' => 'Evidence supports a partial simulated compensation decision.',
            'resolution' => 'Approve a partial account-level compensation record; no payment is executed in this MVP.',
            'approved_amount' => 25000,
        ])->assertRedirect(route('admin.disputes.show', $dispute));
        $this->actingAs($admin)->patch(route('admin.disputes.transition', $dispute), [
            'status' => 'closed',
            'note' => 'Decision was communicated to both parties and the case is complete.',
        ])->assertRedirect(route('admin.disputes.show', $dispute));

        $dispute->refresh();
        $this->assertSame('closed', $dispute->dispute_status->value);
        $this->assertSame('25000.0000', $dispute->approved_amount);
        $this->assertNotNull($dispute->resolved_at);
        $this->assertNotNull($dispute->closed_at);
        $this->assertSame($paymentCount, Payment::query()->count());
        $this->assertGreaterThanOrEqual(6, $dispute->audits()->count());
    }

    public function test_invalid_transition_rolls_back_without_partial_mutation(): void
    {
        $this->seed(AccessControlSeeder::class);
        $admin = $this->administrator('platform_support_admin', 'invalid-flow@example.test');
        $dispute = $this->dispute(Booking::factory()->create(), $admin);
        $auditCount = $dispute->audits()->count();

        $this->actingAs($admin)->from(route('admin.disputes.show', $dispute))
            ->patch(route('admin.disputes.transition', $dispute), [
                'status' => 'resolved',
                'note' => 'Trying to resolve before the mandatory investigation stage.',
                'resolution' => 'This resolution must not be persisted.',
            ])->assertSessionHasErrors('status');

        $this->assertSame('open', $dispute->fresh()->dispute_status->value);
        $this->assertSame($auditCount, $dispute->audits()->count());
    }

    public function test_verification_admin_can_view_but_cannot_manage_disputes(): void
    {
        $this->seed(AccessControlSeeder::class);
        $verification = $this->administrator('platform_verification_admin', 'dispute-view@example.test');
        $booking = Booking::factory()->create();
        $dispute = $this->dispute($booking, $verification);

        $this->actingAs($verification)->get(route('admin.disputes.show', $dispute))->assertOk();
        $response = $this->actingAs($verification)->post(route('admin.disputes.store'), [
            'booking_id' => $booking->id,
            'dispute_type' => 'guest_complaint',
            'description' => 'This role must not create disputes.',
            'priority' => 'normal',
            'note' => 'This role must not create a dispute case.',
        ]);
        $this->assertSame(403, $response->getStatusCode());
    }

    private function dispute(Booking $booking, User $actor): BookingDispute
    {
        return BookingDispute::query()->create([
            'business_id' => $booking->business_id, 'property_id' => $booking->property_id,
            'booking_id' => $booking->id, 'reference' => 'DSP-'.strtoupper(fake()->unique()->bothify('######')),
            'dispute_type' => 'guest_complaint', 'opened_by_type' => 'platform_admin', 'opened_by' => $actor->id,
            'description' => 'A dispute created for transition testing.', 'priority' => 'normal',
            'currency' => $booking->currency, 'dispute_status' => 'open', 'opened_at' => now(),
            'status' => 'active', 'created_by' => $actor->id, 'updated_by' => $actor->id,
        ]);
    }

    private function administrator(string $roleKey, string $email): User
    {
        $admin = User::factory()->create(['email' => $email, 'email_verified_at' => now()]);
        UserRole::query()->create(['user_id' => $admin->id, 'role_id' => Role::query()->where('system_key', $roleKey)->firstOrFail()->id, 'status' => 'active', 'assigned_at' => now()]);

        return $admin;
    }
}
