<?php

declare(strict_types=1);

namespace Padosoft\LaravelAiSearchProviders\Repositories;

use Padosoft\LaravelAiSearchProviders\Contracts\SearchProviderConfigRepositoryInterface;
use Padosoft\LaravelAiSearchProviders\Data\SearchProviderDefinition;
use Padosoft\LaravelAiSearchProviders\Models\SearchProviderConfig;

final class EloquentSearchProviderConfigRepository implements SearchProviderConfigRepositoryInterface
{
    /**
     * @param  class-string|null  $providerModel  Override the Eloquent model used as the
     *         backing store. Host applications can pass a custom subclass when
     *         they need to preserve a legacy table name (`product_image_search_providers`)
     *         or extend the schema with extra columns.
     */
    public function __construct(
        private readonly ?string $providerModel = null,
    ) {
    }

    /**
     * @return array<int, SearchProviderDefinition>
     */
    public function getActiveProviders(): array
    {
        $modelClass = $this->resolveModelClass();

        if ($modelClass === null) {
            return [];
        }

        return $modelClass::query()
            ->active()
            ->ordered()
            ->get()
            ->map(static function ($provider): SearchProviderDefinition {
                return SearchProviderDefinition::fromArray([
                    'code' => $provider->code,
                    'name' => $provider->name,
                    'driver' => $provider->driver,
                    'base_url' => $provider->base_url,
                    'api_key' => $provider->api_key_encrypted,
                    'api_secret' => $provider->api_secret_encrypted,
                    'config' => $provider->config ?? [],
                    'priority' => $provider->priority,
                    'timeout_seconds' => $provider->timeout_seconds,
                    'rate_limit_per_minute' => $provider->rate_limit_per_minute,
                    'is_active' => $provider->is_active,
                ]);
            })
            ->all();
    }

    /**
     * @return class-string|null
     */
    private function resolveModelClass(): ?string
    {
        if ($this->providerModel !== null && class_exists($this->providerModel)) {
            return $this->providerModel;
        }

        if (function_exists('config')) {
            $configured = config('ai-search-providers.model');

            if (is_string($configured) && class_exists($configured)) {
                return $configured;
            }
        }

        return class_exists(SearchProviderConfig::class) ? SearchProviderConfig::class : null;
    }
}
