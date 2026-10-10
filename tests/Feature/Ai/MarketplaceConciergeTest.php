<?php

namespace Tests\Feature\Ai;

use App\Models\PlatformSetting;
use Database\Seeders\ServicedApartmentMarketplaceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MarketplaceConciergeTest extends TestCase
{
    use RefreshDatabase;

    public function test_concierge_uses_published_listing_context_without_storing_provider_input(): void
    {
        $this->seed(ServicedApartmentMarketplaceSeeder::class);
        $this->setting('ai.enabled', true, 'boolean');
        $this->setting('ai.openai_api_key', 'openai-server-key', 'secret');
        $this->setting('ai.openai_model', 'gpt-5-mini');

        Http::fake(function (Request $request) {
            $this->assertSame('https://api.openai.com/v1/responses', $request->url());
            $this->assertSame('Bearer openai-server-key', $request->header('Authorization')[0] ?? null);
            $this->assertFalse($request['store']);
            $this->assertStringContainsString('Admiralty Waterfront Residence', $request['input']);

            return Http::response(['output' => [[
                'content' => [['type' => 'output_text', 'text' => 'Admiralty Waterfront Residence matches your request.']],
            ]]]);
        });

        $this->postJson(route('ai.marketplace-concierge'), ['question' => 'Which published stay is in Lekki?'])
            ->assertOk()
            ->assertJson(['answer' => 'Admiralty Waterfront Residence matches your request.']);
    }

    public function test_disabled_concierge_returns_a_safe_message_without_calling_provider(): void
    {
        Http::fake();

        $this->postJson(route('ai.marketplace-concierge'), ['question' => 'Find a quiet stay'])
            ->assertStatus(503)
            ->assertJsonStructure(['message']);
        Http::assertNothingSent();
    }

    private function setting(string $key, mixed $value, string $type = 'string'): void
    {
        [$group] = explode('.', $key, 2);
        PlatformSetting::query()->create([
            'group_key' => $group,
            'key' => $key,
            'value' => $value,
            'value_type' => $type,
            'label' => $key,
        ]);
    }
}
