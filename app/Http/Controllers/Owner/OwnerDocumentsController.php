<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Services\Access\BusinessPermissionService;
use App\Support\ActiveBusinessContext;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OwnerDocumentsController extends Controller
{
    public function __invoke(Request $request, ActiveBusinessContext $context, BusinessPermissionService $permissions): View
    {
        $propertyIds = $permissions->permittedPropertyIds($context);
        $documents = $context->business->documents()->with(['owner', 'versions' => fn ($q) => $q->latest('version_number')])
            ->where('status', 'active')
            ->when($request->filled('q'), fn ($q) => $q->where(fn ($query) => $query->where('title', 'like', '%'.$request->string('q').'%')->orWhere('category', 'like', '%'.$request->string('q').'%')))
            ->latest()->get();
        if ($propertyIds !== null) {
            $documents = $documents->filter(fn ($document) => $document->owner_type !== \App\Models\Property::class || in_array($document->owner_id, $propertyIds, true))->values();
        }

        return view('owner.documents', ['business' => $context->business, 'documents' => $documents, 'query' => $request->string('q')->toString()]);
    }
}
