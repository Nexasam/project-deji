<?php

namespace App\Services\Notifications;

use App\Services\Platform\PlatformSettings;
use Illuminate\Mail\MailManager;

final class PlatformMailConfigurator
{
    public function __construct(private readonly PlatformSettings $settings) {}

    public function apply(): void
    {
        if (! (bool) $this->settings->get('notifications.email_delivery_enabled', true)) {
            return;
        }

        $provider = strtolower(trim((string) $this->settings->get('notifications.mail_provider', 'custom')));
        $host = trim((string) $this->settings->get('notifications.mail_host', ''));
        $port = (int) $this->settings->get('notifications.mail_port', 587);
        $scheme = strtolower(trim((string) $this->settings->get('notifications.mail_scheme', 'smtp')));

        if ($provider === 'gmail') {
            $host = 'smtp.gmail.com';
        } elseif ($provider === 'brevo') {
            $host = 'smtp-relay.brevo.com';
            $port = 587;
            $scheme = 'smtp';
        }

        if ($host === '') {
            return;
        }

        $scheme = match ($scheme) {
            'ssl' => 'smtps',
            'tls' => 'smtp',
            default => in_array($scheme, ['smtp', 'smtps'], true) ? $scheme : 'smtp',
        };

        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.transport' => 'smtp',
            'mail.mailers.smtp.host' => $host,
            'mail.mailers.smtp.port' => $port,
            'mail.mailers.smtp.scheme' => $scheme,
            'mail.mailers.smtp.username' => $this->settings->get('notifications.mail_username'),
            'mail.mailers.smtp.password' => $this->settings->get('notifications.mail_password'),
            'mail.from.address' => $this->settings->get('notifications.mail_from_address'),
            'mail.from.name' => $this->settings->get('notifications.mail_from_name', 'Verified Shortlet'),
        ]);

        app(MailManager::class)->purge('smtp');
    }
}
