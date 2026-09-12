<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\StoreExpenseRequest;
use App\Http\Requests\Owner\StoreManualPaymentRequest;
use App\Services\Finance\RecordExpense;
use App\Services\Finance\RecordManualPayment;
use App\Support\ActiveBusinessContext;
use Illuminate\Http\RedirectResponse;

class OwnerFinanceEntryController extends Controller
{
    public function payment(StoreManualPaymentRequest $request, ActiveBusinessContext $context, RecordManualPayment $record): RedirectResponse
    {
        $booking = $context->business->bookings()->findOrFail($request->validated('booking_id'));
        $record->handle($booking, $request->user(), $request->validated());

        return redirect()->route('owner.finance')->with('status', 'Payment recorded.');
    }

    public function expense(StoreExpenseRequest $request, ActiveBusinessContext $context, RecordExpense $record): RedirectResponse
    {
        $data = $request->validated();
        $property = empty($data['property_id']) ? null : $context->business->properties()->findOrFail($data['property_id']);
        $record->handle($context->business->id, $property, $request->user(), $context->business->currency, $data);

        return redirect()->route('owner.finance')->with('status', 'Expense recorded.');
    }
}
