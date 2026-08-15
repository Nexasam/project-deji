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

final class StorePropertyMediaService
{
    /** @param list<UploadedFile> $files */
    public function store(Property $property, User $actor, array $files): void
    {
        $storedPaths = [];

        try {
            DB::transaction(function () use ($property, $actor, $files, &$storedPaths): void {
                $nextSortOrder = ((int) $property->media()->withTrashed()->max('sort_order')) + 1;
                $hasPrimaryImage = $property->media()
                    ->where('media_type', 'image')
                    ->where('is_primary', true)
                    ->exists();

                foreach ($files as $file) {
                    $path = $file->store(
                        "businesses/{$property->business_id}/properties/{$property->id}/media",
                        'public',
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
                        'storage_disk' => 'public',
                        'storage_path' => $path,
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
                Storage::disk('public')->delete($path);
            }

            throw $exception;
        }
    }
}
