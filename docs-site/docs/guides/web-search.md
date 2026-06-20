---
title: Web Search
description: Search normalized organic web results.
---

# Web Search

Use `searchWeb()` for source discovery, snippets, citations, and agent context gathering.

```php
$execution = app(\Padosoft\LaravelAiSearchProviders\SearchProviderManager::class)
    ->searchWeb(\Padosoft\LaravelAiSearchProviders\Data\SearchQueryData::fromArray([
        'query' => 'Laravel package AI search providers',
        'limit' => 5,
    ]));
```

::: tabs
== tab "Direct query"
Set `query` when you already have a natural-language search string.

== tab "Product fields"
Set `brand`, `model`, `color`, `ean`, or `supplier_sku` and `toSearchString()` joins the populated fields.
:::

