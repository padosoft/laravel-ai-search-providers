<?php

declare(strict_types=1);

namespace Padosoft\LaravelAiSearchProviders\Providers;

use RuntimeException;
use Padosoft\LaravelAiSearchProviders\Data\SearchQueryData;
use Padosoft\LaravelAiSearchProviders\Data\SearchResultCollection;

/**
 * You.com Search API driver — https://you.com/docs/api-reference/search/v1-search
 *
 * GET https://ydc-index.io/v1/search with `X-API-Key` header. Returns a
 * `results.web[]` array (and optionally `results.news[]`) where each entry
 * exposes title/url/description/snippets[]/thumbnail_url/page_age/contents.
 *
 * No dedicated image-search endpoint is documented as of 2026-05. The driver
 * therefore reports `supportsImageSearch() === false`; consumers that need
 * images can call `searchWeb()` and harvest `thumbnail_url` per result, or
 * let the downstream pipeline extract images from the returned pages.
 *
 * Site filter propagated as `include_domains` (comma-separated list).
 */
final class YouComSearchProvider extends AbstractHttpSearchProvider
{
    public function supportsImageSearch(): bool
    {
        return false;
    }

    public function searchImages(SearchQueryData $query): SearchResultCollection
    {
        return new SearchResultCollection();
    }

    public function searchWeb(SearchQueryData $query): SearchResultCollection
    {
        $payload = $this->request([
            'query' => $query->toSearchString(),
            'count' => $this->clampCount($query->limit),
            'country' => $this->definition->configValue('country'),
            'language' => $this->definition->configValue('language'),
            'safesearch' => $this->definition->configValue('safesearch'),
            'include_domains' => $this->buildIncludeDomains($query),
        ]);

        $web = $payload['results']['web'] ?? null;

        if (! is_array($web)) {
            return new SearchResultCollection();
        }

        return new SearchResultCollection(array_map(function (array $hit): array {
            $pageUrl = is_string($hit['url'] ?? null) ? $hit['url'] : null;
            $thumbnail = $this->pickUrl($hit, ['thumbnail_url']);
            $snippet = $this->firstSnippet($hit);

            return [
                'title' => (string) ($hit['title'] ?? 'Untitled result'),
                'page_url' => $pageUrl,
                'image_url' => null,
                'thumbnail_url' => $thumbnail,
                'source_domain' => $this->extractDomain($pageUrl),
                'snippet' => $snippet,
                'width' => null,
                'height' => null,
                'score' => null,
                'provider_metadata' => [
                    'provider' => $this->definition->code,
                    'page_age' => $hit['page_age'] ?? null,
                    'favicon_url' => $hit['favicon_url'] ?? null,
                ],
            ];
        }, array_values(array_filter($web, static fn (mixed $entry): bool => is_array($entry)))));
    }

    /**
     * @param  array<string, mixed>  $hit
     */
    private function firstSnippet(array $hit): ?string
    {
        $description = $hit['description'] ?? null;

        if (is_string($description) && trim($description) !== '') {
            return trim($description);
        }

        $snippets = $hit['snippets'] ?? null;

        if (is_array($snippets)) {
            foreach ($snippets as $candidate) {
                if (is_string($candidate) && trim($candidate) !== '') {
                    return trim($candidate);
                }
            }
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>
     */
    private function request(array $query): array
    {
        $this->assertHttpClientAvailable();

        $response = \Illuminate\Support\Facades\Http::baseUrl($this->definition->baseUrl ?? 'https://ydc-index.io')
            ->acceptJson()
            ->withHeaders([
                'X-API-Key' => (string) $this->definition->apiKey,
            ])
            ->timeout($this->definition->timeoutSeconds)
            ->get('/v1/search', array_filter(
                $query,
                static fn (mixed $value): bool => $value !== null && $value !== '' && $value !== [],
            ));

        $payload = $response->throw()->json();

        if (! is_array($payload)) {
            throw new RuntimeException('Unexpected You.com payload: not a JSON object.');
        }

        return $payload;
    }

    private function clampCount(int $limit): int
    {
        return max(1, min(50, $limit));
    }

    private function buildIncludeDomains(SearchQueryData $query): ?string
    {
        if ($query->site === null || $query->site === '') {
            return null;
        }

        return $query->site;
    }
}
