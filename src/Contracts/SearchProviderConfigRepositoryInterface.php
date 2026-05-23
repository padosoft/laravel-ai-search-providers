<?php

declare(strict_types=1);

namespace Padosoft\LaravelAiSearchProviders\Contracts;

use Padosoft\LaravelAiSearchProviders\Data\SearchProviderDefinition;

interface SearchProviderConfigRepositoryInterface
{
    /**
     * @return array<int, SearchProviderDefinition>
     */
    public function getActiveProviders(): array;
}
