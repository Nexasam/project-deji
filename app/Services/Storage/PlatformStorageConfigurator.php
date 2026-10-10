<?php

namespace App\Services\Storage;

use App\Services\Platform\PlatformSettings;
use Illuminate\Filesystem\FilesystemManager;

final class PlatformStorageConfigurator
{
    public function __construct(private readonly PlatformSettings $settings) {}

    public function apply(): void
    {
        $driver = (string) $this->settings->get('storage.media_driver', 'local');
        $configuration = $driver === 's3' ? [
            'driver' => 's3',
            'key' => $this->settings->get('storage.s3_key'),
            'secret' => $this->settings->get('storage.s3_secret'),
            'region' => $this->settings->get('storage.s3_region', 'us-east-1'),
            'bucket' => $this->settings->get('storage.s3_bucket'),
            'endpoint' => blank($endpoint = $this->settings->get('storage.s3_endpoint')) ? null : $endpoint,
            'url' => blank($url = $this->settings->get('storage.s3_url')) ? null : rtrim($url, '/'),
            'use_path_style_endpoint' => (bool) $this->settings->get('storage.s3_path_style', false),
            'visibility' => 'public', 'throw' => true,
        ] : [
            'driver' => 'local', 'root' => storage_path('app/public'),
            'url' => rtrim(config('app.url'), '/').'/storage',
            'visibility' => 'public', 'throw' => true,
        ];
        config(['filesystems.disks.platform_media' => $configuration]);
        app(FilesystemManager::class)->forgetDisk('platform_media');
    }
}
