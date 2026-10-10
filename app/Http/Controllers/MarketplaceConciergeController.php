<?php

namespace App\Http\Controllers;

use App\Services\Ai\MarketplaceConcierge;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

final class MarketplaceConciergeController extends Controller
{
    public function __invoke(Request $request, MarketplaceConcierge $concierge): JsonResponse
    {
        $data = $request->validate(['question' => ['required', 'string', 'min:3', 'max:500']]);
        try {
            return response()->json(['answer' => $concierge->answer($data['question'])]);
        } catch (Throwable $exception) {
            report($exception);
            return response()->json(['message' => 'The stay concierge is temporarily unavailable. Please browse the published stays or contact support.'], 503);
        }
    }
}
