<?php

namespace App\Services\Identity;

use App\Services\Platform\PlatformSettings;
use Illuminate\Support\Facades\Http;
use RuntimeException;

final class DojahIdentityVerifier
{
    public function __construct(private readonly PlatformSettings $settings) {}

    public function verifyNin(string $nin): array
    {
        return $this->lookup('/api/v1/kyc/nin', ['nin' => preg_replace('/\D/', '', $nin)], 'NIN');
    }

    public function verifyBvn(string $bvn): array
    {
        return $this->lookup('/api/v1/kyc/bvn', ['bvn' => preg_replace('/\D/', '', $bvn)], 'BVN');
    }

    private function lookup(string $path, array $query, string $label): array
    {
        $environment = (string) $this->settings->get('identity.environment', 'test');
        $appId = trim((string) $this->settings->get('identity.dojah_app_id', ''));
        $privateKey = trim((string) $this->settings->get('identity.dojah_private_key', ''));
        if ($appId === '' || $privateKey === '') {
            if ($environment !== 'live') {
                return [
                    'provider' => 'platform-test',
                    'environment' => 'test',
                    'reference' => 'IDV-'.strtoupper(bin2hex(random_bytes(6))),
                ];
            }

            throw new RuntimeException('Identity verification is not configured. Please contact Verified Shortlet support.');
        }
        $base = $environment === 'live' ? 'https://api.dojah.io' : 'https://sandbox.dojah.io';
        $response = Http::acceptJson()->withHeaders(['AppId' => $appId, 'Authorization' => $privateKey])
            ->timeout(20)->retry(2, 250)->get($base.$path, $query)->throw()->json();
        $entity = data_get($response, 'entity');
        if (! is_array($entity) || $entity === []) {
            throw new RuntimeException("{$label} could not be verified with the identity provider.");
        }

        return ['provider' => 'dojah', 'environment' => $environment, 'reference' => data_get($response, 'reference_id') ?? data_get($response, 'reference')];
    }
}
