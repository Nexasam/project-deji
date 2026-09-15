<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditEvent;
use App\Models\Review;
use App\Models\ReviewResponse;
use App\Services\Platform\ModerateMarketplaceContent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminReviewController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'moderation_status' => ['nullable', 'in:pending,approved,hidden,rejected'],
            'rating' => ['nullable', 'integer', 'between:1,5'],
        ]);
        $reviews = Review::query()->with(['guest', 'property', 'property.business', 'response'])
            ->when($filters['q'] ?? null, fn ($query, $term) => $query->where(fn ($query) => $query
                ->where('title', 'like', "%{$term}%")->orWhere('content', 'like', "%{$term}%")
                ->orWhereHas('guest', fn ($guest) => $guest->where('name', 'like', "%{$term}%"))
                ->orWhereHas('property', fn ($property) => $property->where('name', 'like', "%{$term}%"))))
            ->when($filters['moderation_status'] ?? null, fn ($query, $value) => $query->where('moderation_status', $value))
            ->when($filters['rating'] ?? null, fn ($query, $value) => $query->where('rating', $value))
            ->latest('submitted_at')->paginate(25)->withQueryString();

        return view('admin.reviews.index', compact('reviews', 'filters'));
    }

    public function show(Review $review): View
    {
        $review->load(['guest', 'property.business', 'booking.payments', 'response']);
        $subjectIds = array_filter([$review->id, $review->response?->id]);

        return view('admin.reviews.show', [
            'review' => $review,
            'audits' => AuditEvent::query()->with('actor')->whereIn('auditable_id', $subjectIds)->latest('occurred_at')->get(),
        ]);
    }

    public function hide(Request $request, Review $review, ModerateMarketplaceContent $moderator): RedirectResponse
    {
        $moderator->hideReview($review, $request->user(), $this->reason($request));

        return redirect()->route('admin.reviews.show', $review)->with('status', 'Review hidden from the marketplace.');
    }

    public function restore(Request $request, Review $review, ModerateMarketplaceContent $moderator): RedirectResponse
    {
        $moderator->restoreReview($review, $request->user(), $this->reason($request));

        return redirect()->route('admin.reviews.show', $review)->with('status', 'Review restored to the marketplace.');
    }

    public function hideResponse(Request $request, ReviewResponse $response, ModerateMarketplaceContent $moderator): RedirectResponse
    {
        $moderator->hideResponse($response, $request->user(), $this->reason($request));

        return redirect()->route('admin.reviews.show', $response->review_id)->with('status', 'Owner response hidden from the marketplace.');
    }

    public function restoreResponse(Request $request, ReviewResponse $response, ModerateMarketplaceContent $moderator): RedirectResponse
    {
        $moderator->restoreResponse($response, $request->user(), $this->reason($request));

        return redirect()->route('admin.reviews.show', $response->review_id)->with('status', 'Owner response restored to the marketplace.');
    }

    private function reason(Request $request): string
    {
        return $request->validate(['reason' => ['required', 'string', 'min:10', 'max:1000']])['reason'];
    }
}
