<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\StoreAvailabilityBlockRequest;
use App\Models\PropertyAvailabilityBlock;
use App\Services\Calendar\ManageManualAvailabilityBlock;
use App\Support\ActiveBusinessContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OwnerAvailabilityBlockController extends Controller
{
    public function store(StoreAvailabilityBlockRequest $request, ActiveBusinessContext $context, ManageManualAvailabilityBlock $blocks): RedirectResponse
    {
        $data = $request->validated();
        $property = $context->business->properties()->findOrFail($data['property_id']);
        $blocks->create($property, $request->user(), $data);

        return redirect()->route('owner.calendar', array_filter([
            'month' => $data['month'] ?? null,
            'property' => $property->id,
        ]))->with('status', 'Availability block added.');
    }

    public function destroy(Request $request, string $block, ActiveBusinessContext $context, ManageManualAvailabilityBlock $blocks): RedirectResponse
    {
        $block = PropertyAvailabilityBlock::query()->where('business_id', $context->business->id)->findOrFail($block);
        $blocks->release($block, $request->user());

        return redirect()->route('owner.calendar', array_filter([
            'month' => $request->string('month')->toString() ?: null,
            'property' => $request->string('property')->toString() ?: null,
        ]))->with('status', 'Availability block released.');
    }
}
