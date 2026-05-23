<?php

declare(strict_types=1);

namespace Padosoft\LaravelAiSearchProviders;

use Illuminate\Support\ServiceProvider;

final class LaravelAiSearchProvidersServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/ai-search-providers.php', 'ai-search-providers');
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/ai-search-providers.php' => $this->configPath('ai-search-providers.php'),
        ], 'ai-search-providers-config');
    }

    private function configPath(string $file): string
    {
        if (function_exists('config_path')) {
            return config_path($file);
        }

        return base_path('config/' . $file);
    }
}
