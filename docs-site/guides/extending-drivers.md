---
title: Extending Drivers
description: Add a custom search provider driver.
---

# Extending Drivers

A driver implements `SearchProviderInterface`. HTTP-based drivers can extend `AbstractHttpSearchProvider` to reuse helpers for URL picking, domain extraction, numeric normalization, and site filtering.

```php
final class SerperSearchProvider extends AbstractHttpSearchProvider
{
    public function searchImages(SearchQueryData $query): SearchResultCollection
    {
        $payload = Http::baseUrl($this->definition->baseUrl ?? 'https://google.serper.dev')
            ->withHeaders(['X-API-KEY' => (string) $this->definition->apiKey])
            ->post('/images', ['q' => $this->applySiteFilter($query), 'num' => $query->limit])
            ->throw()
            ->json();

        return new SearchResultCollection(array_map(fn (array $hit): array => [
            'title' => $hit['title'] ?? 'Untitled',
            'page_url' => $hit['link'] ?? null,
            'image_url' => $hit['imageUrl'] ?? null,
            'thumbnail_url' => $hit['thumbnailUrl'] ?? null,
        ], $payload['images'] ?? []));
    }
}
```

::: callout info "Registration point"
Add the factory under `ai-search-providers.factories`, then insert a row with the same `driver` string.
:::

