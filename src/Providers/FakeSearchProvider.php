<?php

declare(strict_types=1);

namespace Padosoft\LaravelAiSearchProviders\Providers;

use RuntimeException;
use Padosoft\LaravelAiSearchProviders\Contracts\SearchProviderInterface;
use Padosoft\LaravelAiSearchProviders\Data\SearchProviderDefinition;
use Padosoft\LaravelAiSearchProviders\Data\SearchQueryData;
use Padosoft\LaravelAiSearchProviders\Data\SearchResultCollection;

final class FakeSearchProvider implements SearchProviderInterface
{
    /**
     * @param  array<int, array<string, mixed>>  $imageResults
     * @param  array<int, array<string, mixed>>  $webResults
     */
    public function __construct(
        private readonly SearchProviderDefinition $definition,
        private readonly array $imageResults = [],
        private readonly array $webResults = [],
    ) {
    }

    public static function fromDefinition(SearchProviderDefinition $definition): self
    {
        $imageResults = $definition->configValue('image_results', []);
        $webResults = $definition->configValue('web_results', []);

        return new self(
            definition: $definition,
            imageResults: is_array($imageResults) ? $imageResults : [],
            webResults: is_array($webResults) ? $webResults : [],
        );
    }

    public function searchImages(SearchQueryData $query): SearchResultCollection
    {
        $this->guardAgainstConfiguredFailure('images');

        return new SearchResultCollection($this->scopedResults($this->imageResults, $query));
    }

    public function searchWeb(SearchQueryData $query): SearchResultCollection
    {
        $this->guardAgainstConfiguredFailure('web');

        return new SearchResultCollection($this->scopedResults($this->webResults, $query));
    }

    public function supportsImageSearch(): bool
    {
        return (bool) $this->definition->configValue('supports_image_search', true);
    }

    public function supportsSiteFilter(): bool
    {
        return (bool) $this->definition->configValue('supports_site_filter', true);
    }

    private function guardAgainstConfiguredFailure(string $mode): void
    {
        $failModes = $this->definition->configValue('throw_for', []);
        $failModes = is_array($failModes) ? $failModes : [$failModes];

        if ((bool) $this->definition->configValue('throw', false) || in_array($mode, $failModes, true)) {
            throw new RuntimeException(sprintf('Fake provider [%s] forced failure for %s search.', $this->definition->code, $mode));
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $results
     * @return array<int, array<string, mixed>>
     */
    private function scopedResults(array $results, SearchQueryData $query): array
    {
        $filtered = $results;

        if ($query->site !== null && $query->site !== '') {
            $filtered = array_values(array_filter(
                $filtered,
                static function (array $result) use ($query): bool {
                    $pageUrl = (string) ($result['page_url'] ?? $result['pageUrl'] ?? '');

                    return $pageUrl === '' || str_contains($pageUrl, $query->site);
                },
            ));
        }

        return array_slice($filtered, 0, $query->limit);
    }
}
