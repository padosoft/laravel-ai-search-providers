<?php

declare(strict_types=1);

namespace Padosoft\LaravelAiSearchProviders\Data;

final class SearchProviderExecutionResult
{
    /**
     * @param  array<int, array<string, mixed>>  $attempts
     */
    public function __construct(
        public readonly ?SearchProviderDefinition $provider,
        public readonly SearchResultCollection $results,
        public readonly array $attempts = [],
        public readonly bool $usedFallback = false,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'provider' => $this->provider?->toSafeArray(),
            'results' => $this->results->toArray(),
            'attempts' => $this->attempts,
            'used_fallback' => $this->usedFallback,
        ];
    }
}
