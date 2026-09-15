<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Services\Booking\ManageGuestServiceRequest;
use App\Support\ActiveBusinessContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OwnerGuestServiceRequestController extends Controller
{
    public function update(Request $request, ActiveBusinessContext $context, ManageGuestServiceRequest $service, string $serviceRequest): RedirectResponse
    {
        $data = $request->validate([
            'action' => ['required', 'in:acknowledge,resolve'],
            'resolution' => ['nullable', 'required_if:action,resolve', 'string', 'max:2000'],
        ]);
        $record = $context->business->guestServiceRequests()->findOrFail($serviceRequest);
        $service->update($record, $request->user(), $data);

        return back()->with('status', $data['action'] === 'resolve' ? 'Guest request resolved.' : 'Guest request acknowledged.');
    }
}
