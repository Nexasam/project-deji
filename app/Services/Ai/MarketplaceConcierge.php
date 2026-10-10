<?php

namespace App\Services\Ai;

use App\Models\Property;
use App\Services\Platform\PlatformSettings;
use Illuminate\Support\Facades\Http;
use RuntimeException;

final class MarketplaceConcierge
{
    public function __construct(private readonly PlatformSettings $settings) {}

    public function answer(string $question): string
    {
        if (! (bool) $this->settings->get('ai.enabled', false)) throw new RuntimeException('The stay concierge is unavailable.');
        $key = trim((string) $this->settings->get('ai.openai_api_key', ''));
        if ($key === '') throw new RuntimeException('The stay concierge is not configured.');

        $properties = Property::query()->with('marketplaceListing')->where('publication_status', 'published')->where('status', 'active')->orderBy('name')->limit(40)->get()->map(fn (Property $property) => [
            'name' => $property->marketplaceListing?->public_title ?: $property->name,
            'area' => collect([$property->city, $property->state])->filter()->join(', '),
            'nightly_price' => (float) $property->default_nightly_price,
            'currency' => $property->pricing_currency,
            'capacity' => $property->capacity,
            'summary' => $property->marketplaceListing?->short_summary,
            'categories' => $property->marketplaceListing?->stay_categories,
        ])->values()->all();

        $response = Http::acceptJson()->asJson()->withToken($key)->timeout(30)->retry(2, 300)->post('https://api.openai.com/v1/responses', [
            'model' => (string) $this->settings->get('ai.openai_model', 'gpt-5-mini'),
            'store' => false,
            'instructions' => 'You are the Verified Shortlet stay concierge. Answer only from the supplied published-listing data. Never invent availability, prices, amenities or policies. If the supplied data cannot answer, say so and recommend opening the listing or contacting support. Keep the answer under 120 words.',
            'input' => "Published listings:\n".json_encode($properties, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)."\n\nGuest question: {$question}",
        ])->throw()->json();

        $item = collect(data_get($response, 'output', []))->flatMap(fn ($output) => data_get($output, 'content', []))->first(fn ($content) => data_get($content, 'type') === 'output_text');
        $answer = trim((string) data_get($item, 'text', ''));
        if ($answer === '') throw new RuntimeException('The stay concierge returned no answer.');
        return $answer;
    }
}
