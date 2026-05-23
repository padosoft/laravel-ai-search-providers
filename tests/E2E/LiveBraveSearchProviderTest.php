<?php

declare(strict_types=1);

namespace Padosoft\LaravelAiSearchProviders\Tests\E2E;

use Padosoft\LaravelAiSearchProviders\Data\SearchProviderDefinition;
use Padosoft\LaravelAiSearchProviders\Data\SearchQueryData;
use Padosoft\LaravelAiSearchProviders\Providers\BraveSearchProvider;
use Padosoft\LaravelAiSearchProviders\Tests\Concerns\ReadsLocalEnv;
use Padosoft\LaravelAiSearchProviders\Tests\TestCase;

final class LiveBraveSearchProviderTest extends TestCase
{
    use ReadsLocalEnv;

    public function testLiveBraveImageSearchReturnsProductLikeResults(): void
    {
        $apiKey = $this->envValue('BRAVE_SEARCH_API_KEY');

        if ($apiKey === null || $apiKey === '') {
            self::markTestSkipped('Set BRAVE_SEARCH_API_KEY in .env to run the live Brave Search test.');
        }

        $provider = new BraveSearchProvider(SearchProviderDefinition::fromArray([
            'code' => 'brave-live',
            'name' => 'Brave Live',
            'driver' => 'brave',
            'base_url' => 'https://api.search.brave.com',
            'api_key' => $apiKey,
            'timeout_seconds' => 30,
            'is_active' => true,
        ]));

        $results = $provider->searchImages(SearchQueryData::fromArray([
            'query' => 'Nike Air Force 1 07 white product image',
            'site' => 'nike.com',
            'limit' => 5,
        ]));

        self::assertFalse($results->isEmpty(), 'Brave returned no image results for the live smoke query.');

        $first = $results->first();
        self::assertNotNull($first);
        self::assertNotSame('', trim((string) $first->imageUrl));
        self::assertStringStartsWith('http', (string) $first->imageUrl);
    }
}
