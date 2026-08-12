<?php

namespace App\Enums;

enum BookingStatus: string
{
    case Enquiry = 'enquiry';
    case Reserved = 'reserved';
    case AwaitingPayment = 'awaiting_payment';
    case Confirmed = 'confirmed';
    case CheckedIn = 'checked_in';
    case CheckedOut = 'checked_out';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case Refunded = 'refunded';
    case NoShow = 'no_show';

    public function blocksAvailability(): bool
    {
        return match ($this) {
            self::Reserved,
            self::AwaitingPayment,
            self::Confirmed,
            self::CheckedIn => true,
            self::Enquiry,
            self::CheckedOut,
            self::Completed,
            self::Cancelled,
            self::Refunded,
            self::NoShow => false,
        };
    }
}
