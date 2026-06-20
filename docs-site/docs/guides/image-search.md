---
title: Image Search
description: Search for normalized image candidates.
---

# Image Search

Use `searchImages()` when the downstream workflow needs `image_url`, dimensions, thumbnails, and source attribution.

```php
$execution = app(\Padosoft\LaravelAiSearchProviders\SearchProviderManager::class)
    ->searchImages(\Padosoft\LaravelAiSearchProviders\Data\SearchQueryData::fromArray([
        'brand' => 'Adidas',
        'model' => 'Samba OG',
        'color' => 'Core Black',
        'site' => 'adidas.com',
        'limit' => 10,
    ]));
```

::: callout warning "Web-only drivers are skipped"
`websearchapi`, `duckduckgo`, and `youcom` return `supportsImageSearch() === false`. The manager records a skipped attempt and moves to the next provider.
:::

## Result contract

Every image result is represented by `SearchResult`, with fields such as `title`, `page_url`, `image_url`, `thumbnail_url`, `source_domain`, `width`, `height`, `score`, and `provider_metadata`.

