<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['business_id', 'property_id', 'slug', 'public_title', 'short_summary', 'public_description', 'check_in_time', 'check_out_time', 'instant_booking_enabled', 'minimum_advance_notice_hours', 'maximum_advance_booking_days', 'seo_title', 'seo_description', 'seo_keywords', 'seo_score', 'seo_score_details', 'listing_quality_score', 'listing_quality_details', 'publication_status', 'is_publication_eligible', 'publication_eligibility_details', 'eligibility_checked_at', 'published_by', 'published_at', 'unpublished_by', 'unpublished_at', 'suspension_reason', 'status', 'created_by', 'updated_by'])]
class PropertyMarketplaceListing extends Model
{
    use HasUuids, SoftDeletes;

    protected function casts(): array
    {
        return [
            'instant_booking_enabled' => 'boolean',
            'seo_keywords' => 'array',
            'seo_score' => 'integer',
            'seo_score_details' => 'array',
            'listing_quality_score' => 'integer',
            'listing_quality_details' => 'array',
            'is_publication_eligible' => 'boolean',
            'publication_eligibility_details' => 'array',
            'eligibility_checked_at' => 'datetime',
            'published_at' => 'datetime',
            'unpublished_at' => 'datetime',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'published_by');
    }

    public function unpublisher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'unpublished_by');
    }
}
