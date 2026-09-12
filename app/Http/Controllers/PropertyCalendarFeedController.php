<?php

namespace App\Http\Controllers;

use App\Models\PropertyCalendarExport;
use App\Services\Calendar\BuildPropertyCalendarFeed;
use Illuminate\Http\Response;

class PropertyCalendarFeedController extends Controller
{
    public function __invoke(PropertyCalendarExport $export, string $token, BuildPropertyCalendarFeed $builder): Response
    {
        abort_unless($export->active_key === 'active' && $export->status === 'active' && ! $export->revoked_at && hash_equals($export->token_hash, hash('sha256', $token)), 404);
        $export->forceFill(['last_accessed_at' => now()])->save();
        return response($builder->build($export->property), 200, [
            'Content-Type' => 'text/calendar; charset=UTF-8',
            'Content-Disposition' => 'inline; filename="verified-shortlet-'.$export->property_id.'.ics"',
            'Cache-Control' => 'private, max-age=300',
        ]);
    }
}
