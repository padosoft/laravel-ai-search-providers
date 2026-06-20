---
title: Testing
description: Unit, feature, fake-provider, and live E2E testing.
---

# Testing

The package is test-first: driver unit tests use Laravel `Http::fake`, and live E2E tests self-skip when credentials are unavailable.

```bash
vendor/bin/phpunit --testsuite Unit,Feature
```

```bash
vendor/bin/phpunit --testsuite E2E
```

::: callout tip "Prefer fake provider in host apps"
Use the bundled `fake` driver or `InMemorySearchProviderConfigRepository` for application feature tests. It keeps CI deterministic and avoids provider quota drift.
:::

## Fake failure modes

```php
'config' => [
    'throw' => true,
    'throw_for' => ['web'],
]
```

This lets you verify fallback behavior without network calls.

