<?php

namespace App\Models;

use App\Enums\PropertyMaintenanceStatus;
use App\Enums\PropertyOperationalStatus;
use App\Enums\PropertyPublicationStatus;
use App\Enums\PropertyReadinessStatus;
use App\Enums\PropertyStatus;
use App\Enums\PropertyVerificationStatus;
use Database\Factories\PropertyFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'business_id',
    'name',
    'code',
    'address',
    'latitude',
    'longitude',
    'property_type',
    'booking_mode',
    'capacity',
    'bedrooms',
    'beds',
    'bathrooms',
    'floor_area_sqm',
    'description',
    'default_nightly_price',
    'pricing_currency',
    'verification_status',
    'maintenance_status',
    'publication_status',
    'readiness_status',
    'operational_status',
    'operational_status_updated_at',
    'information_completed_at',
    'media_completed_at',
    'verification_submitted_at',
    'verified_at',
    'verified_by',
    'published_at',
    'published_by',
    'archived_at',
    'owner_name',
    'manager_name',
    'status',
    'created_by',
    'updated_by',
])]
class Property extends Model
{
    /** @use HasFactory<PropertyFactory> */
    use HasFactory, HasUuids, SoftDeletes;

    protected function casts(): array
    {
        return [
            'address' => 'array',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'capacity' => 'integer',
            'bedrooms' => 'integer',
            'beds' => 'integer',
            'bathrooms' => 'decimal:1',
            'floor_area_sqm' => 'decimal:2',
            'default_nightly_price' => 'decimal:4',
            'verification_status' => PropertyVerificationStatus::class,
            'maintenance_status' => PropertyMaintenanceStatus::class,
            'publication_status' => PropertyPublicationStatus::class,
            'readiness_status' => PropertyReadinessStatus::class,
            'operational_status' => PropertyOperationalStatus::class,
            'operational_status_updated_at' => 'datetime',
            'information_completed_at' => 'datetime',
            'media_completed_at' => 'datetime',
            'verification_submitted_at' => 'datetime',
            'verified_at' => 'datetime',
            'published_at' => 'datetime',
            'archived_at' => 'datetime',
            'status' => PropertyStatus::class,
        ];
    }

    public function channelConnections(): HasMany
    {
        return $this->hasMany(PropertyChannelConnection::class);
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class, 'property_amenities')
            ->withPivot(['id', 'business_id', 'details', 'sort_order', 'status'])
            ->withTimestamps();
    }

    public function setupSteps(): HasMany
    {
        return $this->hasMany(PropertySetupStep::class);
    }

    public function amenityAssignments(): HasMany
    {
        return $this->hasMany(PropertyAmenity::class);
    }

    public function houseRules(): HasMany
    {
        return $this->hasMany(PropertyHouseRule::class);
    }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'owner');
    }

    public function media(): HasMany
    {
        return $this->hasMany(PropertyMedia::class);
    }

    public function cleaningSchedules(): HasMany
    {
        return $this->hasMany(PropertyCleaningSchedule::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function operationalTasks(): HasMany
    {
        return $this->hasMany(OperationalTask::class);
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }

    public function lifecycleEvents(): HasMany
    {
        return $this->hasMany(PropertyLifecycleEvent::class);
    }

    public function availabilityBlocks(): HasMany
    {
        return $this->hasMany(PropertyAvailabilityBlock::class);
    }

    public function inspections(): HasMany
    {
        return $this->hasMany(Inspection::class);
    }

    public function maintenanceIssues(): HasMany
    {
        return $this->hasMany(MaintenanceIssue::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function staffAssignments(): HasMany
    {
        return $this->hasMany(PropertyStaffAssignment::class);
    }

    public function marketplaceListing(): HasOne
    {
        return $this->hasOne(PropertyMarketplaceListing::class);
    }

    public function pricingRules(): HasMany
    {
        return $this->hasMany(PropertyPricingRule::class);
    }

    public function promotions(): HasMany
    {
        return $this->hasMany(PropertyPromotion::class);
    }

    public function healthSnapshots(): HasMany
    {
        return $this->hasMany(PropertyHealthSnapshot::class);
    }

    public function assetMedia(): HasMany
    {
        return $this->hasMany(AssetMedia::class);
    }

    public function reviewAnalyses(): HasMany
    {
        return $this->hasMany(ReviewAnalysis::class);
    }

    public function accessInstructions(): HasMany
    {
        return $this->hasMany(PropertyAccessInstruction::class);
    }

    public function bookingDisputes(): HasMany
    {
        return $this->hasMany(BookingDispute::class);
    }

    public function automationSettings(): HasMany
    {
        return $this->hasMany(BusinessAutomationSetting::class);
    }

    public function availabilityDays(): HasMany
    {
        return $this->hasMany(PropertyAvailabilityDay::class);
    }

    public function externalCalendarConnections(): HasMany
    {
        return $this->hasMany(ExternalCalendarConnection::class);
    }

    public function revenueEntries(): HasMany
    {
        return $this->hasMany(RevenueEntry::class);
    }

    public function financialTransactions(): HasMany
    {
        return $this->hasMany(FinancialTransaction::class);
    }

    public function financialForecasts(): HasMany
    {
        return $this->hasMany(FinancialForecastSnapshot::class);
    }
}
