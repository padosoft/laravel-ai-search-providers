<?php

declare(strict_types=1);

namespace Padosoft\LaravelAiSearchProviders;

use Padosoft\LaravelAiSearchProviders\Contracts\SearchProviderConfigRepositoryInterface;

/**
 * Bootstrap-safe stand-in used by the service provider before PR A5 wires
 * the real Eloquent-backed repository. Returns no providers so the manager
 * falls back to its fallback list (or returns an empty result), but never
 * crashes on `composer require` immediately after PR A3 lands.
 *
 * Consumer applications should never use this directly — they get the
 * Eloquent repository via the standard `SearchProviderConfigRepositoryInterface`
 * binding from PR A5 onwards.
 *
 * @internal
 */
final class EmptyConfigRepository implements SearchProviderConfigRepositoryInterface
{
    public function getActiveProviders(): array
    {
        return [];
    }
}
