<?php

namespace Tests\Feature\Owner;

use App\Models\Booking;
use App\Models\Business;
use App\Models\Expense;
use App\Models\FinancialTransaction;
use App\Models\Payment;
use App\Models\Property;
use App\Models\User;
use App\Services\Business\BusinessOnboardingService;
use Database\Seeders\AccessControlSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use LogicException;
use Tests\TestCase;

class FinanceWorkspaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_finance_page_uses_real_scoped_payment_and_expense_totals(): void
    {
        [$owner, $business] = $this->ownerWithBusiness('Nexa Stays');
        [, $other] = $this->ownerWithBusiness('Other Stays');
        $property = Property::factory()->for($business)->create(['name' => 'Admiralty Suite']);
        $booking = Booking::factory()->for($business)->for($property)->create(['currency' => 'NGN', 'total_amount' => 120000]);
        Payment::factory()->for($booking)->create(['business_id' => $business->id, 'amount' => 100000, 'currency' => 'NGN', 'status' => 'completed', 'purpose' => 'deposit']);
        Expense::query()->create(['business_id' => $business->id, 'property_id' => $property->id, 'reference' => 'EXP-REAL', 'category' => 'cleaning', 'amount' => 15000, 'currency' => 'NGN', 'incurred_on' => '2026-09-10', 'approval_status' => 'approved', 'description' => 'Turnover cleaning', 'status' => 'active']);
        $otherBooking = Booking::factory()->for($other)->for(Property::factory()->for($other))->create(['currency' => 'NGN']);
        Payment::factory()->for($otherBooking)->create(['business_id' => $other->id, 'amount' => 900000, 'currency' => 'NGN', 'status' => 'completed']);

        $this->actingAs($owner)->get(route('owner.finance'))->assertOk()
            ->assertSee('₦100,000')->assertSee('₦15,000')->assertSee('₦85,000')
            ->assertSee('Admiralty Suite')->assertSee('Turnover cleaning')
            ->assertDontSee('₦900,000')->assertDontSee('Lekki Waterview Suites');
    }

    public function test_owner_can_record_manual_payment_and_booking_status_is_reconciled(): void
    {
        [$owner, $business] = $this->ownerWithBusiness('Nexa Stays');
        $property = Property::factory()->for($business)->create();
        $booking = Booking::factory()->for($business)->for($property)->create(['currency' => 'NGN', 'total_amount' => 125000, 'payment_status' => 'unpaid']);

        $this->actingAs($owner)->post(route('owner.finance.payments.store'), [
            'booking_id' => $booking->id, 'reference' => 'BANK-001', 'amount' => '125000.00',
            'method' => 'bank_transfer', 'transaction_at' => '2026-09-10T10:30', 'notes' => 'Verified transfer',
        ])->assertRedirect(route('owner.finance'));

        $payment = Payment::query()->sole();
        $this->assertSame('125000.0000', $payment->amount);
        $this->assertSame('paid', $booking->fresh()->payment_status->value);
        $this->assertDatabaseHas('financial_transactions', ['payment_id' => $payment->id, 'direction' => 'inflow', 'transaction_status' => 'settled']);

        $this->actingAs($owner)->post(route('owner.finance.payments.store'), [
            'booking_id' => $booking->id, 'reference' => 'BANK-001', 'amount' => '10',
            'method' => 'cash', 'transaction_at' => '2026-09-10T11:00',
        ])->assertSessionHasErrors('reference');
        $this->assertDatabaseCount('payments', 1);
    }

    public function test_owner_can_record_expense_and_foreign_entities_are_rejected(): void
    {
        Storage::fake('local');
        [$owner, $business] = $this->ownerWithBusiness('Nexa Stays');
        [, $other] = $this->ownerWithBusiness('Other Stays');
        $property = Property::factory()->for($business)->create();
        $foreignProperty = Property::factory()->for($other)->create();
        $foreignBooking = Booking::factory()->for($other)->for($foreignProperty)->create(['currency' => 'NGN']);

        $this->actingAs($owner)->post(route('owner.finance.expenses.store'), [
            'property_id' => $property->id, 'category' => 'maintenance', 'amount' => '45000.50',
            'incurred_on' => '2026-09-10', 'description' => 'Air conditioner repair', 'payee' => 'Repair Co',
            'receipt' => UploadedFile::fake()->create('receipt.pdf', 50, 'application/pdf'),
        ])->assertRedirect(route('owner.finance'));

        $expense = Expense::query()->sole();
        $this->assertSame('45000.5000', $expense->amount);
        Storage::disk('local')->assertExists($expense->receipt_path);
        $this->assertDatabaseHas('financial_transactions', ['expense_id' => $expense->id, 'direction' => 'outflow']);

        $this->actingAs($owner)->post(route('owner.finance.expenses.store'), [
            'property_id' => $foreignProperty->id, 'category' => 'cleaning', 'amount' => 100,
            'incurred_on' => '2026-09-10', 'description' => 'Not mine',
        ])->assertNotFound();
        $this->actingAs($owner)->post(route('owner.finance.payments.store'), [
            'booking_id' => $foreignBooking->id, 'reference' => 'FOREIGN', 'amount' => 100,
            'method' => 'cash', 'transaction_at' => '2026-09-10T10:00',
        ])->assertNotFound();
    }

    public function test_finance_filters_real_records_and_rejects_zero_amounts(): void
    {
        [$owner, $business] = $this->ownerWithBusiness('Nexa Stays');
        $first = Property::factory()->for($business)->create(['name' => 'First Suite']);
        $second = Property::factory()->for($business)->create(['name' => 'Second Suite']);
        Expense::query()->create(['business_id' => $business->id, 'property_id' => $first->id, 'reference' => 'EXP-FIRST', 'category' => 'cleaning', 'amount' => 1000, 'currency' => 'NGN', 'incurred_on' => '2026-09-01', 'approval_status' => 'approved', 'description' => 'Visible cleaning', 'status' => 'active']);
        Expense::query()->create(['business_id' => $business->id, 'property_id' => $second->id, 'reference' => 'EXP-SECOND', 'category' => 'maintenance', 'amount' => 2000, 'currency' => 'NGN', 'incurred_on' => '2026-08-01', 'approval_status' => 'approved', 'description' => 'Hidden repair', 'status' => 'active']);

        $this->actingAs($owner)->get(route('owner.finance', ['property' => $first->id, 'from' => '2026-09-01', 'type' => 'expense', 'q' => 'cleaning']))
            ->assertOk()->assertSee('Visible cleaning')->assertDontSee('Hidden repair');
        $this->actingAs($owner)->post(route('owner.finance.expenses.store'), ['category' => 'cleaning', 'amount' => 0, 'incurred_on' => '2026-09-10', 'description' => 'Invalid'])
            ->assertSessionHasErrors('amount');
    }

    public function test_finance_records_are_immutable(): void
    {
        [, $business] = $this->ownerWithBusiness('Nexa Stays');
        $expense = Expense::query()->create(['business_id' => $business->id, 'reference' => 'EXP-AUDIT', 'category' => 'other', 'amount' => 1000, 'currency' => 'NGN', 'incurred_on' => '2026-09-10', 'approval_status' => 'approved', 'description' => 'Audit record', 'status' => 'active']);

        $this->expectException(LogicException::class);
        $expense->delete();
    }

    /** @return array{User, Business} */
    private function ownerWithBusiness(string $name): array
    {
        $this->seed(AccessControlSeeder::class);
        $user = User::factory()->create(['email_verified_at' => now()]);
        $business = app(BusinessOnboardingService::class)->onboard($user, ['name' => $name, 'country_code' => 'NG', 'business_type' => 'serviced_apartments', 'timezone' => 'Africa/Lagos', 'currency' => 'NGN']);

        return [$user, $business];
    }
}
