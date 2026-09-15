<?php

namespace App\Http\Middleware;

use App\Models\Booking;
use App\Models\GuestServiceRequest;
use App\Models\OperationalTask;
use App\Models\Property;
use App\Models\PropertyAvailabilityBlock;
use App\Models\Review;
use App\Services\Access\BusinessPermissionService;
use App\Support\ActiveBusinessContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureBusinessPermission
{
    public function __construct(private readonly BusinessPermissionService $permissions) {}

    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $context = app(ActiveBusinessContext::class);
        $property = $this->resolveProperty($request, $context);

        abort_unless($this->permissions->allows($request->user(), $context, $permission, $property), 403);

        return $next($request);
    }

    private function resolveProperty(Request $request, ActiveBusinessContext $context): ?Property
    {
        if ($value = $request->route('property')) {
            return $value instanceof Property
                ? $value
                : $context->business->properties()->findOrFail($value);
        }

        foreach ([
            'booking' => Booking::class,
            'task' => OperationalTask::class,
            'review' => Review::class,
            'block' => PropertyAvailabilityBlock::class,
            'serviceRequest' => GuestServiceRequest::class,
        ] as $parameter => $model) {
            if (! $value = $request->route($parameter)) {
                continue;
            }
            $record = $value instanceof $model
                ? $value
                : $model::query()->where('business_id', $context->business->id)->findOrFail($value);

            return $context->business->properties()->findOrFail($record->property_id);
        }

        if ($propertyId = $request->input('property_id')) {
            return $context->business->properties()->findOrFail($propertyId);
        }

        if ($bookingId = $request->input('booking_id')) {
            $booking = $context->business->bookings()->findOrFail($bookingId);

            return $context->business->properties()->findOrFail($booking->property_id);
        }

        return null;
    }
}
