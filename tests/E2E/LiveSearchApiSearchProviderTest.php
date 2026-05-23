<?php

declare(strict_types=1);

namespace Padosoft\LaravelAiSearchProviders\Tests\E2E;

use Padosoft\LaravelAiSearchProviders\Data\SearchProviderDefinition;
use Padosoft\LaravelAiSearchProviders\Data\SearchQueryData;
use Padosoft\LaravelAiSearchProviders\Providers\SearchApiSearchProvider;
use Padosoft\LaravelAiSearchProviders\Tests\Concerns\ReadsLocalEnv;
use Padosoft\LaravelAiSearchProviders\Tests\TestCase;

final class LiveSearchApiSearchProviderTest extends TestCase
{
    use ReadsLocalEnv;

    public function testLiveSearchApiImageSearchReturnsProductLikeResults(): void
    {
        $apiKey = $this->envValue('SEARCHAPI_API_KEY');

        if ($apiKey === null || $apiKey === '') {
            self::markTestSkipped('Set SEARCHAPI_API_KEY in .env to run the live SearchAPI.io test.');
        }

        $provider = new SearchApiSearchProvider(SearchProviderDefinition::fromArray([
            'code' => 'searchapi-live',
            'name' => 'SearchAPI.io Live',
            'driver' => 'searchapi',
            'base_url' => 'https://www.searchapi.io',
            'api_key' => $apiKey,
            'timeout_seconds' => 30,
            'is_active' => true,
        ]));

        $results = $provider->searchImages(SearchQueryData::fromArray([
            'query' => 'Nike Air Force 1 07 white sneaker product image',
            'limit' => 5,
        ]));

        self::assertFalse($results->isEmpty(), 'SearchAPI.io returned no image results for the live smoke query.');

        $first = $results->first();
        self::assertNotNull($first);
        self::assertNotSame('', trim((string) $first->imageUrl));
        self::assertStringStartsWith('http', (string) $first->imageUrl);
    }
}
