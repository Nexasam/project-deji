<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Services\Reviews\VerifiedStayReviewService;
use App\Support\ActiveBusinessContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OwnerReviewResponseController extends Controller
{
    public function store(
        Request $request,
        ActiveBusinessContext $context,
        string $review,
        VerifiedStayReviewService $reviews,
    ): RedirectResponse {
        $record = Review::query()->where('business_id', $context->business->id)->findOrFail($review);
        $data = $request->validate([
            'content' => ['required', 'string', 'min:10', 'max:2000'],
        ]);

        $reviews->respond($context->business, $record, $request->user(), $data);

        return redirect()->route('owner.bookings.show', $record->booking_id)
            ->with('status', 'Your public response has been posted.');
    }
}
