<?php

declare(strict_types=1);

namespace Padosoft\LaravelAiSearchProviders\Tests\Unit;

use Padosoft\LaravelAiSearchProviders\LaravelAiSearchProvidersServiceProvider;
use Padosoft\LaravelAiSearchProviders\Tests\TestCase;

final class ServiceProviderTest extends TestCase
{
    public function test_service_provider_is_registered(): void
    {
        $providers = $this->app->getLoadedProviders();

        self::assertArrayHasKey(LaravelAiSearchProvidersServiceProvider::class, $providers);
    }

    public function test_default_config_is_merged(): void
    {
        $config = $this->app['config']->get('ai-search-providers');

        self::assertIsArray($config);
        self::assertSame('search_providers', $config['table']);
        self::assertNull($config['model']);
        self::assertTrue((bool) $config['load_migrations']);
        self::assertIsArray($config['factories']);
    }

    public function test_config_table_name_can_be_overridden(): void
    {
        $this->app['config']->set('ai-search-providers.table', 'custom_providers_table');

        self::assertSame('custom_providers_table', $this->app['config']->get('ai-search-providers.table'));
    }
}
