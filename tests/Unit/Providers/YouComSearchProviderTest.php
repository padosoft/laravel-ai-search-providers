<?php

declare(strict_types=1);

namespace Padosoft\LaravelAiSearchProviders\Tests\Unit\Providers;

use Illuminate\Http\Client\RequestException;
use Padosoft\LaravelAiSearchProviders\Data\SearchProviderDefinition;
use Padosoft\LaravelAiSearchProviders\Data\SearchQueryData;
use Padosoft\LaravelAiSearchProviders\Providers\YouComSearchProvider;
use Padosoft\LaravelAiSearchProviders\Tests\TestCase;

final class YouComSearchProviderTest extends TestCase
{
    public function test_search_images_is_disabled(): void
    {
        $provider = $this->makeProvider();

        self::assertFalse($provider->supportsImageSearch());
        self::assertTrue($provider->searchImages(SearchQueryData::fromArray(['query' => 'x']))->isEmpty());
    }

    public function test_web_search_parses_results_web_array(): void
    {
        if (! class_exists(\Illuminate\Support\Facades\Http::class)) {
            $this->markTestSkipped('Illuminate HTTP client is not available.');
        }

        \Illuminate\Support\Facades\Http::fake([
            'https://ydc-index.io/v1/search*' => \Illuminate\Support\Facades\Http::response([
                'results' => [
                    'web' => [[
                        'title' => 'Nike AF1 page',
                        'url' => 'https://www.nike.com/t/air-force-1',
                        'description' => 'Iconic AF1.',
                        'snippets' => ['fallback snippet'],
                        'thumbnail_url' => 'https://cdn.nike.com/thumb.jpg',
                        'page_age' => '2026-01-15T00:00:00Z',
                        'favicon_url' => 'https://www.nike.com/favicon.ico',
                    ]],
                ],
                'metadata' => ['search_uuid' => 'abc', 'query' => 'Air Force 1', 'latency' => 0.4],
            ], 200),
        ]);

        $results = $this->makeProvider()->searchWeb(SearchQueryData::fromArray([
            'query' => 'Air Force 1',
            'limit' => 5,
        ]));

        self::assertCount(1, $results);
        $first = $results->first();
        self::assertNotNull($first);
        self::assertSame('https://www.nike.com/t/air-force-1', $first->pageUrl);
        self::assertSame('https://cdn.nike.com/thumb.jpg', $first->thumbnailUrl);
        self::assertSame('Iconic AF1.', $first->snippet);
        self::assertSame('www.nike.com', $first->sourceDomain);
    }

    public function test_snippet_falls_back_to_first_non_empty_snippets_entry(): void
    {
        if (! class_exists(\Illuminate\Support\Facades\Http::class)) {
            $this->markTestSkipped('Illuminate HTTP client is not available.');
        }

        \Illuminate\Support\Facades\Http::fake([
            'https://ydc-index.io/v1/search*' => \Illuminate\Support\Facades\Http::response([
                'results' => [
                    'web' => [[
                        'title' => 'AF1',
                        'url' => 'https://example.test/af1',
                        'snippets' => ['', 'first usable snippet', 'second'],
                    ]],
                ],
            ], 200),
        ]);

        $first = $this->makeProvider()->searchWeb(SearchQueryData::fromArray(['query' => 'x']))->first();

        self::assertNotNull($first);
        self::assertSame('first usable snippet', $first->snippet);
    }

    public function test_returns_empty_when_results_web_missing(): void
    {
        if (! class_exists(\Illuminate\Support\Facades\Http::class)) {
            $this->markTestSkipped('Illuminate HTTP client is not available.');
        }

        \Illuminate\Support\Facades\Http::fake([
            'https://ydc-index.io/v1/search*' => \Illuminate\Support\Facades\Http::response([
                'results' => ['news' => [['title' => 'only news here']]],
            ], 200),
        ]);

        $results = $this->makeProvider()->searchWeb(SearchQueryData::fromArray(['query' => 'x']));

        self::assertTrue($results->isEmpty());
    }

    public function test_it_throws_on_unauthorized(): void
    {
        if (! class_exists(\Illuminate\Support\Facades\Http::class)) {
            $this->markTestSkipped('Illuminate HTTP client is not available.');
        }

        \Illuminate\Support\Facades\Http::fake([
            'https://ydc-index.io/v1/search*' => \Illuminate\Support\Facades\Http::response(['error' => 'unauthorized'], 401),
        ]);

        $this->expectException(RequestException::class);

        $this->makeProvider()->searchWeb(SearchQueryData::fromArray(['query' => 'x']));
    }

    public function test_site_filter_is_propagated_as_include_domains_and_x_api_key_sent(): void
    {
        if (! class_exists(\Illuminate\Support\Facades\Http::class)) {
            $this->markTestSkipped('Illuminate HTTP client is not available.');
        }

        \Illuminate\Support\Facades\Http::fake([
            'https://ydc-index.io/v1/search*' => \Illuminate\Support\Facades\Http::response([
                'results' => ['web' => []],
            ], 200),
        ]);

        $this->makeProvider()->searchWeb(SearchQueryData::fromArray([
            'query' => 'Air Force 1',
            'site' => 'nike.com',
            'limit' => 7,
        ]));

        \Illuminate\Support\Facades\Http::assertSent(static function ($request): bool {
            $queryString = parse_url($request->url(), PHP_URL_QUERY) ?? '';
            parse_str($queryString, $params);

            return ($params['query'] ?? null) === 'Air Force 1'
                && ($params['include_domains'] ?? null) === 'nike.com'
                && ((string) ($params['count'] ?? '')) === '7'
                && $request->hasHeader('X-API-Key', 'test-key');
        });
    }

    private function makeProvider(): YouComSearchProvider
    {
        return new YouComSearchProvider(SearchProviderDefinition::fromArray([
            'code' => 'youcom',
            'name' => 'You.com',
            'driver' => 'youcom',
            'base_url' => 'https://ydc-index.io',
            'api_key' => 'test-key',
            'timeout_seconds' => 7,
        ]));
    }
}
