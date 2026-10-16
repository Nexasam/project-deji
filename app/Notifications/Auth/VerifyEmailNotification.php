<?php

namespace App\Notifications\Auth;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class VerifyEmailNotification extends VerifyEmail
{
    protected function verificationUrl($notifiable): string
    {
        $signedPath = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ],
            absolute: false,
        );

        return url($signedPath);
    }

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
