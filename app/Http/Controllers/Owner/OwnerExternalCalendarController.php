<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\StoreExternalCalendarConnectionRequest;
use App\Services\Calendar\ImportExternalCalendar;
use App\Services\Calendar\ManageExternalCalendarConnection;
use App\Support\ActiveBusinessContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OwnerExternalCalendarController extends Controller
{
    public function store(StoreExternalCalendarConnectionRequest $request, ActiveBusinessContext $context, ManageExternalCalendarConnection $manager, string $property): RedirectResponse
    {
        $record = $context->business->properties()->findOrFail($property);
        $manager->save($record, $request->user(), $request->string('provider'), $request->string('feed_url'));
        return back()->with('status', 'Calendar connection saved. Select Sync now to import reservations.');
    }

    public function sync(Request $request, ActiveBusinessContext $context, ImportExternalCalendar $importer, string $property, string $connection): RedirectResponse
    {
        $record = $context->business->properties()->findOrFail($property);
        $calendar = $record->externalCalendarConnections()->whereKey($connection)->where('status', 'active')->firstOrFail();
        try { $run = $importer->sync($calendar, 'manual', $request->user()); }
        catch (\Throwable $error) { return back()->withErrors(['calendar_sync' => 'Sync failed: '.$error->getMessage()]); }
        return back()->with('status', "Calendar synced: {$run->created_count} added, {$run->updated_count} updated.");
    }

    public function destroy(Request $request, ActiveBusinessContext $context, ManageExternalCalendarConnection $manager, string $property, string $connection): RedirectResponse
    {
        $record = $context->business->properties()->findOrFail($property);
        $calendar = $record->externalCalendarConnections()->whereKey($connection)->firstOrFail();
        $manager->disable($calendar, $request->user());
        return back()->with('status', 'Calendar connection disabled and its imported dates released.');
    }

    public function regenerate(Request $request, ActiveBusinessContext $context, ManageExternalCalendarConnection $manager, string $property): RedirectResponse
    {
        $record = $context->business->properties()->findOrFail($property);
        $manager->regenerateExport($record, $request->user());
        return back()->with('status', 'A new export URL was generated. Replace the old URL on connected platforms.');
    }
}
