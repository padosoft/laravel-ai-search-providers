<?php

declare(strict_types=1);

namespace Padosoft\LaravelAiSearchProviders;

use Closure;
use Padosoft\LaravelAiSearchProviders\Contracts\SearchProviderFactoryInterface;
use Padosoft\LaravelAiSearchProviders\Contracts\SearchProviderInterface;
use Padosoft\LaravelAiSearchProviders\Data\SearchProviderDefinition;

final class CallableSearchProviderFactory implements SearchProviderFactoryInterface
{
    /**
     * @param  Closure(SearchProviderDefinition): SearchProviderInterface  $factory
     */
    public function __construct(private readonly Closure $factory)
    {
    }

    public function make(SearchProviderDefinition $definition): SearchProviderInterface
    {
        return ($this->factory)($definition);
    }
}
