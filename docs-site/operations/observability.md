---
title: Observability
description: Capture provider attempt events.
---

# Observability

Bind `SearchEventLoggerInterface` to record provider successes and failures.

```php
$this->app->bind(
    \Padosoft\LaravelAiSearchProviders\Contracts\SearchEventLoggerInterface::class,
    \App\Search\SearchAuditLogger::class,
);
```

::: callout info "Return type is mixed"
The logger contract intentionally allows existing host application loggers to return database rows, IDs, or nothing.
:::

## Useful dimensions

- Provider `code` and `driver`.
- Method: `searchImages` or `searchWeb`.
- Result count.
- Failure message.
- `used_fallback`.
- Query metadata such as product or tenant ID.

