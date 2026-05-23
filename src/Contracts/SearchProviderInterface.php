<?php

declare(strict_types=1);

namespace Padosoft\LaravelAiSearchProviders\Contracts;

use Padosoft\LaravelAiSearchProviders\Data\SearchQueryData;
use Padosoft\LaravelAiSearchProviders\Data\SearchResultCollection;

interface SearchProviderInterface
{
    public function searchImages(SearchQueryData $query): SearchResultCollection;

    public function searchWeb(SearchQueryData $query): SearchResultCollection;

    public function supportsImageSearch(): bool;

    public function supportsSiteFilter(): bool;
}
