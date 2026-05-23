<?php

declare(strict_types=1);

namespace Padosoft\LaravelAiSearchProviders\Tests\Unit\Providers;

use Illuminate\Http\Client\RequestException;
use Padosoft\LaravelAiSearchProviders\Data\SearchProviderDefinition;
use Padosoft\LaravelAiSearchProviders\Data\SearchQueryData;
use Padosoft\LaravelAiSearchProviders\Providers\SearchApiSearchProvider;
use Padosoft\LaravelAiSearchProviders\Tests\TestCase;

final class SearchApiSearchProviderTest extends TestCase
{
    public function test_it_parses_google_images_payload(): void
    {
        if (! class_exists(\Illuminate\Support\Facades\Http::class)) {
            $this->markTestSkipped('Illuminate HTTP client is not available.');
        }

        \Illuminate\Support\Facades\Http::fake([
            'https://www.searchapi.io/api/v1/search*' => \Illuminate\Support\Facades\Http::response([
                'images' => [[
                    'position' => 1,
                    'title' => 'Nike AF1',
                    'source' => ['name' => 'Nike', 'link' => 'https://www.nike.com/t/air-force-1'],
                    'original' => ['link' => 'https://cdn.nike.com/af1.jpg', 'width' => 1200, 'height' => 1200],
                    'thumbnail' => 'https://encrypted-tbn.gstatic.com/x.jpg',
                ]],
            ], 200),
        ]);

        $results = $this->makeProvider()->searchImages(SearchQueryData::fromArray([
            'query' => 'Nike Air Force 1',
            'limit' => 5,
        ]));

        self::assertCount(1, $results);
        $first = $results->first();
        self::assertNotNull($first);
        self::assertSame('https://cdn.nike.com/af1.jpg', $first->imageUrl);
        self::assertSame('https://www.nike.com/t/air-force-1', $first->pageUrl);
        self::assertSame('www.nike.com', $first->sourceDomain);
        self::assertSame(1200, $first->width);
        self::assertSame(1200, $first->height);
    }

    public function test_it_returns_empty_when_images_array_missing(): void
    {
        if (! class_exists(\Illuminate\Support\Facades\Http::class)) {
            $this->markTestSkipped('Illuminate HTTP client is not available.');
        }

        \Illuminate\Support\Facades\Http::fake([
            'https://www.searchapi.io/api/v1/search*' => \Illuminate\Support\Facades\Http::response([
                'search_metadata' => ['status' => 'Success'],
            ], 200),
        ]);

        $results = $this->makeProvider()->searchImages(SearchQueryData::fromArray(['query' => 'x']));

        self::assertTrue($results->isEmpty());
    }

    public function test_it_throws_on_unauthorized(): void
    {
        if (! class_exists(\Illuminate\Support\Facades\Http::class)) {
            $this->markTestSkipped('Illuminate HTTP client is not available.');
        }

        \Illuminate\Support\Facades\Http::fake([
            'https://www.searchapi.io/api/v1/search*' => \Illuminate\Support\Facades\Http::response(['error' => 'unauthorized'], 401),
        ]);

        $this->expectException(RequestException::class);

        $this->makeProvider()->searchImages(SearchQueryData::fromArray(['query' => 'x']));
    }

    public function test_site_filter_is_appended_as_operator_and_bearer_token_sent(): void
    {
        if (! class_exists(\Illuminate\Support\Facades\Http::class)) {
            $this->markTestSkipped('Illuminate HTTP client is not available.');
        }

        \Illuminate\Support\Facades\Http::fake([
            'https://www.searchapi.io/api/v1/search*' => \Illuminate\Support\Facades\Http::response(['images' => []], 200),
        ]);

        $this->makeProvider()->searchImages(SearchQueryData::fromArray([
            'query' => 'Air Force 1',
            'site' => 'nike.com',
            'limit' => 7,
        ]));

        \Illuminate\Support\Facades\Http::assertSent(static function ($request): bool {
            $queryString = parse_url($request->url(), PHP_URL_QUERY) ?? '';
            parse_str($queryString, $params);

            return ($params['engine'] ?? null) === 'google_images'
                && ($params['q'] ?? null) === 'Air Force 1 site:nike.com'
                && ((string) ($params['num'] ?? '')) === '7'
                && $request->hasHeader('Authorization', 'Bearer test-key');
        });
    }

    public function test_web_search_maps_organic_results(): void
    {
        if (! class_exists(\Illuminate\Support\Facades\Http::class)) {
            $this->markTestSkipped('Illuminate HTTP client is not available.');
        }

        \Illuminate\Support\Facades\Http::fake([
            'https://www.searchapi.io/api/v1/search*' => \Illuminate\Support\Facades\Http::response([
                'organic_results' => [[
                    'position' => 1,
                    'title' => 'Nike AF1 page',
                    'link' => 'https://www.nike.com/t/air-force-1',
                    'snippet' => 'Iconic AF1.',
                    'source' => 'nike.com',
                ]],
            ], 200),
        ]);

        $results = $this->makeProvider()->searchWeb(SearchQueryData::fromArray([
            'query' => 'Air Force 1',
        ]));

        self::assertCount(1, $results);
        $first = $results->first();
        self::assertNotNull($first);
        self::assertSame('https://www.nike.com/t/air-force-1', $first->pageUrl);
        self::assertSame('Iconic AF1.', $first->snippet);
        self::assertSame('www.nike.com', $first->sourceDomain);
    }

    private function makeProvider(): SearchApiSearchProvider
    {
        return new SearchApiSearchProvider(SearchProviderDefinition::fromArray([
            'code' => 'searchapi',
            'name' => 'SearchAPI.io',
            'driver' => 'searchapi',
            'base_url' => 'https://www.searchapi.io',
            'api_key' => 'test-key',
            'timeout_seconds' => 7,
        ]));
    }
}
