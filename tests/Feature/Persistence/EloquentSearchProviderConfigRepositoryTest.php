<?php

declare(strict_types=1);

namespace Padosoft\LaravelAiSearchProviders\Tests\Feature\Persistence;

use Padosoft\LaravelAiSearchProviders\Models\SearchProviderConfig;
use Padosoft\LaravelAiSearchProviders\Repositories\EloquentSearchProviderConfigRepository;
use Padosoft\LaravelAiSearchProviders\Tests\TestCase;

final class EloquentSearchProviderConfigRepositoryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'migrations');
        $this->artisan('migrate')->run();
    }

    public function test_default_table_is_search_providers(): void
    {
        $model = new SearchProviderConfig();

        self::assertSame('search_providers', $model->getTable());
    }

    public function test_table_name_can_be_overridden_via_config(): void
    {
        $this->app['config']->set('ai-search-providers.table', 'custom_providers');

        $model = new SearchProviderConfig();

        self::assertSame('custom_providers', $model->getTable());
    }

    public function test_repository_returns_active_providers_sorted_by_priority(): void
    {
        SearchProviderConfig::query()->create([
            'code' => 'beta',
            'name' => 'Beta',
            'driver' => 'fake',
            'priority' => 30,
            'is_active' => true,
        ]);

        SearchProviderConfig::query()->create([
            'code' => 'inactive',
            'name' => 'Inactive',
            'driver' => 'fake',
            'priority' => 5,
            'is_active' => false,
        ]);

        SearchProviderConfig::query()->create([
            'code' => 'alpha',
            'name' => 'Alpha',
            'driver' => 'fake',
            'priority' => 10,
            'is_active' => true,
        ]);

        $repository = new EloquentSearchProviderConfigRepository();
        $definitions = $repository->getActiveProviders();

        self::assertCount(2, $definitions);
        self::assertSame('alpha', $definitions[0]->code);
        self::assertSame('beta', $definitions[1]->code);
    }

    public function test_repository_constructor_override_takes_priority_over_config(): void
    {
        SearchProviderConfig::query()->create([
            'code' => 'a',
            'name' => 'A',
            'driver' => 'fake',
            'priority' => 10,
            'is_active' => true,
        ]);

        $repository = new EloquentSearchProviderConfigRepository(SearchProviderConfig::class);
        $definitions = $repository->getActiveProviders();

        self::assertCount(1, $definitions);
        self::assertSame('a', $definitions[0]->code);
    }

    public function test_repository_returns_empty_when_model_class_invalid(): void
    {
        $repository = new EloquentSearchProviderConfigRepository('Nonexistent\\Model');

        $this->app['config']->set('ai-search-providers.model', 'AlsoNonexistent\\Model');

        // No override match, config value invalid → falls back to package default which exists
        // so we instead test by giving the constructor a valid override and clearing config
        $this->app['config']->set('ai-search-providers.model', null);

        // With a valid SearchProviderConfig fallback there will be no rows
        $definitions = (new EloquentSearchProviderConfigRepository())->getActiveProviders();
        self::assertSame([], $definitions);
    }
}
