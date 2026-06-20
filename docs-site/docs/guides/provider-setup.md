---
title: Provider Setup
description: Activate built-in search providers.
---

# Provider Setup

Each live provider follows the same shape: store the API key in `.env`, create an active `SearchProviderConfig` row, and set a priority.

| Driver | Image search | Site filter | Notes |
|---|---:|---:|---|
| `fake` | yes | yes | Deterministic, no network. |
| `brave` | yes | yes | Uses Brave Search API. |
| `tavily` | yes | yes | Normalizes legacy and current `images` shapes. |
| `exa` | yes | yes | Flattens `extras.imageLinks`. |
| `firecrawl` | yes | yes | Synchronous search, use longer timeout. |
| `websearchapi` | no | yes | Web-only organic results. |
| `duckduckgo` | no | yes | HTML lite parser, no key. |
| `searchapi` | yes | yes | Google and Google Images engines. |
| `youcom` | no | yes | Web-only endpoint with thumbnails. |

::: collapsible "Example: Brave row"
```php
\Padosoft\LaravelAiSearchProviders\Models\SearchProviderConfig::query()->create([
    'code' => 'brave',
    'name' => 'Brave Search',
    'driver' => 'brave',
    'base_url' => 'https://api.search.brave.com',
    'api_key_encrypted' => env('BRAVE_SEARCH_API_KEY'),
    'priority' => 10,
    'timeout_seconds' => 15,
    'is_active' => true,
]);
```
:::

::: collapsible "Example: Firecrawl row"
```php
\Padosoft\LaravelAiSearchProviders\Models\SearchProviderConfig::query()->create([
    'code' => 'firecrawl',
    'name' => 'Firecrawl',
    'driver' => 'firecrawl',
    'base_url' => 'https://api.firecrawl.dev',
    'api_key_encrypted' => env('FIRECRAWL_API_KEY'),
    'priority' => 40,
    'timeout_seconds' => 60,
    'rate_limit_per_minute' => 30,
    'is_active' => true,
]);
```
:::

