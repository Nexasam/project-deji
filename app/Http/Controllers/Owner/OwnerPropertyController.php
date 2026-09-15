<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\StorePropertyMediaRequest;
use App\Http\Requests\Owner\StorePropertyRequest;
use App\Http\Requests\Owner\UpdatePropertyAmenitiesRequest;
use App\Models\Amenity;
use App\Models\Property;
use App\Services\Access\BusinessPermissionService;
use App\Services\Property\CreatePropertyService;
use App\Services\Property\PropertyLocationSummary;
use App\Services\Property\PropertySetupWorkflow;
use App\Services\Property\StorePropertyMediaService;
use App\Services\Property\SyncPropertyAmenitiesService;
use App\Services\Property\UpdatePropertyService;
use App\Support\ActiveBusinessContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OwnerPropertyController extends Controller
{
    public function index(Request $request, ActiveBusinessContext $context, BusinessPermissionService $permissions, PropertyLocationSummary $locationSummary): View
    {
        if ($request->query('view') === 'demo') {
            return view('properties', ['business' => $context->business]);
        }

        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'in:draft,pending,published,unpublished'],
            'layout' => ['nullable', 'in:grid,list'],
        ]);
        $filters = [
            'q' => trim($filters['q'] ?? ''),
            'status' => $filters['status'] ?? '',
            'layout' => $filters['layout'] ?? 'grid',
        ];
        $propertyQuery = $permissions->scopeProperties($context->business->properties(), $context);
        $locationProperties = (clone $propertyQuery)
            ->with(['staffAssignments' => fn ($query) => $query
                ->where('status', 'active')
                ->where('assignment_status', 'active')
                ->where('assignment_role', 'property_manager')
                ->orderByDesc('is_primary')
                ->with('employee.businessMembership.user')])
            ->get(['id', 'business_id', 'address']);
        $portfolioCount = $locationProperties->count();
        $properties = $propertyQuery
            ->when($filters['q'] !== '', fn ($query) => $query->where(function ($nested) use ($filters): void {
                $search = '%'.$filters['q'].'%';
                $nested->where('name', 'like', $search)
                    ->orWhere('code', 'like', $search)
                    ->orWhere('address', 'like', $search);
            }))
            ->when($filters['status'] !== '', fn ($query) => $query->where('publication_status', $filters['status']))
            ->with([
                'marketplaceListing',
                'media' => fn ($query) => $query
                    ->where('status', 'active')
                    ->where('media_type', 'image')
                    ->orderByDesc('is_primary')
                    ->orderBy('sort_order'),
            ])
            ->latest()
            ->get(['id', 'business_id', 'name', 'code', 'address', 'property_type', 'capacity', 'bedrooms', 'bathrooms', 'publication_status', 'verification_status', 'readiness_status', 'created_at']);

        return view('owner.properties.index', [
            'business' => $context->business,
            'properties' => $properties,
            'portfolioCount' => $portfolioCount,
            'locationSummary' => $locationSummary->build($context->business, $locationProperties),
            'filters' => $filters,
        ]);
    }

    public function create(ActiveBusinessContext $context): View
    {
        return view('owner.properties.create', [
            'business' => $context->business,
            'property' => null,
        ]);
    }

    public function show(ActiveBusinessContext $context, string $property): View
    {
        $record = $context->business->properties()
            ->with([
                'amenities' => fn ($query) => $query->orderBy('category')->orderBy('name'),
                'media' => fn ($query) => $query->where('status', 'active')->orderByDesc('is_primary')->orderBy('sort_order'),
                'marketplaceListing',
                'assets' => fn ($query) => $query->where('status', 'active')->orderBy('name'),
                'documents.versions',
                'channelConnections' => fn ($query) => $query->where('status', 'active'),
                'promotions' => fn ($query) => $query->where('status', 'active'),
                'externalCalendarConnections' => fn ($query) => $query->where('status', 'active')->with(['syncRuns' => fn ($runs) => $runs->latest()->limit(5)]),
                'calendarExports' => fn ($query) => $query->where('active_key', 'active'),
            ])
            ->whereKey($property)
            ->firstOrFail();

        return view('owner.properties.show', [
            'business' => $context->business,
            'property' => $record,
        ]);
    }

    public function store(
        StorePropertyRequest $request,
        ActiveBusinessContext $context,
        CreatePropertyService $service,
        PropertySetupWorkflow $workflow,
    ): RedirectResponse {
        $property = $service->create($context, $request->user(), $request->propertyAttributes());
        $workflow->initialize($property, $request->user());
        $workflow->complete($property, 'basics', $request->user());

        return redirect()->route('owner.properties.wizard.step', ['property' => $property, 'step' => 4])
            ->with('status', "{$property->name} basics were saved.");
    }

    public function resume(ActiveBusinessContext $context, PropertySetupWorkflow $workflow, string $property): RedirectResponse
    {
        $draft = $this->draftProperty($context, $property);

        return redirect()->route($workflow->routeName($workflow->resumeKey($draft)), $draft);
    }

    public function edit(ActiveBusinessContext $context, string $property): View
    {
        return view('owner.properties.create', [
            'business' => $context->business,
            'property' => $this->draftProperty($context, $property),
        ]);
    }

    public function update(
        StorePropertyRequest $request,
        ActiveBusinessContext $context,
        UpdatePropertyService $service,
        PropertySetupWorkflow $workflow,
        string $property,
    ): RedirectResponse {
        $draft = $this->draftProperty($context, $property);
        $service->update($draft, $request->user(), $request->propertyAttributes());
        $workflow->complete($draft, 'basics', $request->user());

        return redirect()->route('owner.properties.wizard.step', ['property' => $draft, 'step' => 4])
            ->with('status', "{$draft->name} basics were updated.");
    }

    public function amenities(ActiveBusinessContext $context, string $property): View
    {
        $draft = $this->draftProperty($context, $property)->load('amenityAssignments');

        return view('owner.properties.amenities', [
            'business' => $context->business,
            'property' => $draft,
            'amenities' => Amenity::query()
                ->where('status', 'active')
                ->orderBy('category')
                ->orderBy('name')
                ->get()
                ->groupBy('category'),
            'selectedAmenityIds' => $draft->amenityAssignments->pluck('amenity_id')->all(),
        ]);
    }

    public function updateAmenities(
        UpdatePropertyAmenitiesRequest $request,
        ActiveBusinessContext $context,
        SyncPropertyAmenitiesService $service,
        PropertySetupWorkflow $workflow,
        string $property,
    ): RedirectResponse {
        $draft = $this->draftProperty($context, $property);
        $service->sync($draft, $request->user(), $request->amenityIds());
        $workflow->complete($draft, 'amenities', $request->user());

        return redirect()->route('owner.properties.setup.media', $draft)
            ->with('status', "{$draft->name} amenities were saved.");
    }

    public function media(ActiveBusinessContext $context, string $property): View
    {
        $draft = $this->draftProperty($context, $property);

        return view('owner.properties.media', [
            'business' => $context->business,
            'property' => $draft,
            'mediaItems' => $draft->media()->orderBy('sort_order')->get(),
        ]);
    }

    public function storeMedia(
        StorePropertyMediaRequest $request,
        ActiveBusinessContext $context,
        StorePropertyMediaService $service,
        PropertySetupWorkflow $workflow,
        string $property,
    ): RedirectResponse {
        $draft = $this->draftProperty($context, $property);
        $service->store($draft, $request->user(), $request->mediaFiles());
        if (! $draft->media()->where('media_type', 'image')->exists()) {
            return back()->withErrors(['media' => 'Upload at least one image before continuing.']);
        }
        $workflow->complete($draft, 'media', $request->user());

        return redirect()->route('owner.properties.setup.house-rules', $draft)
            ->with('status', "{$draft->name} media were saved.");
    }

    public function destroyMedia(
        ActiveBusinessContext $context,
        string $property,
        string $media,
    ): RedirectResponse {
        $draft = $this->draftProperty($context, $property);
        $draft->media()->whereKey($media)->firstOrFail()->delete();

        return back()->with('status', 'Media item removed from the draft.');
    }

    private function draftProperty(ActiveBusinessContext $context, string $property): Property
    {
        return $context->business->properties()
            ->whereKey($property)
            ->where('publication_status', 'draft')
            ->firstOrFail();
    }
}
