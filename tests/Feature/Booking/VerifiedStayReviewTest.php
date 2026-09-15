<?php

namespace Tests\Feature\Booking;

use App\Models\Booking;
use App\Models\Business;
use App\Models\Property;
use App\Models\PropertyMarketplaceListing;
use App\Models\Review;
use App\Models\ReviewResponse;
use App\Models\User;
use App\Services\Booking\BookingLifecycleService;
use App\Services\Business\BusinessOnboardingService;
use App\Services\Reviews\VerifiedStayReviewService;
use Database\Seeders\AccessControlSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class VerifiedStayReviewTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake();
    }

    public function test_completing_a_stay_creates_one_review_invitation_and_notifies_the_guest(): void
    {
        [$owner, $business] = $this->ownerWithBusiness();
        $guest = User::factory()->create();
        $property = Property::factory()->for($business)->create();
        $booking = Booking::factory()->for($business)->for($property)->create([
            'guest_user_id' => $guest->id,
            'status' => 'checked_out',
        ]);

        app(BookingLifecycleService::class)->complete($booking, $owner);
        app(VerifiedStayReviewService::class)->invite($booking->fresh(['guest', 'business', 'property']), $owner);

        $this->assertDatabaseCount('review_invitations', 1);
        $this->assertDatabaseHas('review_invitations', [
            'booking_id' => $booking->id,
            'guest_user_id' => $guest->id,
            'status' => 'pending',
        ]);
        $this->assertSame(1, $guest->notifications()->where('type', 'review_invitation')->count());
    }

    public function test_only_the_completed_stay_guest_can_submit_one_verified_review(): void
    {
        [$owner, $business] = $this->ownerWithBusiness();
        $guest = User::factory()->create();
        $stranger = User::factory()->create();
        $property = Property::factory()->for($business)->create();
        $booking = Booking::factory()->for($business)->for($property)->create([
            'guest_user_id' => $guest->id,
            'status' => 'completed',
        ]);

        $payload = [
            'rating' => 5,
            'cleanliness_rating' => 5,
            'communication_rating' => 4,
            'location_rating' => 5,
            'value_rating' => 4,
            'accuracy_rating' => 5,
            'title' => 'Exactly as described',
            'content' => 'The apartment was clean, comfortable and exactly as described.',
        ];

        $this->actingAs($stranger)->post(route('guest.bookings.reviews.store', $booking), $payload)->assertNotFound();
        $this->actingAs($guest)->post(route('guest.bookings.reviews.store', $booking), $payload)
            ->assertRedirect(route('guest.bookings.show', $booking));

        $this->assertDatabaseHas('reviews', [
            'booking_id' => $booking->id,
            'guest_user_id' => $guest->id,
            'rating' => 5,
            'is_verified_stay' => true,
            'moderation_status' => 'approved',
            'status' => 'active',
        ]);
        $this->assertDatabaseHas('review_invitations', ['booking_id' => $booking->id, 'status' => 'used']);
        $this->assertDatabaseHas('notifications', ['user_id' => $owner->id, 'type' => 'review_submitted']);

        $this->post(route('guest.bookings.reviews.store', $booking), $payload)->assertSessionHasErrors('review');
        $this->assertSame(1, Review::query()->where('booking_id', $booking->id)->count());
    }

    public function test_a_guest_cannot_review_an_unfinished_stay(): void
    {
        [, $business] = $this->ownerWithBusiness();
        $guest = User::factory()->create();
        $booking = Booking::factory()->for($business)->for(Property::factory()->for($business))->create([
            'guest_user_id' => $guest->id,
            'status' => 'checked_in',
        ]);

        $this->actingAs($guest)->post(route('guest.bookings.reviews.store', $booking), [
            'rating' => 4,
            'content' => 'This stay has not been completed, so this should not publish.',
        ])->assertSessionHasErrors('review');

        $this->assertDatabaseCount('reviews', 0);
    }

    public function test_correct_business_owner_can_respond_once_and_guest_is_notified(): void
    {
        [$owner, $business] = $this->ownerWithBusiness();
        [$foreignOwner] = $this->ownerWithBusiness('Foreign Stays');
        $guest = User::factory()->create();
        $property = Property::factory()->for($business)->create();
        $booking = Booking::factory()->for($business)->for($property)->create([
            'guest_user_id' => $guest->id,
            'status' => 'completed',
        ]);
        $review = $this->publishedReview($booking, $guest, 'A helpful verified guest review.');

        $this->actingAs($foreignOwner)->post(route('owner.reviews.responses.store', $review), [
            'content' => 'A foreign owner must not be allowed to post this response.',
        ])->assertNotFound();

        $this->actingAs($owner)->post(route('owner.reviews.responses.store', $review), [
            'content' => 'Thank you for staying with us. We would be delighted to host you again.',
        ])->assertRedirect(route('owner.bookings.show', $booking));

        $this->assertDatabaseHas('review_responses', [
            'review_id' => $review->id,
            'responded_by' => $owner->id,
            'moderation_status' => 'approved',
        ]);
        $this->assertDatabaseHas('notifications', ['user_id' => $guest->id, 'type' => 'review_response']);

        $this->post(route('owner.reviews.responses.store', $review), [
            'content' => 'A duplicate public response should be rejected.',
        ])->assertSessionHasErrors('response');
        $this->assertSame(1, ReviewResponse::query()->where('review_id', $review->id)->count());
    }

    public function test_published_reviews_and_owner_responses_appear_on_the_marketplace_listing(): void
    {
        $business = Business::factory()->create();
        $property = $this->marketplaceProperty($business);
        $guest = User::factory()->create(['name' => 'Verified Guest']);
        $booking = Booking::factory()->for($business)->for($property)->create([
            'guest_user_id' => $guest->id,
            'status' => 'completed',
        ]);
        $review = $this->publishedReview($booking, $guest, 'The waterfront view was excellent and the flat was spotless.');
        ReviewResponse::query()->create([
            'business_id' => $business->id,
            'review_id' => $review->id,
            'content' => 'Thank you for your thoughtful feedback.',
            'moderation_status' => 'approved',
            'submitted_at' => now(),
            'published_at' => now(),
            'status' => 'active',
        ]);
        $pendingGuest = User::factory()->create();
        $pendingBooking = Booking::factory()->for($business)->for($property)->create([
            'guest_user_id' => $pendingGuest->id,
            'status' => 'completed',
        ]);
        Review::query()->create([
            'business_id' => $business->id,
            'property_id' => $property->id,
            'booking_id' => $pendingBooking->id,
            'guest_user_id' => $pendingGuest->id,
            'rating' => 1,
            'content' => 'This pending review must not appear publicly.',
            'is_verified_stay' => true,
            'moderation_status' => 'pending',
            'submitted_at' => now(),
            'status' => 'active',
        ]);

        $this->get(route('marketplace.show', 'waterfront-review-flat'))
            ->assertOk()
            ->assertSee('Verified Guest')
            ->assertSee('The waterfront view was excellent')
            ->assertSee('Thank you for your thoughtful feedback.')
            ->assertDontSee('This pending review must not appear publicly.');
    }

    private function ownerWithBusiness(string $name = 'Nexa Stays'): array
    {
        $this->seed(AccessControlSeeder::class);
        $owner = User::factory()->create(['email_verified_at' => now()]);
        $business = app(BusinessOnboardingService::class)->onboard($owner, [
            'name' => $name,
            'country_code' => 'NG',
            'business_type' => 'serviced_apartments',
            'timezone' => 'Africa/Lagos',
            'currency' => 'NGN',
        ]);

        return [$owner, $business];
    }

    private function publishedReview(Booking $booking, User $guest, string $content): Review
    {
        return Review::query()->create([
            'business_id' => $booking->business_id,
            'property_id' => $booking->property_id,
            'booking_id' => $booking->id,
            'guest_user_id' => $guest->id,
            'rating' => 5,
            'title' => 'A lovely stay',
            'content' => $content,
            'is_verified_stay' => true,
            'moderation_status' => 'approved',
            'submitted_at' => now(),
            'published_at' => now(),
            'status' => 'active',
        ]);
    }

    private function marketplaceProperty(Business $business): Property
    {
        $property = Property::factory()->for($business)->create([
            'property_type' => 'serviced_apartment',
            'booking_mode' => 'entire',
            'verification_status' => 'verified',
            'publication_status' => 'published',
            'readiness_status' => 'ready',
            'operational_status' => 'available',
            'status' => 'active',
        ]);
        PropertyMarketplaceListing::query()->create([
            'business_id' => $business->id,
            'property_id' => $property->id,
            'slug' => 'waterfront-review-flat',
            'public_title' => 'Waterfront Review Flat',
            'public_description' => 'A published marketplace property used to verify guest reviews.',
            'publication_status' => 'published',
            'is_publication_eligible' => true,
            'published_at' => now(),
            'status' => 'active',
        ]);

        return $property;
    }
}
