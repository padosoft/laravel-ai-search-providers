<?php

declare(strict_types=1);

namespace Padosoft\LaravelAiSearchProviders;

use Illuminate\Support\ServiceProvider;
use Padosoft\LaravelAiSearchProviders\Contracts\SearchEventLoggerInterface;
use Padosoft\LaravelAiSearchProviders\Contracts\SearchProviderConfigRepositoryInterface;
use Padosoft\LaravelAiSearchProviders\Contracts\SearchProviderFactoryInterface;
use Padosoft\LaravelAiSearchProviders\Providers\BraveSearchProvider;
use Padosoft\LaravelAiSearchProviders\Providers\DuckDuckGoSearchProvider;
use Padosoft\LaravelAiSearchProviders\Providers\ExaSearchProvider;
use Padosoft\LaravelAiSearchProviders\Providers\FakeSearchProvider;
use Padosoft\LaravelAiSearchProviders\Providers\FirecrawlSearchProvider;
use Padosoft\LaravelAiSearchProviders\Providers\SearchApiSearchProvider;
use Padosoft\LaravelAiSearchProviders\Providers\TavilySearchProvider;
use Padosoft\LaravelAiSearchProviders\Providers\WebSearchApiSearchProvider;
use Padosoft\LaravelAiSearchProviders\Providers\YouComSearchProvider;
use Padosoft\LaravelAiSearchProviders\Repositories\EloquentSearchProviderConfigRepository;

final class LaravelAiSearchProvidersServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/ai-search-providers.php', 'ai-search-providers');

        $this->app->bind(
            SearchProviderConfigRepositoryInterface::class,
            static fn (): EloquentSearchProviderConfigRepository => new EloquentSearchProviderConfigRepository(),
        );

        $this->app->singleton(SearchProviderManager::class, function ($app): SearchProviderManager {
            return new SearchProviderManager(
                repository: $app->make(SearchProviderConfigRepositoryInterface::class),
                factories: $this->resolveFactories($app),
                logger: $app->bound(SearchEventLoggerInterface::class)
                    ? $app->make(SearchEventLoggerInterface::class)
                    : null,
            );
        });
    }

    public function boot(): void
    {
        if ((bool) $this->app['config']->get('ai-search-providers.load_migrations', true)) {
            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        }

        $this->publishes([
            __DIR__ . '/../config/ai-search-providers.php' => $this->configPath('ai-search-providers.php'),
        ], 'ai-search-providers-config');

        $this->publishes([
            __DIR__ . '/../database/migrations' => $this->databasePath('migrations'),
        ], 'ai-search-providers-migrations');
    }

    /**
     * @return array<string, SearchProviderFactoryInterface>
     */
    private function resolveFactories($app): array
    {
        $defaults = [
            'fake' => new CallableSearchProviderFactory(
                static fn ($definition): FakeSearchProvider => FakeSearchProvider::fromDefinition($definition),
            ),
            'brave' => new CallableSearchProviderFactory(
                static fn ($definition): BraveSearchProvider => new BraveSearchProvider($definition),
            ),
            'tavily' => new CallableSearchProviderFactory(
                static fn ($definition): TavilySearchProvider => new TavilySearchProvider($definition),
            ),
            'exa' => new CallableSearchProviderFactory(
                static fn ($definition): ExaSearchProvider => new ExaSearchProvider($definition),
            ),
            'firecrawl' => new CallableSearchProviderFactory(
                static fn ($definition): FirecrawlSearchProvider => new FirecrawlSearchProvider($definition),
            ),
            'websearchapi' => new CallableSearchProviderFactory(
                static fn ($definition): WebSearchApiSearchProvider => new WebSearchApiSearchProvider($definition),
            ),
            'duckduckgo' => new CallableSearchProviderFactory(
                static fn ($definition): DuckDuckGoSearchProvider => new DuckDuckGoSearchProvider($definition),
            ),
            'searchapi' => new CallableSearchProviderFactory(
                static fn ($definition): SearchApiSearchProvider => new SearchApiSearchProvider($definition),
            ),
            'youcom' => new CallableSearchProviderFactory(
                static fn ($definition): YouComSearchProvider => new YouComSearchProvider($definition),
            ),
        ];

        $overrides = (array) ($app['config']->get('ai-search-providers.factories') ?? []);
        $merged = $defaults;

        foreach ($overrides as $driver => $factory) {
            if ($factory instanceof SearchProviderFactoryInterface) {
                $merged[$driver] = $factory;
                continue;
            }

            if (is_string($factory) && class_exists($factory)) {
                $instance = $app->make($factory);

                if ($instance instanceof SearchProviderFactoryInterface) {
                    $merged[$driver] = $instance;
                }

                continue;
            }

            if (is_callable($factory) || $factory instanceof \Closure) {
                $merged[$driver] = new CallableSearchProviderFactory(\Closure::fromCallable($factory));
            }
        }

        return $merged;
    }

    private function configPath(string $file): string
    {
        if (function_exists('config_path')) {
            return config_path($file);
        }

        return base_path('config/' . $file);
    }

    private function databasePath(string $folder): string
    {
        if (function_exists('database_path')) {
            return database_path($folder);
        }

        return base_path('database/' . $folder);
    }
}
