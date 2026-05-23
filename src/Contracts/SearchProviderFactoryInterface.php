<?php

declare(strict_types=1);

namespace Padosoft\LaravelAiSearchProviders\Contracts;

use Padosoft\LaravelAiSearchProviders\Data\SearchProviderDefinition;

interface SearchProviderFactoryInterface
{
    public function make(SearchProviderDefinition $definition): SearchProviderInterface;
}
