<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\PropertyLifecycleEvent;
use App\Services\Property\PropertyPublicationReadinessService;
use App\Services\Property\PropertySetupWorkflow;
use App\Services\Property\SavePropertySetupService;
use App\Support\ActiveBusinessContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PropertySetupController extends Controller
{
    public function houseRules(ActiveBusinessContext $context, string $property): View { return $this->view($context, $property, 'house-rules'); }
    public function operations(ActiveBusinessContext $context, string $property): View { return $this->view($context, $property, 'operations'); }
    public function assets(ActiveBusinessContext $context, string $property): View { return $this->view($context, $property, 'assets'); }
    public function documents(ActiveBusinessContext $context, string $property): View { return $this->view($context, $property, 'documents'); }
    public function marketplace(ActiveBusinessContext $context, string $property): View { return $this->view($context, $property, 'marketplace'); }

    public function review(ActiveBusinessContext $context, PropertyPublicationReadinessService $readiness, PropertySetupWorkflow $workflow, string $property): View
    {
        $draft = $this->draft($context, $property);
        $workflow->initialize($draft, request()->user());
        return view('owner.properties.setup-step', $this->data($draft, 'review') + ['blockers' => $readiness->setupBlockers($draft), 'warnings' => $readiness->warnings($draft)]);
    }

    public function storeHouseRules(Request $request, ActiveBusinessContext $context, SavePropertySetupService $save, PropertySetupWorkflow $workflow, string $property): RedirectResponse
    {
        $data = $request->validate(['rules' => ['required', 'string', 'max:5000']]);
        $draft = $this->draft($context, $property);
        $rules = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $data['rules']))));
        $save->houseRules($draft, $request->user(), $rules);
        $workflow->complete($draft, 'house-rules', $request->user());
        return $this->next($draft, 'house-rules', $workflow, 'House rules saved.');
    }

    public function storeOperations(Request $request, ActiveBusinessContext $context, SavePropertySetupService $save, PropertySetupWorkflow $workflow, string $property): RedirectResponse
    {
        $data = $request->validate(['frequency' => ['required', 'in:between_stays,daily,weekly,monthly,custom'], 'preferred_start_time' => ['nullable', 'date_format:H:i'], 'instructions' => ['nullable', 'string', 'max:3000']]);
        $draft = $this->draft($context, $property);
        $save->operations($draft, $data);
        $workflow->complete($draft, 'operations', $request->user());
        return $this->next($draft, 'operations', $workflow, 'Operations saved.');
    }

    public function storeAssets(Request $request, ActiveBusinessContext $context, SavePropertySetupService $save, PropertySetupWorkflow $workflow, string $property): RedirectResponse
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:255'], 'category' => ['required', 'string', 'max:80'], 'serial_number' => ['nullable', 'string', 'max:191'], 'condition' => ['required', 'in:new,good,fair,poor,damaged'], 'replacement_value' => ['nullable', 'numeric', 'min:0']]);
        $draft = $this->draft($context, $property);
        $save->asset($draft, $data);
        $workflow->complete($draft, 'assets', $request->user());
        return $this->next($draft, 'assets', $workflow, 'Asset saved.');
    }

    public function storeDocuments(Request $request, ActiveBusinessContext $context, SavePropertySetupService $save, PropertySetupWorkflow $workflow, string $property): RedirectResponse
    {
        $data = $request->validate(['title' => ['required', 'string', 'max:255'], 'category' => ['required', 'string', 'max:80'], 'document' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:20480']]);
        $draft = $this->draft($context, $property);
        $save->document($draft, $request->user(), $request->file('document'), $data);
        $workflow->complete($draft, 'documents', $request->user());
        return $this->next($draft, 'documents', $workflow, 'Document saved.');
    }

    public function storeMarketplace(Request $request, ActiveBusinessContext $context, SavePropertySetupService $save, PropertySetupWorkflow $workflow, string $property): RedirectResponse
    {
        $data = $request->validate(['public_title' => ['required', 'string', 'max:255'], 'short_summary' => ['nullable', 'string', 'max:500'], 'public_description' => ['required', 'string', 'max:5000'], 'check_in_time' => ['required', 'date_format:H:i'], 'check_out_time' => ['required', 'date_format:H:i'], 'instant_booking_enabled' => ['sometimes', 'boolean']]);
        $data['instant_booking_enabled'] = $request->boolean('instant_booking_enabled');
        $draft = $this->draft($context, $property);
        $save->marketplace($draft, $request->user(), $data);
        $workflow->complete($draft, 'marketplace', $request->user());
        return $this->next($draft, 'marketplace', $workflow, 'Marketplace details saved.');
    }

    public function skip(Request $request, ActiveBusinessContext $context, PropertySetupWorkflow $workflow, string $property, string $step): RedirectResponse
    {
        abort_unless(array_key_exists($step, PropertySetupWorkflow::STEPS), 404);
        $draft = $this->draft($context, $property);
        $workflow->skip($draft, $step, $request->user());
        return $this->next($draft, $step, $workflow, 'Step skipped for now.');
    }

    public function complete(Request $request, ActiveBusinessContext $context, PropertyPublicationReadinessService $readiness, PropertySetupWorkflow $workflow, string $property): RedirectResponse
    {
        $draft = $this->draft($context, $property);
        $blockers = $readiness->setupBlockers($draft);
        if ($blockers !== []) return back()->withErrors(['readiness' => implode(' ', $blockers)]);

        $draft->update(['publication_status' => 'unpublished', 'readiness_status' => 'ready', 'operational_status' => 'available', 'operational_status_updated_at' => now(), 'updated_by' => $request->user()->id]);
        $workflow->complete($draft, 'review', $request->user());
        PropertyLifecycleEvent::query()->create(['business_id' => $draft->business_id, 'property_id' => $draft->id, 'event_type' => 'setup_completed', 'previous_state' => 'draft', 'new_state' => 'unpublished', 'occurred_at' => now(), 'status' => 'active', 'created_by' => $request->user()->id, 'updated_by' => $request->user()->id]);
        return redirect()->route('owner.dashboard')->with('status', "{$draft->name} is ready for internal business operations.");
    }

    public function submitMarketplace(Request $request, ActiveBusinessContext $context, PropertyPublicationReadinessService $readiness, string $property): RedirectResponse
    {
        $record = $context->business->properties()->whereKey($property)->where('publication_status', 'unpublished')->firstOrFail();
        $blockers = $readiness->marketplaceBlockers($record);
        if ($blockers !== []) return back()->withErrors(['marketplace' => implode(' ', $blockers)]);
        $record->update(['verification_status' => 'pending', 'verification_submitted_at' => now(), 'publication_status' => 'pending', 'updated_by' => $request->user()->id]);
        PropertyLifecycleEvent::query()->create(['business_id' => $record->business_id, 'property_id' => $record->id, 'event_type' => 'marketplace_verification_submitted', 'previous_state' => 'unpublished', 'new_state' => 'pending', 'occurred_at' => now(), 'status' => 'active', 'created_by' => $request->user()->id, 'updated_by' => $request->user()->id]);
        return back()->with('status', "{$record->name} was submitted to Project Nexus for marketplace verification.");
    }

    private function view(ActiveBusinessContext $context, string $property, string $step): View { return view('owner.properties.setup-step', $this->data($this->draft($context, $property), $step)); }
    private function draft(ActiveBusinessContext $context, string $id): Property { return $context->business->properties()->whereKey($id)->where('publication_status', 'draft')->firstOrFail(); }
    private function data(Property $property, string $step): array { return ['business' => $property->business, 'property' => $property->loadMissing(['houseRules', 'cleaningSchedules', 'assets', 'documents', 'marketplaceListing', 'setupSteps']), 'step' => $step, 'stepNumber' => array_search($step, array_keys(PropertySetupWorkflow::STEPS), true) + 1]; }
    private function next(Property $property, string $step, PropertySetupWorkflow $workflow, string $status): RedirectResponse { $next = $workflow->nextKey($step) ?? 'review'; return redirect()->route($workflow->routeName($next), $property)->with('status', $status); }
}
