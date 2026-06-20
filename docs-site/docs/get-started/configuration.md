---
title: Configuration
description: Configure the table, model, migrations, and custom factories.
---

# Configuration

The package merges `config/ai-search-providers.php` into the host application.

| Key | Default | Purpose |
|---|---|---|
| `table` | `search_providers` | Database table read by the model and migration. |
| `model` | `null` | Optional Eloquent model override. |
| `load_migrations` | `true` | Toggles package migration auto-loading. |
| `factories` | `[]` | Driver factory overrides or custom driver registrations. |

::: callout warning "Config rows are runtime routing"
Provider activation is not controlled by environment variables alone. The manager reads active `SearchProviderConfig` rows, then resolves the row's `driver` through the factory registry.
:::

## Custom table

```env
AI_SEARCH_PROVIDERS_TABLE=product_search_providers
```

## Factory override

```php
'factories' => [
    'serper' => static fn ($definition) => new \App\Search\SerperSearchProvider($definition),
],
```

