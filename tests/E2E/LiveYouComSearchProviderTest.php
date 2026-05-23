<?php

declare(strict_types=1);

namespace Padosoft\LaravelAiSearchProviders\Tests\E2E;

use Padosoft\LaravelAiSearchProviders\Data\SearchProviderDefinition;
use Padosoft\LaravelAiSearchProviders\Data\SearchQueryData;
use Padosoft\LaravelAiSearchProviders\Providers\YouComSearchProvider;
use Padosoft\LaravelAiSearchProviders\Tests\Concerns\ReadsLocalEnv;
use Padosoft\LaravelAiSearchProviders\Tests\TestCase;

final class LiveYouComSearchProviderTest extends TestCase
{
    use ReadsLocalEnv;

    public function testLiveYouComReturnsWebResults(): void
    {
        $apiKey = $this->envValue('YOUCOM_API_KEY');

        if ($apiKey === null || $apiKey === '') {
            self::markTestSkipped('Set YOUCOM_API_KEY in .env to run the live You.com Search test.');
        }

        $provider = new YouComSearchProvider(SearchProviderDefinition::fromArray([
            'code' => 'youcom-live',
            'name' => 'You.com Live',
            'driver' => 'youcom',
            'base_url' => 'https://ydc-index.io',
            'api_key' => $apiKey,
            'timeout_seconds' => 30,
            'is_active' => true,
        ]));

        $results = $provider->searchWeb(SearchQueryData::fromArray([
            'query' => 'Nike Air Force 1 07 white sneaker',
            'limit' => 5,
        ]));

        self::assertFalse($results->isEmpty(), 'You.com returned no web results for the live smoke query.');

        $first = $results->first();
        self::assertNotNull($first);
        self::assertNotSame('', trim((string) $first->title));
        self::assertNotSame('', trim((string) $first->pageUrl));
        self::assertStringStartsWith('http', (string) $first->pageUrl);
    }
}
