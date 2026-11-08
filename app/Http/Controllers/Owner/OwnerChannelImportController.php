<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Services\Platform\PlatformSettings;
use App\Support\ActiveBusinessContext;
use Illuminate\View\View;

final class OwnerChannelImportController extends Controller
{
    public function __invoke(ActiveBusinessContext $context, PlatformSettings $settings): View
    {
        abort_unless((bool) $settings->get('channels.import_enabled', false), 404);
        abort_unless((bool) $context->business->channel_import_enabled, 403, 'Listing import is not enabled for this business.');

        return view('owner.channel-import', ['business' => $context->business, 'provider' => $settings->get('channels.provider', 'rentals_united')]);
    }
}
