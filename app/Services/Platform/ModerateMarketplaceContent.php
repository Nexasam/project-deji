<?php

namespace App\Services\Platform;

use App\Models\Review;
use App\Models\ReviewResponse;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class ModerateMarketplaceContent
{
    public function __construct(private readonly PlatformAudit $audit) {}

    public function hideReview(Review $review, User $actor, string $reason): Review
    {
        /** @var Review */
        return $this->transition($review, $actor, $reason, 'hidden', 'platform.review.hidden', 'Hidden guest review');
    }

    public function restoreReview(Review $review, User $actor, string $reason): Review
    {
        /** @var Review */
        return $this->transition($review, $actor, $reason, 'approved', 'platform.review.restored', 'Restored guest review');
    }

    public function hideResponse(ReviewResponse $response, User $actor, string $reason): ReviewResponse
    {
        /** @var ReviewResponse */
        return $this->transition($response, $actor, $reason, 'hidden', 'platform.review_response.hidden', 'Hidden owner response');
    }

    public function restoreResponse(ReviewResponse $response, User $actor, string $reason): ReviewResponse
    {
        /** @var ReviewResponse */
        return $this->transition($response, $actor, $reason, 'approved', 'platform.review_response.restored', 'Restored owner response');
    }

    private function transition(Model $content, User $actor, string $reason, string $status, string $event, string $description): Model
    {
        if (mb_strlen(trim($reason)) < 10) {
            throw ValidationException::withMessages(['reason' => 'Give a clear moderation reason of at least 10 characters.']);
        }

        return DB::transaction(function () use ($content, $actor, $reason, $status, $event, $description): Model {
            $content = $content->newQuery()->lockForUpdate()->findOrFail($content->getKey());
            $before = ['moderation_status' => $content->moderation_status, 'published_at' => $content->published_at?->toIso8601String()];
            $content->update(['moderation_status' => $status, 'updated_by' => $actor->id]);
            $content->refresh();
            $this->audit->record($actor, $event, $content, "{$description} {$content->getKey()}.", $before, [
                'moderation_status' => $content->moderation_status,
                'published_at' => $content->published_at?->toIso8601String(),
            ], ['reason' => trim($reason)]);

            return $content;
        });
    }
}
