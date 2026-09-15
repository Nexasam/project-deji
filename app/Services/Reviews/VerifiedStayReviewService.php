<?php

namespace App\Services\Reviews;

use App\Models\Booking;
use App\Models\Business;
use App\Models\Review;
use App\Models\ReviewInvitation;
use App\Models\ReviewResponse;
use App\Models\User;
use App\Services\Notifications\ProductNotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class VerifiedStayReviewService
{
    public function __construct(private readonly ProductNotificationService $notifications) {}

    public function invite(Booking $booking, User $actor): ReviewInvitation
    {
        $invitation = ReviewInvitation::query()->firstOrCreate(
            ['booking_id' => $booking->id, 'guest_user_id' => $booking->guest_user_id],
            [
                'business_id' => $booking->business_id,
                'property_id' => $booking->property_id,
                'token_hash' => hash('sha512', Str::random(80).$booking->id),
                'sent_at' => now(),
                'expires_at' => now()->addDays(30),
                'status' => 'pending',
                'created_by' => $actor->id,
                'updated_by' => $actor->id,
            ]
        );

        if ($invitation->wasRecentlyCreated) {
            $this->notifications->user(
                $booking->guest,
                $booking->business,
                'review_invitation',
                'How was your stay?',
                "Your verified stay at {$booking->property->name} is complete. Share a review within 30 days.",
                ['url' => route('guest.bookings.show', $booking), 'booking_id' => $booking->id]
            );
        }

        return $invitation;
    }

    /** @param array<string, mixed> $data */
    public function submit(Booking $booking, User $guest, array $data): Review
    {
        return DB::transaction(function () use ($booking, $guest, $data): Review {
            $booking = Booking::query()->with(['business', 'property', 'guest'])
                ->lockForUpdate()->findOrFail($booking->id);

            if ($booking->guest_user_id !== $guest->id) {
                abort(404);
            }

            if ($booking->status->value !== 'completed') {
                throw ValidationException::withMessages([
                    'review' => 'A review can only be submitted after the stay is completed.',
                ]);
            }

            if ($booking->reviews()->where('guest_user_id', $guest->id)->exists()) {
                throw ValidationException::withMessages([
                    'review' => 'You have already reviewed this stay.',
                ]);
            }

            $invitation = ReviewInvitation::query()
                ->where('booking_id', $booking->id)
                ->where('guest_user_id', $guest->id)
                ->lockForUpdate()
                ->first();

            if ($invitation && $invitation->expires_at->isPast()) {
                $invitation->update(['status' => 'expired', 'updated_by' => $guest->id]);
                throw ValidationException::withMessages([
                    'review' => 'This review invitation has expired.',
                ]);
            }

            $invitation ??= ReviewInvitation::query()->create([
                'business_id' => $booking->business_id,
                'property_id' => $booking->property_id,
                'booking_id' => $booking->id,
                'guest_user_id' => $guest->id,
                'token_hash' => hash('sha512', Str::random(80).$booking->id),
                'sent_at' => now(),
                'expires_at' => now()->addDays(30),
                'status' => 'pending',
                'created_by' => $guest->id,
                'updated_by' => $guest->id,
            ]);

            $review = Review::query()->create([
                'business_id' => $booking->business_id,
                'property_id' => $booking->property_id,
                'booking_id' => $booking->id,
                'guest_user_id' => $guest->id,
                'rating' => $data['rating'],
                'cleanliness_rating' => $data['cleanliness_rating'] ?? null,
                'communication_rating' => $data['communication_rating'] ?? null,
                'location_rating' => $data['location_rating'] ?? null,
                'value_rating' => $data['value_rating'] ?? null,
                'accuracy_rating' => $data['accuracy_rating'] ?? null,
                'title' => $data['title'] ?? null,
                'content' => $data['content'],
                'is_verified_stay' => true,
                'moderation_status' => 'approved',
                'submitted_at' => now(),
                'published_at' => now(),
                'status' => 'active',
                'created_by' => $guest->id,
                'updated_by' => $guest->id,
            ]);

            $invitation->update([
                'used_at' => now(),
                'status' => 'used',
                'updated_by' => $guest->id,
            ]);

            $this->notifications->businessOwners(
                $booking->business,
                'review_submitted',
                'A guest reviewed your property',
                "{$guest->name} left a {$review->rating}-star verified-stay review for {$booking->property->name}.",
                ['url' => route('owner.bookings.show', $booking), 'booking_id' => $booking->id, 'review_id' => $review->id]
            );

            return $review;
        });
    }

    /** @param array<string, mixed> $data */
    public function respond(Business $business, Review $review, User $owner, array $data): ReviewResponse
    {
        return DB::transaction(function () use ($business, $review, $owner, $data): ReviewResponse {
            $review = Review::query()->with(['guest', 'booking', 'property'])
                ->where('business_id', $business->id)->lockForUpdate()->findOrFail($review->id);

            if ($review->moderation_status !== 'approved' || ! $review->published_at || $review->status !== 'active') {
                throw ValidationException::withMessages([
                    'response' => 'Only a published review can receive a response.',
                ]);
            }

            if ($review->response()->exists()) {
                throw ValidationException::withMessages([
                    'response' => 'This review already has an owner response.',
                ]);
            }

            $response = ReviewResponse::query()->create([
                'business_id' => $business->id,
                'review_id' => $review->id,
                'responded_by' => $owner->id,
                'content' => $data['content'],
                'moderation_status' => 'approved',
                'submitted_at' => now(),
                'published_at' => now(),
                'status' => 'active',
                'created_by' => $owner->id,
                'updated_by' => $owner->id,
            ]);

            $this->notifications->user(
                $review->guest,
                $business,
                'review_response',
                'The property owner responded',
                "The owner of {$review->property->name} responded to your review.",
                ['url' => route('guest.bookings.show', $review->booking), 'booking_id' => $review->booking_id, 'review_id' => $review->id]
            );

            return $response;
        });
    }
}
