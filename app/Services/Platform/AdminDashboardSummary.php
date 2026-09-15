<?php

namespace App\Services\Platform;

use App\Models\AuditEvent;
use App\Models\BookingDispute;
use App\Models\Business;
use App\Models\ExternalCalendarConnection;
use App\Models\OperationalTask;
use App\Models\Property;
use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

final class AdminDashboardSummary
{
    /** @return array<string, mixed> */
    public function for(User $administrator): array
    {
        return [
            'businesses_awaiting_verification' => Business::query()->where('verification_status', 'pending')->count(),
            'suspended_businesses' => Business::query()->where('status', 'suspended')->count(),
            'properties_awaiting_review' => Property::query()->where('publication_status', 'pending')->count(),
            'reviews_requiring_attention' => Review::query()->whereIn('moderation_status', ['pending', 'hidden'])->count(),
            'active_disputes' => BookingDispute::query()->whereIn('dispute_status', ['open', 'under_review', 'awaiting_guest', 'awaiting_business'])->count(),
            'restricted_users' => User::query()->where(fn ($query) => $query
                ->where('status', 'suspended')
                ->orWhere('locked_until', '>', now()))->count(),
            'calendar_connections_requiring_attention' => ExternalCalendarConnection::query()
                ->where(fn ($query) => $query->where('sync_status', 'failed')->orWhere('status', 'inactive'))
                ->count(),
            'failed_jobs' => Schema::hasTable('failed_jobs') ? DB::table('failed_jobs')->count() : 0,
            'overdue_tasks' => OperationalTask::query()
                ->whereNotIn('status', ['completed', 'verified', 'cancelled'])
                ->where('due_at', '<', now())
                ->count(),
            'recent_audits' => AuditEvent::query()->with('actor')->latest('occurred_at')->limit(8)->get(),
        ];
    }
}
