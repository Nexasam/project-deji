<?php

namespace App\Models;

use App\Enums\BusinessOnboardingStatus;
use App\Enums\BusinessStatus;
use App\Enums\BusinessVerificationStatus;
use Database\Factories\BusinessFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'name',
    'description',
    'logo_disk',
    'logo_path',
    'website_url',
    'social_links',
    'registration_number',
    'country_code',
    'address',
    'primary_contact_name',
    'email',
    'phone_number',
    'tax_information',
    'business_type',
    'timezone',
    'currency',
    'subscription_plan',
    'onboarding_status',
    'onboarding_started_at',
    'onboarding_completed_at',
    'verification_status',
    'status',
])]
class Business extends Model
{
    /** @use HasFactory<BusinessFactory> */
    use HasFactory, HasUuids, SoftDeletes;

    protected function casts(): array
    {
        return [
            'address' => 'array',
            'social_links' => 'array',
            'tax_information' => 'encrypted:array',
            'verification_status' => BusinessVerificationStatus::class,
            'status' => BusinessStatus::class,
            'onboarding_status' => BusinessOnboardingStatus::class,
            'onboarding_started_at' => 'datetime',
            'onboarding_completed_at' => 'datetime',
        ];
    }

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(BusinessMembership::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    public function operationalTasks(): HasMany
    {
        return $this->hasMany(OperationalTask::class);
    }

    public function workflowTemplates(): HasMany
    {
        return $this->hasMany(WorkflowTemplate::class);
    }

    public function inventoryItems(): HasMany
    {
        return $this->hasMany(InventoryItem::class);
    }

    public function inventoryLocations(): HasMany
    {
        return $this->hasMany(InventoryLocation::class);
    }

    public function approvalWorkflows(): HasMany
    {
        return $this->hasMany(ApprovalWorkflow::class);
    }

    public function approvalRequests(): HasMany
    {
        return $this->hasMany(ApprovalRequest::class);
    }

    public function retentionPolicies(): HasMany
    {
        return $this->hasMany(DataRetentionPolicy::class);
    }

    public function dataSubjectRequests(): HasMany
    {
        return $this->hasMany(DataSubjectRequest::class);
    }

    public function aiSettings(): HasOne
    {
        return $this->hasOne(AiBusinessSetting::class);
    }

    public function aiConversations(): HasMany
    {
        return $this->hasMany(AiConversation::class);
    }

    public function aiPredictions(): HasMany
    {
        return $this->hasMany(AiPredictionSnapshot::class);
    }

    public function aiKnowledgeSources(): HasMany
    {
        return $this->hasMany(AiKnowledgeSource::class);
    }

    public function suppliers(): HasMany
    {
        return $this->hasMany(Supplier::class);
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function auditEvents(): HasMany
    {
        return $this->hasMany(AuditEvent::class);
    }

    public function customRoles(): HasMany
    {
        return $this->hasMany(Role::class);
    }

    public function passwordPolicies(): HasMany
    {
        return $this->hasMany(PasswordPolicy::class);
    }

    public function impersonationSessions(): HasMany
    {
        return $this->hasMany(ImpersonationSession::class);
    }

    public function onboardingSteps(): HasMany
    {
        return $this->hasMany(BusinessOnboardingStep::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(BusinessSubscription::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function domainEvents(): HasMany
    {
        return $this->hasMany(DomainEvent::class);
    }

    public function workspacePreferences(): HasMany
    {
        return $this->hasMany(UserWorkspacePreference::class);
    }

    public function dashboardBriefings(): HasMany
    {
        return $this->hasMany(DashboardBriefing::class);
    }

    public function healthSnapshots(): HasMany
    {
        return $this->hasMany(BusinessHealthSnapshot::class);
    }

    public function aiRecommendations(): HasMany
    {
        return $this->hasMany(AiRecommendation::class);
    }

    public function propertyStaffAssignments(): HasMany
    {
        return $this->hasMany(PropertyStaffAssignment::class);
    }

    public function propertyMarketplaceListings(): HasMany
    {
        return $this->hasMany(PropertyMarketplaceListing::class);
    }

    public function propertyPricingRules(): HasMany
    {
        return $this->hasMany(PropertyPricingRule::class);
    }

    public function propertyPromotions(): HasMany
    {
        return $this->hasMany(PropertyPromotion::class);
    }

    public function propertyHealthSnapshots(): HasMany
    {
        return $this->hasMany(PropertyHealthSnapshot::class);
    }

    public function automationSettings(): HasMany
    {
        return $this->hasMany(BusinessAutomationSetting::class);
    }

    public function availabilityDays(): HasMany
    {
        return $this->hasMany(PropertyAvailabilityDay::class);
    }

    public function externalCalendarSyncRuns(): HasMany
    {
        return $this->hasMany(ExternalCalendarSyncRun::class);
    }

    public function financialAccounts(): HasMany
    {
        return $this->hasMany(FinancialAccount::class);
    }

    public function financialTransactions(): HasMany
    {
        return $this->hasMany(FinancialTransaction::class);
    }

    public function revenueEntries(): HasMany
    {
        return $this->hasMany(RevenueEntry::class);
    }

    public function refundRequests(): HasMany
    {
        return $this->hasMany(RefundRequest::class);
    }

    public function financialForecasts(): HasMany
    {
        return $this->hasMany(FinancialForecastSnapshot::class);
    }
}
