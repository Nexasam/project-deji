<?php

namespace App\Notifications\Auth;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmailNotification extends VerifyEmail
{
    public function toMail($notifiable): MailMessage
    {
        $verificationUrl = $this->verificationUrl($notifiable);
        $firstName = str((string) $notifiable->name)->trim()->before(' ')->toString();

        return (new MailMessage)
            ->subject('Verify your email | Verified Shortlet')
            ->view(
                [
                    'html' => 'mail.auth.verify-email',
                    'text' => 'mail.auth.verify-email-text',
                ],
                [
                    'firstName' => $firstName !== '' ? $firstName : 'there',
                    'verificationUrl' => $verificationUrl,
                    'expiresInMinutes' => (int) config('auth.verification.expire', 60),
                    'homeUrl' => url('/'),
                ],
            );
    }
}
