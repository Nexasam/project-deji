<?php

namespace Tests\Feature\Admin;

use App\Models\Booking;
use App\Models\BookingFinancialAllocation;
use App\Models\Business;
use App\Models\BusinessSettlementAccount;
use App\Models\SettlementItem;
use App\Models\User;
use App\Services\Finance\SettlementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use LogicException;
use Tests\TestCase;

class SettlementManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_verified_business_balances_follow_separated_settlement_transitions(): void
    {
        $creator = User::factory()->create(); $approver = User::factory()->create();
        $business = Business::factory()->verified()->create(['currency'=>'NGN','verification_payload'=>['nin_status'=>'verified','bvn_status'=>'verified']]);
        $booking = Booking::factory()->for($business)->create(['currency'=>'NGN']);
        $entry = BookingFinancialAllocation::query()->create(['business_id'=>$business->id,'booking_id'=>$booking->id,'allocation_type'=>'owner_balance','direction'=>'credit','amount'=>95000,'currency'=>'NGN','recognized_on'=>today(),'status'=>'active']);
        $account = BusinessSettlementAccount::query()->create(['business_id'=>$business->id,'bank_name'=>'Test Bank','account_name'=>'Operator Ltd','account_number'=>'0123456789','account_number_last4'=>'6789','status'=>'verified','verified_by'=>$approver->id,'verified_at'=>now()]);
        $service = app(SettlementService::class);

        $batch = $service->create($business, $account, [$entry->id], $creator);
        $this->assertSame('draft', $batch->settlement_status);
        $this->assertSame('95000.0000', $batch->amount);
        $this->assertSame('•••• •••• 6789', $account->masked_number);
        $this->assertNotSame('0123456789', $account->getRawOriginal('account_number'));
        $this->expectException(ValidationException::class);
        $service->approve($batch, $creator);
    }

    public function test_approved_batch_completes_once_and_allocations_cannot_be_reused_or_mutated(): void
    {
        $creator = User::factory()->create(); $approver = User::factory()->create();
        $business = Business::factory()->verified()->create(['currency'=>'NGN','verification_payload'=>['nin_status'=>'verified','bvn_status'=>'verified']]);
        $booking = Booking::factory()->for($business)->create(['currency'=>'NGN']);
        $entry = BookingFinancialAllocation::query()->create(['business_id'=>$business->id,'booking_id'=>$booking->id,'allocation_type'=>'owner_balance','direction'=>'credit','amount'=>50000,'currency'=>'NGN','recognized_on'=>today(),'status'=>'active']);
        $account = BusinessSettlementAccount::query()->create(['business_id'=>$business->id,'bank_name'=>'Test Bank','account_name'=>'Operator Ltd','account_number'=>'0123456789','account_number_last4'=>'6789','status'=>'verified']);
        $service = app(SettlementService::class);
        $batch = $service->approve($service->create($business,$account,[$entry->id],$creator),$approver);
        $batch = $service->complete($batch,'TRF-001',$approver);
        $this->assertSame('completed',$batch->settlement_status);
        $this->assertCount(0,$service->available($business));
        $this->assertSame(1,SettlementItem::query()->count());

        try { $service->create($business,$account,[$entry->id],$creator); $this->fail('Allocation was reused.'); }
        catch (ValidationException) { $this->assertTrue(true); }
        $this->expectException(LogicException::class);
        $entry->update(['amount'=>1]);
    }

    public function test_unverified_business_identity_is_blocked_from_settlement(): void
    {
        $actor=User::factory()->create(); $business=Business::factory()->verified()->create(['verification_payload'=>[]]);
        $booking=Booking::factory()->for($business)->create();
        $entry=BookingFinancialAllocation::query()->create(['business_id'=>$business->id,'booking_id'=>$booking->id,'allocation_type'=>'owner_balance','direction'=>'credit','amount'=>100,'currency'=>'NGN','recognized_on'=>today(),'status'=>'active']);
        $account=BusinessSettlementAccount::query()->create(['business_id'=>$business->id,'bank_name'=>'Bank','account_name'=>'Name','account_number'=>'0123456789','account_number_last4'=>'6789','status'=>'verified']);
        $this->expectException(ValidationException::class);
        app(SettlementService::class)->create($business,$account,[$entry->id],$actor);
    }
}
