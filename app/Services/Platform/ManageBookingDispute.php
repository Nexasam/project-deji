<?php

namespace App\Services\Platform;

use App\Models\Booking;
use App\Models\BookingDispute;
use App\Models\User;
use App\Services\PlatformPermissionService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class ManageBookingDispute
{
    private const TRANSITIONS = [
        'open' => ['under_review'],
        'under_review' => ['awaiting_guest', 'awaiting_business', 'resolved', 'rejected'],
        'awaiting_guest' => ['under_review'],
        'awaiting_business' => ['under_review'],
        'resolved' => ['closed'],
        'rejected' => ['closed'],
        'closed' => [],
    ];

    public function __construct(
        private readonly PlatformAudit $audit,
        private readonly PlatformPermissionService $permissions,
    ) {}

    /** @param array<string, mixed> $data */
    public function create(Booking $booking, User $actor, array $data): BookingDispute
    {
        $this->note($data['note'] ?? null);
        $this->validAssignee($data['assigned_to'] ?? null);

        return DB::transaction(function () use ($booking, $actor, $data): BookingDispute {
            $booking = Booking::query()->with(['business', 'property'])->lockForUpdate()->findOrFail($booking->id);
            $dispute = BookingDispute::query()->create([
                'business_id' => $booking->business_id,
                'property_id' => $booking->property_id,
                'booking_id' => $booking->id,
                'payment_id' => $data['payment_id'] ?? null,
                'reference' => $this->reference($booking),
                'dispute_type' => $data['dispute_type'],
                'opened_by_type' => 'platform_admin',
                'opened_by' => $actor->id,
                'description' => $data['description'],
                'priority' => $data['priority'],
                'disputed_amount' => $data['disputed_amount'] ?? null,
                'currency' => $booking->currency,
                'dispute_status' => 'open',
                'assigned_to' => $data['assigned_to'] ?? null,
                'due_at' => $data['due_at'] ?? null,
                'opened_at' => now(),
                'status' => 'active',
                'created_by' => $actor->id,
                'updated_by' => $actor->id,
            ]);
            $this->audit->record($actor, 'platform.dispute.created', $dispute, "Opened dispute {$dispute->reference} for booking {$booking->reference}.", [], $this->snapshot($dispute), ['reason' => trim($data['note'])]);

            return $dispute->fresh(['booking', 'property', 'assignee']);
        });
    }

    public function assign(BookingDispute $dispute, User $actor, string $assigneeId, string $note): BookingDispute
    {
        $this->note($note);
        $this->validAssignee($assigneeId);

        return DB::transaction(function () use ($dispute, $actor, $assigneeId, $note): BookingDispute {
            $dispute = BookingDispute::query()->lockForUpdate()->findOrFail($dispute->id);
            $before = $this->snapshot($dispute);
            $dispute->update(['assigned_to' => $assigneeId, 'updated_by' => $actor->id]);
            $dispute->refresh();
            $this->audit->record($actor, 'platform.dispute.assigned', $dispute, "Assigned dispute {$dispute->reference}.", $before, $this->snapshot($dispute), ['reason' => trim($note)]);

            return $dispute;
        });
    }

    /** @param array<string, mixed> $data */
    public function transition(BookingDispute $dispute, User $actor, array $data): BookingDispute
    {
        $this->note($data['note'] ?? null);

        return DB::transaction(function () use ($dispute, $actor, $data): BookingDispute {
            $dispute = BookingDispute::query()->lockForUpdate()->findOrFail($dispute->id);
            $from = $dispute->dispute_status->value;
            $to = $data['status'];
            if (! in_array($to, self::TRANSITIONS[$from] ?? [], true)) {
                throw ValidationException::withMessages(['status' => "A dispute cannot move from {$from} to {$to}."]);
            }
            if (in_array($to, ['resolved', 'rejected'], true) && mb_strlen(trim((string) ($data['resolution'] ?? ''))) < 10) {
                throw ValidationException::withMessages(['resolution' => 'Record a clear resolution of at least 10 characters.']);
            }

            $before = $this->snapshot($dispute);
            $changes = ['dispute_status' => $to, 'updated_by' => $actor->id];
            if (in_array($to, ['resolved', 'rejected'], true)) {
                $changes += [
                    'resolution' => trim($data['resolution']),
                    'approved_amount' => $data['approved_amount'] ?? null,
                    'resolved_by' => $actor->id,
                    'resolved_at' => now(),
                ];
            }
            if ($to === 'closed') {
                $changes['closed_at'] = now();
            }
            $dispute->update($changes);
            $dispute->refresh();
            $this->audit->record($actor, 'platform.dispute.transitioned', $dispute, "Moved dispute {$dispute->reference} from {$from} to {$to}.", $before, $this->snapshot($dispute), ['reason' => trim($data['note'])]);

            return $dispute;
        });
    }

    private function validAssignee(?string $assigneeId): void
    {
        if (! $assigneeId) {
            return;
        }
        $assignee = User::query()->findOrFail($assigneeId);
        if (! $this->permissions->allows($assignee, 'platform.dispute.manage')) {
            throw ValidationException::withMessages(['assigned_to' => 'Choose an active platform dispute administrator.']);
        }
    }

    private function note(?string $note): void
    {
        if (mb_strlen(trim((string) $note)) < 10) {
            throw ValidationException::withMessages(['note' => 'Add a case note of at least 10 characters.']);
        }
    }

    private function reference(Booking $booking): string
    {
        do {
            $reference = 'DSP-'.Str::upper(Str::random(10));
        } while (BookingDispute::query()->where('business_id', $booking->business_id)->where('reference', $reference)->exists());

        return $reference;
    }

    /** @return array<string, mixed> */
    private function snapshot(BookingDispute $dispute): array
    {
        return [
            'dispute_status' => $dispute->dispute_status->value,
            'priority' => $dispute->priority,
            'assigned_to' => $dispute->assigned_to,
            'due_at' => $dispute->due_at?->toIso8601String(),
            'resolution' => $dispute->resolution,
            'approved_amount' => $dispute->approved_amount,
        ];
    }
}
