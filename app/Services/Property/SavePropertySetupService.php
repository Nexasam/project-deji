<?php

namespace App\Services\Property;

use App\Models\Asset;
use App\Models\Document;
use App\Models\DocumentVersion;
use App\Models\Property;
use App\Models\PropertyCleaningSchedule;
use App\Models\PropertyHouseRule;
use App\Models\PropertyMarketplaceListing;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class SavePropertySetupService
{
    /** @param list<string> $rules */
    public function houseRules(Property $property, User $actor, array $rules): void
    {
        DB::transaction(function () use ($property, $rules): void {
            $property->houseRules()->delete();
            foreach ($rules as $index => $rule) PropertyHouseRule::query()->create(['business_id' => $property->business_id, 'property_id' => $property->id, 'name' => Str::limit($rule, 255, ''), 'description' => $rule, 'is_mandatory' => true, 'sort_order' => $index, 'status' => 'active']);
        });
    }

    public function operations(Property $property, array $data): void
    {
        PropertyCleaningSchedule::query()->updateOrCreate(['property_id' => $property->id, 'name' => 'Default cleaning schedule'], ['business_id' => $property->business_id, 'frequency' => $data['frequency'], 'preferred_start_time' => $data['preferred_start_time'] ?? null, 'instructions' => $data['instructions'] ?? null, 'status' => 'active']);
    }

    public function asset(Property $property, array $data): void
    {
        Asset::query()->create(array_merge($data, ['business_id' => $property->business_id, 'property_id' => $property->id, 'asset_code' => 'AST-'.Str::upper(Str::random(10)), 'qr_identifier' => (string) Str::uuid(), 'currency' => $property->pricing_currency, 'status' => 'active']));
    }

    public function document(Property $property, User $actor, UploadedFile $file, array $data): void
    {
        DB::transaction(function () use ($property, $actor, $file, $data): void {
            $path = $file->store("businesses/{$property->business_id}/properties/{$property->id}/documents", 'public');
            $document = Document::query()->create(['business_id' => $property->business_id, 'owner_type' => Property::class, 'owner_id' => $property->id, 'title' => $data['title'], 'category' => $data['category'], 'confidentiality' => 'internal', 'verification_status' => 'unverified', 'current_version_number' => 1, 'status' => 'active']);
            DocumentVersion::query()->create(['business_id' => $property->business_id, 'document_id' => $document->id, 'version_number' => 1, 'storage_disk' => 'public', 'storage_path' => $path, 'original_name' => $file->getClientOriginalName(), 'mime_type' => $file->getMimeType(), 'size_bytes' => $file->getSize(), 'checksum' => hash_file('sha256', $file->getRealPath()), 'uploaded_by' => $actor->id, 'status' => 'active']);
        });
    }

    public function marketplace(Property $property, User $actor, array $data): void
    {
        PropertyMarketplaceListing::query()->updateOrCreate(['property_id' => $property->id], array_merge($data, ['business_id' => $property->business_id, 'slug' => Str::slug($data['public_title']).'-'.Str::lower(Str::random(6)), 'publication_status' => 'draft', 'status' => 'active', 'created_by' => $actor->id, 'updated_by' => $actor->id]));
    }
}
