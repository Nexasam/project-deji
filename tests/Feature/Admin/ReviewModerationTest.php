<?php

namespace Tests\Feature\Admin;

use App\Models\Booking;
use App\Models\Business;
use App\Models\Property;
use App\Models\PropertyMarketplaceListing;
use App\Models\Review;
use App\Models\ReviewResponse;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Database\Seeders\AccessControlSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewModerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_verification_admin_can_hide_and_restore_a_review_without_deleting_it(): void
    {
        $this->seed(AccessControlSeeder::class);
        $admin = $this->administrator('platform_verification_admin');
        [$review, $slug] = $this->publishedReview();
        $publishedAt = $review->published_at;

        $this->get(route('marketplace.show', $slug))->assertSee('A genuinely wonderful stay');
        $this->actingAs($admin)->get(route('admin.reviews.index'))
            ->assertOk()->assertSee('Review moderation')->assertSee('A genuinely wonderful stay');

        $this->actingAs($admin)->post(route('admin.reviews.hide', $review), [
            'reason' => 'Content is being hidden while a credible safety report is reviewed.',
        ])->assertRedirect(route('admin.reviews.show', $review));

        $review->refresh();
        $this->assertSame('hidden', $review->moderation_status);
        $this->assertTrue($publishedAt->equalTo($review->published_at));
        $this->assertDatabaseHas('reviews', ['id' => $review->id, 'content' => 'A genuinely wonderful stay']);
        $this->get(route('marketplace.show', $slug))->assertDontSee('A genuinely wonderful stay');
        $this->assertDatabaseHas('audit_events', ['event_type' => 'platform.review.hidden', 'auditable_id' => $review->id]);

        $this->actingAs($admin)->post(route('admin.reviews.restore', $review), [
            'reason' => 'Moderation review completed and the content meets platform policy.',
        ])->assertRedirect(route('admin.reviews.show', $review));
        $this->assertSame('approved', $review->fresh()->moderation_status);
        $this->get(route('marketplace.show', $slug))->assertSee('A genuinely wonderful stay');
    }

    public function test_owner_response_can_be_hidden_and_restored_independently(): void
    {
        $this->seed(AccessControlSeeder::class);
        $admin = $this->administrator('platform_verification_admin');
        [$review, $slug] = $this->publishedReview(withResponse: true);
        $response = $review->response;

        $this->get(route('marketplace.show', $slug))->assertSee('Thank you for staying with us');
        $this->actingAs($admin)->post(route('admin.review-responses.hide', $response), [
            'reason' => 'The owner response includes content requiring a policy assessment.',
        ])->assertRedirect(route('admin.reviews.show', $review));
        $this->assertSame('hidden', $response->fresh()->moderation_status);
        $this->assertSame('approved', $review->fresh()->moderation_status);
        $this->get(route('marketplace.show', $slug))->assertDontSee('Thank you for staying with us');

        $this->actingAs($admin)->post(route('admin.review-responses.restore', $response), [
            'reason' => 'The owner response was assessed and can be shown publicly again.',
        ])->assertRedirect(route('admin.reviews.show', $review));
        $this->get(route('marketplace.show', $slug))->assertSee('Thank you for staying with us');
    }

    public function test_support_admin_can_inspect_but_cannot_moderate_reviews(): void
    {
        $this->seed(AccessControlSeeder::class);
        $support = $this->administrator('platform_support_admin');
        [$review] = $this->publishedReview();

        $this->actingAs($support)->get(route('admin.reviews.show', $review))->assertOk();
        $response = $this->actingAs($support)->post(route('admin.reviews.hide', $review), [
            'reason' => 'Support roles must not be able to perform moderation decisions.',
        ]);
        $this->assertSame(403, $response->getStatusCode());
        $this->assertSame('approved', $review->fresh()->moderation_status);
    }

    /** @return array{Review, string} */
    private function publishedReview(bool $withResponse = false): array
    {
        $business = Business::factory()->verified()->create();
        $property = Property::factory()->for($business)->create([
            'property_type' => 'serviced_apartment', 'booking_mode' => 'entire',
            'verification_status' => 'verified', 'publication_status' => 'published',
            'readiness_status' => 'ready', 'operational_status' => 'available', 'status' => 'active',
        ]);
        PropertyMarketplaceListing::query()->create([
            'business_id' => $business->id, 'property_id' => $property->id,
            'slug' => 'review-moderation-home', 'public_title' => 'Review Moderation Home',
            'publication_status' => 'published', 'is_publication_eligible' => true, 'status' => 'active',
        ]);
        $guest = User::factory()->create();
        $booking = Booking::factory()->create(['business_id' => $business->id, 'property_id' => $property->id, 'guest_user_id' => $guest->id]);
        $review = Review::query()->create([
            'business_id' => $business->id, 'property_id' => $property->id, 'booking_id' => $booking->id,
            'guest_user_id' => $guest->id, 'rating' => 5, 'title' => 'Excellent',
            'content' => 'A genuinely wonderful stay', 'is_verified_stay' => true,
            'moderation_status' => 'approved', 'submitted_at' => now(), 'published_at' => now(),
            'status' => 'active', 'created_by' => $guest->id,
        ]);
        if ($withResponse) {
            ReviewResponse::query()->create([
                'business_id' => $business->id, 'review_id' => $review->id, 'responded_by' => $guest->id,
                'content' => 'Thank you for staying with us', 'moderation_status' => 'approved',
                'submitted_at' => now(), 'published_at' => now(), 'status' => 'active',
            ]);
            $review->load('response');
        }

        return [$review, 'review-moderation-home'];
    }

    private function administrator(string $roleKey): User
    {
        $admin = User::factory()->create(['email_verified_at' => now()]);
        UserRole::query()->create(['user_id' => $admin->id, 'role_id' => Role::query()->where('system_key', $roleKey)->firstOrFail()->id, 'status' => 'active', 'assigned_at' => now()]);

        return $admin;
    }
}
