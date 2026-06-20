---
title: Configuration API
description: Config keys and database activation.
---

# Configuration API

## `ai-search-providers.php`

```php
return [
    'table' => env('AI_SEARCH_PROVIDERS_TABLE', 'search_providers'),
    'model' => null,
    'load_migrations' => env('AI_SEARCH_PROVIDERS_LOAD_MIGRATIONS', true),
    'factories' => [],
];
```

## Provider row minimum

```php
[
    'code' => 'fake',
    'name' => 'Fake',
    'driver' => 'fake',
    'priority' => 1,
    'is_active' => true,
]
```

