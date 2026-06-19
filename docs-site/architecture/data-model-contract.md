---
title: Data Model & Contract
description: Database schema and value objects.
---

# Data Model & Contract

## Table

| Column | Purpose |
|---|---|
| `code` | Stable provider code, unique. |
| `driver` | Factory lookup key. |
| `base_url` | Provider endpoint override. |
| `api_key_encrypted` | Encrypted API key. |
| `api_secret_encrypted` | Encrypted API secret. |
| `config` | Provider-specific JSON. |
| `priority` | Ascending fallback order. |
| `timeout_seconds` | HTTP timeout budget. |
| `rate_limit_per_minute` | Advisory quota metadata. |
| `is_active` | Runtime activation flag. |

## Contract

```php
interface SearchProviderInterface
{
    public function searchImages(SearchQueryData $query): SearchResultCollection;
    public function searchWeb(SearchQueryData $query): SearchResultCollection;
    public function supportsImageSearch(): bool;
    public function supportsSiteFilter(): bool;
}
```

