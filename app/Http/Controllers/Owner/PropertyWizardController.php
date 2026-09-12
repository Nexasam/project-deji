<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Amenity;
use App\Models\Property;
use App\Models\PropertyLifecycleEvent;
use App\Models\PropertyPromotion;
use App\Models\PropertySetupStep;
use App\Services\Property\SavePropertySetupService;
use App\Services\Property\StorePropertyMediaService;
use App\Services\Property\SyncPropertyAmenitiesService;
use App\Services\Property\SyncPropertyAssetsService;
use App\Services\Property\SyncPropertyChannelsService;
use App\Services\Calendar\ManageExternalCalendarConnection;
use App\Support\ActiveBusinessContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PropertyWizardController extends Controller
{
    public const STEPS = ['property-kind', 'location', 'basics', 'amenities', 'media', 'assets', 'documents', 'name-price', 'channels', 'review'];

    public const SKIPPABLE = [6, 7, 9];

    public function entry(): View
    {
        return view('property-add-step1');
    }

    public function start(Request $request, ActiveBusinessContext $context): RedirectResponse
    {
        $request->validate(['property_kind' => ['required', 'in:brand_new']]);
        $property = DB::transaction(function () use ($request, $context): Property {
            $property = Property::query()->create([
                'business_id' => $context->business->id,
                'name' => 'Untitled property',
                'code' => 'DRAFT-'.Str::upper(Str::random(10)),
                'property_type' => 'apartment',
                'capacity' => 1, 'bedrooms' => 0, 'bathrooms' => 0,
                'pricing_currency' => $context->business->currency,
                'verification_status' => 'unverified', 'maintenance_status' => 'not_required',
                'publication_status' => 'draft', 'readiness_status' => 'not_ready',
                'operational_status' => 'unavailable', 'status' => 'active',
                'created_by' => $request->user()->id, 'updated_by' => $request->user()->id,
            ]);
            foreach (self::STEPS as $index => $key) {
                $this->mark($property, $key, $request, $index === 0 ? 'completed' : 'pending');
            }

            return $property;
        });

        return $this->toStep($property, 2);
    }

    public function show(ActiveBusinessContext $context, string $property, int $step): View|RedirectResponse
    {
        abort_unless($step >= 2 && $step <= 10, 404);
        $draft = $this->draft($context, $property);
        $view = match ($step) {
            2 => 'property-add-step2',3 => 'property-add-step3',4 => 'property-add-step4',5 => 'property-add-step5',6 => 'property-add-step6',7 => 'property-add-step7',8 => 'property-add-step8',9 => 'property-add-channels',10 => 'property-add-step9'
        };

        return view($view, $this->data($context, $draft));
    }

    public function store(Request $request, ActiveBusinessContext $context, string $property, int $step, SyncPropertyAmenitiesService $amenities, StorePropertyMediaService $media, SavePropertySetupService $setup, SyncPropertyAssetsService $assets, SyncPropertyChannelsService $channels, ManageExternalCalendarConnection $calendars): RedirectResponse
    {
        abort_unless($step >= 2 && $step <= 9, 404);
        $draft = $this->draft($context, $property);
        match ($step) {
            2 => $this->location($request, $draft),
            3 => $this->basics($request, $draft),
            4 => $this->amenities($request, $draft, $amenities),
            5 => $this->media($request, $draft, $media),
            6 => $this->assets($request, $draft, $assets),
            7 => $this->documents($request, $draft, $setup),
            8 => $this->namePrice($request, $draft),
            9 => $this->channels($request, $draft, $channels, $calendars),
        };
        $this->mark($draft, self::STEPS[$step - 1], $request, 'completed');

        return $this->toStep($draft, $step + 1);
    }

    public function skip(Request $request, ActiveBusinessContext $context, string $property, int $step): RedirectResponse
    {
        abort_unless(in_array($step, self::SKIPPABLE, true), 422);
        $draft = $this->draft($context, $property);
        $this->mark($draft, self::STEPS[$step - 1], $request, 'skipped');

        return $this->toStep($draft, $step + 1);
    }

    public function resume(ActiveBusinessContext $context, string $property): RedirectResponse
    {
        $draft = $this->draft($context, $property);
        $pending = $draft->setupSteps()->whereIn('state', ['pending', 'in_progress'])->orderBy('sort_order')->first();
        $step = $pending ? array_search($pending->step_key, self::STEPS, true) + 1 : 10;

        return $step <= 1 ? $this->toStep($draft, 2) : $this->toStep($draft, $step);
    }

    public function submit(Request $request, ActiveBusinessContext $context, string $property): RedirectResponse
    {
        $draft = $context->business->properties()->whereKey($property)->firstOrFail();
        if ($draft->publication_status->value === 'pending') {
            return redirect()->route('owner.properties.wizard.success', $draft);
        }
        $errors = [];
        if (! $draft->address || ! $draft->booking_mode) {
            $errors[] = 'Complete location and property basics.';
        }
        if ($draft->name === 'Untitled property' || ! $draft->default_nightly_price) {
            $errors[] = 'Add a property name and nightly price.';
        }
        if (! $draft->media()->where('media_type', 'image')->exists()) {
            $errors[] = 'Upload at least one property photo.';
        }
        if ($errors) {
            return back()->withErrors(['readiness' => implode(' ', $errors)]);
        }
        DB::transaction(function () use ($request, $draft): void {
            $draft->update(['publication_status' => 'pending', 'verification_status' => 'pending', 'readiness_status' => 'ready', 'verification_submitted_at' => now(), 'updated_by' => $request->user()->id]);
            PropertyLifecycleEvent::query()->firstOrCreate(['property_id' => $draft->id, 'event_type' => 'marketplace_verification_submitted'], ['business_id' => $draft->business_id, 'previous_state' => 'draft', 'new_state' => 'pending', 'occurred_at' => now(), 'status' => 'active', 'created_by' => $request->user()->id, 'updated_by' => $request->user()->id]);
            $this->mark($draft, 'review', $request, 'completed');
        });

        return redirect()->route('owner.properties.wizard.success', $draft);
    }

    public function success(ActiveBusinessContext $context, string $property): View
    {
        $record = $context->business->properties()->whereKey($property)->whereIn('publication_status', ['pending', 'published'])->firstOrFail();

        return view('property-add-success', ['property' => $record, 'business' => $context->business]);
    }

    public function destroyMedia(ActiveBusinessContext $context, string $property, string $media): RedirectResponse
    {
        $draft = $this->draft($context, $property);
        $record = $draft->media()->whereKey($media)->firstOrFail();
        Storage::disk($record->storage_disk)->delete($record->storage_path);
        $record->delete();

        if ($record->is_primary) {
            $draft->media()->where('media_type', 'image')->orderBy('sort_order')->first()?->update(['is_primary' => true]);
        }

        return $this->toStep($draft, 5)->with('status', 'Media removed.');
    }

    public function destroyDocument(ActiveBusinessContext $context, string $property, string $document): RedirectResponse
    {
        $draft = $this->draft($context, $property);
        $record = $draft->documents()->with('versions')->whereKey($document)->firstOrFail();
        foreach ($record->versions as $version) {
            Storage::disk($version->storage_disk)->delete($version->storage_path);
        }
        $record->delete();

        return $this->toStep($draft, 7)->with('status', 'Document removed.');
    }

    public function previewDocument(ActiveBusinessContext $context, string $property, string $document): StreamedResponse
    {
        $draft = $this->draft($context, $property);
        $record = $draft->documents()->with('versions')->whereKey($document)->firstOrFail();
        $version = $record->versions->sortByDesc('version_number')->firstOrFail();

        abort_unless(Storage::disk($version->storage_disk)->exists($version->storage_path), 404);

        return Storage::disk($version->storage_disk)->response(
            $version->storage_path,
            $version->original_name,
            ['Content-Type' => $version->mime_type, 'Content-Disposition' => 'inline; filename="'.basename($version->original_name).'"'],
        );
    }

    private function location(Request $r, Property $p): void
    {
        $d = $r->validate(['state' => 'required|string|max:120', 'city' => 'required|string|max:120', 'address_line' => 'required|string|max:500', 'latitude' => 'nullable|numeric|between:-90,90', 'longitude' => 'nullable|numeric|between:-180,180']);
        $p->update(['address' => ['state' => $d['state'], 'city' => $d['city'], 'line_1' => $d['address_line'], 'country_code' => $p->business->country_code], 'latitude' => $d['latitude'] ?? null, 'longitude' => $d['longitude'] ?? null, 'updated_by' => $r->user()->id]);
    }

    private function basics(Request $r, Property $p): void
    {
        $d = $r->validate(['property_type' => 'required|in:flat,duplex,apartment', 'booking_mode' => 'required|in:entire', 'capacity' => 'required|integer|min:1|max:1000', 'bedrooms' => 'required|integer|min:0|max:500', 'bathrooms' => 'required|numeric|min:0|max:500']);
        $p->update($d + ['updated_by' => $r->user()->id]);
    }

    private function amenities(Request $r, Property $p, SyncPropertyAmenitiesService $s): void
    {
        $d = $r->validate(['amenity_codes' => 'required|json']);
        $codes = json_decode($d['amenity_codes'], true) ?: [];
        $ids = Amenity::query()->whereIn('code', $codes)->pluck('id')->all();
        $s->sync($p, $r->user(), $ids);
    }

    private function media(Request $r, Property $p, StorePropertyMediaService $s): void
    {
        $r->validate(['media' => 'array', 'media.*' => 'file|mimes:jpg,jpeg,png,webp,mp4,mov|max:20480']);
        if ($r->hasFile('media')) {
            $s->store($p, $r->user(), $r->file('media'));
        } if (! $p->media()->where('media_type', 'image')->exists()) {
            throw ValidationException::withMessages(['media' => 'Upload at least one image.']);
        }
    }

    private function assets(Request $r, Property $p, SyncPropertyAssetsService $service): void
    {
        $d = $r->validate(['assets' => 'required|json']);
        $names = collect(json_decode($d['assets'], true) ?: [])->map(fn ($v) => trim((string) $v))->filter()->unique()->values();
        $service->sync($p, $names);
    }

    private function documents(Request $r, Property $p, SavePropertySetupService $s): void
    {
        $r->validate(['documents' => 'array', 'documents.*' => 'file|mimes:pdf,jpg,jpeg,png,doc,docx|max:20480']);
        foreach ($r->file('documents', []) as $file) {
            $s->document($p, $r->user(), $file, ['title' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME), 'category' => 'property_record']);
        }
    }

    private function namePrice(Request $r, Property $p): void
    {
        $d = $r->validate(['name' => 'required|string|max:255', 'description' => 'nullable|string|max:5000', 'default_nightly_price' => 'required|numeric|min:1', 'discount_percentage' => 'nullable|numeric|min:1|max:100', 'minimum_stay_nights' => 'nullable|required_with:discount_percentage|integer|min:2|max:365']);
        $p->update(collect($d)->only(['name', 'description', 'default_nightly_price'])->all() + ['information_completed_at' => now(), 'updated_by' => $r->user()->id]);
        $promotion = PropertyPromotion::withTrashed()->firstOrNew(['property_id' => $p->id, 'name' => 'Longer stay discount']);
        if (filled($d['discount_percentage'] ?? null)) {
            $promotion->fill(['business_id' => $p->business_id, 'promotion_type' => 'length_of_stay', 'discount_type' => 'percentage', 'discount_value' => $d['discount_percentage'], 'minimum_stay_nights' => $d['minimum_stay_nights'], 'currency' => $p->pricing_currency, 'publication_status' => 'draft', 'status' => 'active', 'created_by' => $promotion->created_by ?: $r->user()->id, 'updated_by' => $r->user()->id]);
            $promotion->save();
            if ($promotion->trashed()) {
                $promotion->restore();
            }
        } elseif ($promotion->exists) {
            $promotion->delete();
        }
    }

    private function channels(Request $r, Property $p, SyncPropertyChannelsService $service, ManageExternalCalendarConnection $calendars): void
    {
        $d = $r->validate([
            'channels' => 'array', 'channels.*' => 'required|in:airbnb,bookingcom,whatsapp',
            'calendar_urls' => 'array', 'calendar_urls.airbnb' => 'nullable|url:https|max:2048',
            'calendar_urls.bookingcom' => 'nullable|url:https|max:2048',
        ]);
        $service->sync($p, $r->user(), array_values(array_unique($d['channels'] ?? [])));
        foreach (['airbnb', 'bookingcom'] as $provider) {
            if (in_array($provider, $d['channels'] ?? [], true) && filled($d['calendar_urls'][$provider] ?? null)) {
                $calendars->save($p, $r->user(), $provider, $d['calendar_urls'][$provider]);
            }
        }
    }

    private function draft(ActiveBusinessContext $c, string $id): Property
    {
        return $c->business->properties()->whereKey($id)->where('publication_status', 'draft')->firstOrFail();
    }

    private function toStep(Property $p, int $step): RedirectResponse
    {
        return redirect()->route('owner.properties.wizard.step', ['property' => $p, 'step' => $step]);
    }

    private function mark(Property $p, string $key, Request $r, string $state): void
    {
        $i = array_search($key, self::STEPS, true);
        PropertySetupStep::query()->updateOrCreate(['property_id' => $p->id, 'step_key' => $key], ['business_id' => $p->business_id, 'sort_order' => $i + 1, 'is_required' => ! in_array($i + 1, self::SKIPPABLE, true), 'state' => $state, 'completed_at' => $state === 'completed' ? now() : null, 'skipped_at' => $state === 'skipped' ? now() : null, 'completed_by' => $state === 'completed' ? $r->user()->id : null, 'status' => 'active', 'created_by' => $r->user()->id, 'updated_by' => $r->user()->id]);
    }

    private function data(ActiveBusinessContext $c, Property $p): array
    {
        return ['business' => $c->business, 'property' => $p->load(['amenities', 'media', 'assets', 'documents.versions', 'channelConnections', 'promotions', 'externalCalendarConnections', 'calendarExports']), 'amenities' => Amenity::query()->where('status', 'active')->orderBy('category')->orderBy('name')->get(), 'selectedAmenityIds' => $p->amenities()->pluck('amenities.id')->all()];
    }
}
