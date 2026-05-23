<?php

declare(strict_types=1);

namespace Padosoft\LaravelAiSearchProviders\Tests\Unit\Providers;

use Padosoft\LaravelAiSearchProviders\Data\SearchProviderDefinition;
use Padosoft\LaravelAiSearchProviders\Data\SearchQueryData;
use Padosoft\LaravelAiSearchProviders\Providers\FakeSearchProvider;
use Padosoft\LaravelAiSearchProviders\Tests\TestCase;
use RuntimeException;

final class FakeSearchProviderTest extends TestCase
{
    public function test_search_images_returns_configured_results(): void
    {
        $provider = FakeSearchProvider::fromDefinition($this->definitionWithImages([
            ['title' => 'A', 'page_url' => 'https://nike.com/a', 'image_url' => 'https://cdn.nike.com/a.jpg'],
            ['title' => 'B', 'page_url' => 'https://other.test/b', 'image_url' => 'https://other.test/b.jpg'],
        ]));

        $results = $provider->searchImages(SearchQueryData::fromArray(['query' => 'anything']));

        self::assertCount(2, $results);
        self::assertSame('A', $results->first()?->title);
    }

    public function test_search_images_applies_site_filter(): void
    {
        $provider = FakeSearchProvider::fromDefinition($this->definitionWithImages([
            ['title' => 'A', 'page_url' => 'https://nike.com/a', 'image_url' => null],
            ['title' => 'B', 'page_url' => 'https://other.test/b', 'image_url' => null],
        ]));

        $results = $provider->searchImages(SearchQueryData::fromArray([
            'query' => 'anything',
            'site' => 'nike.com',
        ]));

        self::assertCount(1, $results);
        self::assertSame('A', $results->first()?->title);
    }

    public function test_search_images_respects_limit(): void
    {
        $provider = FakeSearchProvider::fromDefinition($this->definitionWithImages([
            ['title' => 'A', 'page_url' => 'https://a.test', 'image_url' => null],
            ['title' => 'B', 'page_url' => 'https://b.test', 'image_url' => null],
            ['title' => 'C', 'page_url' => 'https://c.test', 'image_url' => null],
        ]));

        $results = $provider->searchImages(SearchQueryData::fromArray([
            'query' => 'anything',
            'limit' => 2,
        ]));

        self::assertCount(2, $results);
    }

    public function test_throw_flag_makes_provider_fail(): void
    {
        $provider = FakeSearchProvider::fromDefinition(SearchProviderDefinition::fromArray([
            'code' => 'fake',
            'driver' => 'fake',
            'config' => ['throw' => true],
        ]));

        $this->expectException(RuntimeException::class);

        $provider->searchImages(SearchQueryData::fromArray(['query' => 'x']));
    }

    public function test_throw_for_targets_specific_mode_only(): void
    {
        $provider = FakeSearchProvider::fromDefinition(SearchProviderDefinition::fromArray([
            'code' => 'fake',
            'driver' => 'fake',
            'config' => [
                'throw_for' => ['web'],
                'image_results' => [['title' => 'A', 'page_url' => 'https://a.test', 'image_url' => null]],
                'web_results' => [],
            ],
        ]));

        // searchImages should succeed
        $imageResults = $provider->searchImages(SearchQueryData::fromArray(['query' => 'x']));
        self::assertCount(1, $imageResults);

        // searchWeb should throw
        $this->expectException(RuntimeException::class);
        $provider->searchWeb(SearchQueryData::fromArray(['query' => 'x']));
    }

    public function test_supports_flags_can_be_overridden_in_config(): void
    {
        $provider = FakeSearchProvider::fromDefinition(SearchProviderDefinition::fromArray([
            'code' => 'fake',
            'driver' => 'fake',
            'config' => [
                'supports_image_search' => false,
                'supports_site_filter' => false,
            ],
        ]));

        self::assertFalse($provider->supportsImageSearch());
        self::assertFalse($provider->supportsSiteFilter());
    }

    /**
     * @param  array<int, array<string, mixed>>  $imageResults
     */
    private function definitionWithImages(array $imageResults): SearchProviderDefinition
    {
        return SearchProviderDefinition::fromArray([
            'code' => 'fake',
            'driver' => 'fake',
            'config' => ['image_results' => $imageResults],
        ]);
    }
}
