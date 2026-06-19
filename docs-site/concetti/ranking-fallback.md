---
title: Ranking & Fallback
description: How provider order, skips, empty results, and failures interact.
---

# Ranking & Fallback

The manager sorts active providers by ascending `priority`, then tries each provider in order.

```mermaid
flowchart TD
    A[Load active definitions] --> B[Sort by priority]
    B --> C{Provider supports method?}
    C -- no --> D[Record skipped]
    C -- yes --> E[Call driver]
    E --> F{Results?}
    F -- yes --> G[Return execution]
    F -- empty --> H[Record empty]
    E -- throws --> I[Record failed]
    D --> J[Next provider]
    H --> J
    I --> J
```

::: callout info "Attempt logs are part of the return value"
Use `attempts` for debugging and audit trails. Bind `SearchEventLoggerInterface` when you need central observability.
:::

