<?php

declare(strict_types=1);

namespace Padosoft\LaravelAiSearchProviders\Tests\Unit;

use Padosoft\LaravelAiSearchProviders\CallableSearchProviderFactory;
use Padosoft\LaravelAiSearchProviders\Data\SearchProviderDefinition;
use Padosoft\LaravelAiSearchProviders\Data\SearchQueryData;
use Padosoft\LaravelAiSearchProviders\Providers\FakeSearchProvider;
use Padosoft\LaravelAiSearchProviders\SearchProviderManager;
use Padosoft\LaravelAiSearchProviders\Tests\Support\InMemorySearchProviderConfigRepository;
use Padosoft\LaravelAiSearchProviders\Tests\TestCase;

final class SearchProviderManagerTest extends TestCase
{
    public function test_it_returns_empty_result_when_no_providers_active(): void
    {
        $manager = new SearchProviderManager(
            repository: new InMemorySearchProviderConfigRepository([]),
            factories: ['fake' => $this->fakeFactory()],
        );

        $execution = $manager->searchImages(SearchQueryData::fromArray(['query' => 'anything']));

        self::assertNull($execution->provider);
        self::assertTrue($execution->results->isEmpty());
        self::assertSame([], $execution->attempts);
        self::assertFalse($execution->usedFallback);
    }

    public function test_it_falls_back_to_next_active_provider_and_keeps_attempt_metadata_safe(): void
    {
        $primary = SearchProviderDefinition::fromArray([
            'code' => 'primary',
            'name' => 'Primary',
            'driver' => 'fake',
            'priority' => 10,
            'timeout_seconds' => 3,
            'api_key' => 'super-secret',
            'config' => ['throw' => true],
        ]);
        $fallback = SearchProviderDefinition::fromArray([
            'code' => 'fallback',
            'name' => 'Fallback',
            'driver' => 'fake',
            'priority' => 20,
            'timeout_seconds' => 9,
            'config' => [
                'image_results' => [[
                    'title' => 'Brand Model Red',
                    'page_url' => 'https://example.test/products/1',
                    'image_url' => 'https://cdn.example.test/products/1.jpg',
                    'source_domain' => 'example.test',
                    'score' => 0.91,
                ]],
            ],
        ]);

        $manager = new SearchProviderManager(
            repository: new InMemorySearchProviderConfigRepository([$primary, $fallback]),
            factories: ['fake' => $this->fakeFactory()],
        );

        $execution = $manager->searchImages(SearchQueryData::fromArray([
            'brand' => 'Brand',
            'model' => 'Model',
            'color' => 'Red',
        ]));

        self::assertTrue($execution->usedFallback);
        self::assertSame('fallback', $execution->provider?->code);
        self::assertCount(2, $execution->attempts);
        self::assertSame('failed', $execution->attempts[0]['status']);
        self::assertSame(3, $execution->attempts[0]['timeout_seconds']);
        self::assertArrayNotHasKey('api_key', $execution->attempts[0]['provider']);
        self::assertSame(1, $execution->results->count());
    }

    public function test_it_skips_provider_when_image_search_not_supported(): void
    {
        $webOnly = SearchProviderDefinition::fromArray([
            'code' => 'web-only',
            'name' => 'WebOnly',
            'driver' => 'fake',
            'priority' => 10,
            'config' => ['supports_image_search' => false],
        ]);

        $manager = new SearchProviderManager(
            repository: new InMemorySearchProviderConfigRepository([$webOnly]),
            factories: ['fake' => $this->fakeFactory()],
        );

        $execution = $manager->searchImages(SearchQueryData::fromArray(['query' => 'anything']));

        self::assertNull($execution->provider);
        self::assertSame('skipped', $execution->attempts[0]['status']);
        self::assertSame('image_search_not_supported', $execution->attempts[0]['reason']);
    }

    public function test_it_skips_provider_when_site_filter_unsupported(): void
    {
        $noSite = SearchProviderDefinition::fromArray([
            'code' => 'no-site',
            'name' => 'NoSite',
            'driver' => 'fake',
            'priority' => 10,
            'config' => ['supports_site_filter' => false],
        ]);

        $manager = new SearchProviderManager(
            repository: new InMemorySearchProviderConfigRepository([$noSite]),
            factories: ['fake' => $this->fakeFactory()],
        );

        $execution = $manager->searchImages(SearchQueryData::fromArray([
            'query' => 'anything',
            'site' => 'nike.com',
        ]));

        self::assertSame('skipped', $execution->attempts[0]['status']);
        self::assertSame('site_filter_not_supported', $execution->attempts[0]['reason']);
    }

    public function test_it_raises_when_driver_factory_missing(): void
    {
        $unknown = SearchProviderDefinition::fromArray([
            'code' => 'unknown',
            'name' => 'Unknown',
            'driver' => 'this-driver-is-not-registered',
            'priority' => 10,
        ]);

        $manager = new SearchProviderManager(
            repository: new InMemorySearchProviderConfigRepository([$unknown]),
            factories: ['fake' => $this->fakeFactory()],
        );

        $execution = $manager->searchImages(SearchQueryData::fromArray(['query' => 'anything']));

        self::assertSame('failed', $execution->attempts[0]['status']);
        self::assertStringContainsString('this-driver-is-not-registered', $execution->attempts[0]['error']);
    }

    private function fakeFactory(): CallableSearchProviderFactory
    {
        return new CallableSearchProviderFactory(
            static fn (SearchProviderDefinition $definition): FakeSearchProvider => FakeSearchProvider::fromDefinition($definition),
        );
    }
}
