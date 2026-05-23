<?php

declare(strict_types=1);

namespace Padosoft\LaravelAiSearchProviders\Providers;

use RuntimeException;
use Padosoft\LaravelAiSearchProviders\Data\SearchQueryData;
use Padosoft\LaravelAiSearchProviders\Data\SearchResultCollection;

/**
 * SearchAPI.io driver — https://www.searchapi.io/docs/google-images
 *
 * Single endpoint with `engine` switching: `google_images` for image search,
 * `google` for web search. Auth via `Authorization: Bearer` header.
 *
 * Image response: `images[]` with position/title/source.{name,link}/
 * original.{link,width,height}/thumbnail.
 * Web response: `organic_results[]` with position/title/link/snippet/source.
 */
final class SearchApiSearchProvider extends AbstractHttpSearchProvider
{
    public function searchImages(SearchQueryData $query): SearchResultCollection
    {
        $payload = $this->request([
            'engine' => 'google_images',
            'q' => $this->applySiteFilter($query),
            'num' => $this->clampNum($query->limit),
            'gl' => $this->definition->configValue('country', 'us'),
            'hl' => $this->definition->configValue('language', 'en'),
        ]);

        $images = $payload['images'] ?? [];

        if (! is_array($images)) {
            return new SearchResultCollection();
        }

        return new SearchResultCollection(array_map(function (array $hit): array {
            $imageUrl = $this->pickUrl($hit, ['original.link', 'original_url']);
            $thumbnail = $this->pickUrl($hit, ['thumbnail']);
            $pageUrl = $this->pickUrl($hit, ['source.link', 'link']);
            $title = (string) ($hit['title'] ?? 'SearchAPI image result');

            return [
                'title' => $title,
                'page_url' => $pageUrl,
                'image_url' => $imageUrl,
                'thumbnail_url' => $thumbnail,
                'source_domain' => $this->extractDomain($pageUrl) ?? $this->extractDomain($imageUrl),
                'snippet' => $this->pick($hit, ['source.name']),
                'width' => $this->normalizeInt($this->pick($hit, ['original.width'])),
                'height' => $this->normalizeInt($this->pick($hit, ['original.height'])),
                'score' => null,
                'provider_metadata' => [
                    'provider' => $this->definition->code,
                    'position' => $hit['position'] ?? null,
                ],
            ];
        }, array_values(array_filter($images, static fn (mixed $entry): bool => is_array($entry)))));
    }

    public function searchWeb(SearchQueryData $query): SearchResultCollection
    {
        $payload = $this->request([
            'engine' => 'google',
            'q' => $this->applySiteFilter($query),
            'num' => $this->clampNum($query->limit),
            'gl' => $this->definition->configValue('country', 'us'),
            'hl' => $this->definition->configValue('language', 'en'),
        ]);

        $results = $payload['organic_results'] ?? [];

        if (! is_array($results)) {
            return new SearchResultCollection();
        }

        return new SearchResultCollection(array_map(function (array $hit): array {
            $pageUrl = is_string($hit['link'] ?? null) ? $hit['link'] : null;

            return [
                'title' => (string) ($hit['title'] ?? 'Untitled result'),
                'page_url' => $pageUrl,
                'image_url' => null,
                'thumbnail_url' => $this->pickUrl($hit, ['thumbnail', 'favicon']),
                'source_domain' => $this->extractDomain($pageUrl) ?? $this->normalizeDomain($this->pick($hit, ['source'])),
                'snippet' => is_string($hit['snippet'] ?? null) ? $hit['snippet'] : null,
                'width' => null,
                'height' => null,
                'score' => null,
                'provider_metadata' => [
                    'provider' => $this->definition->code,
                    'position' => $hit['position'] ?? null,
                ],
            ];
        }, array_values(array_filter($results, static fn (mixed $entry): bool => is_array($entry)))));
    }

    /**
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>
     */
    private function request(array $query): array
    {
        $this->assertHttpClientAvailable();

        $response = \Illuminate\Support\Facades\Http::baseUrl($this->definition->baseUrl ?? 'https://www.searchapi.io')
            ->acceptJson()
            ->withHeaders([
                'Authorization' => 'Bearer '.((string) $this->definition->apiKey),
            ])
            ->timeout($this->definition->timeoutSeconds)
            ->get('/api/v1/search', array_filter(
                $query,
                static fn (mixed $value): bool => $value !== null && $value !== '',
            ));

        $payload = $response->throw()->json();

        if (! is_array($payload)) {
            throw new RuntimeException('Unexpected SearchAPI.io payload: not a JSON object.');
        }

        return $payload;
    }

    private function clampNum(int $limit): int
    {
        return max(1, min(100, $limit));
    }
}
