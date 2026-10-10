<?php

namespace App\Services\Property;

use App\Models\Property;
use App\Models\PropertyMedia;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;
use Illuminate\Validation\ValidationException;

final class StorePropertyMediaService
{
    /** @param list<UploadedFile> $files */
    public function store(Property $property, User $actor, array $files): void
    {
        $storedPaths = [];
        $disk = 'platform_media';

        try {
            DB::transaction(function () use ($property, $actor, $files, &$storedPaths, $disk): void {
                $nextSortOrder = ((int) $property->media()->withTrashed()->max('sort_order')) + 1;
                $hasPrimaryImage = $property->media()
                    ->where('media_type', 'image')
                    ->where('is_primary', true)
                    ->exists();

                foreach ($files as $file) {
                    $path = $file->store(
                        "businesses/{$property->business_id}/properties/{$property->id}/media",
                        $disk,
                    );

                    if ($path === false) {
                        throw new RuntimeException('The media file could not be stored.');
                    }

                    $storedPaths[] = $path;
                    $mediaType = str_starts_with((string) $file->getMimeType(), 'image/') ? 'image' : 'video';
                    $isPrimary = $mediaType === 'image' && ! $hasPrimaryImage;

                    PropertyMedia::query()->create([
                        'business_id' => $property->business_id,
                        'property_id' => $property->id,
                        'media_type' => $mediaType,
                        'storage_disk' => $disk,
                        'storage_path' => $path,
                        'external_url' => Storage::disk($disk)->url($path),
                        'title' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                        'alt_text' => $mediaType === 'image' ? $property->name : null,
                        'sort_order' => $nextSortOrder++,
                        'is_primary' => $isPrimary,
                        'status' => 'active',
                    ]);

                    $hasPrimaryImage = $hasPrimaryImage || $isPrimary;
                }

                if ($files !== []) {
                    $property->forceFill([
                        'media_completed_at' => now(),
                        'updated_by' => $actor->id,
                    ])->save();
                }
            });
        } catch (Throwable $exception) {
            foreach ($storedPaths as $path) {
                Storage::disk($disk)->delete($path);
            }

            throw $exception;
        }
    }

    public function storeYouTube(Property $property, User $actor, ?string $url): void
    {
        if (blank($url)) return;
        $url = trim((string) $url);
        if (preg_match('~^(?:https?://)?(?:www\.|m\.)?(?:youtube\.com/(?:watch\?(?:[^#]*&)?v=|shorts/|embed/)|youtu\.be/)([A-Za-z0-9_-]{11})(?:[?&#/].*)?$~i', $url, $matches) !== 1) {
            throw ValidationException::withMessages(['youtube_url' => 'Enter a valid YouTube video link, for example https://youtu.be/VIDEO_ID.']);
        }
        $canonical = 'https://www.youtube.com/watch?v='.$matches[1];
        DB::transaction(function () use ($property, $actor, $canonical): void {
            $video = $property->media()->where('media_type', 'video')->whereNotNull('external_url')->first() ?? new PropertyMedia();
            $video->fill([
                'business_id' => $property->business_id,
                'property_id' => $property->id,
                'media_type' => 'video',
                'storage_disk' => null,
                'storage_path' => null,
                'external_url' => $canonical,
                'title' => 'Property walkthrough',
                'alt_text' => null,
                'sort_order' => $video->exists ? $video->sort_order : ((int) $property->media()->withTrashed()->max('sort_order')) + 1,
                'is_primary' => false,
                'status' => 'active',
            ])->save();
            $property->forceFill(['media_completed_at' => now(), 'updated_by' => $actor->id])->save();
        });
    }
}
