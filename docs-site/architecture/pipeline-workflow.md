---
title: Pipeline Workflow
description: End-to-end request workflow.
---

# Pipeline Workflow

```mermaid
sequenceDiagram
    participant Code as Application code
    participant Manager as SearchProviderManager
    participant Repo as Config repository
    participant Factory as Factory registry
    participant Provider as Search provider
    Code->>Manager: searchWeb or searchImages
    Manager->>Repo: getActiveProviders
    Repo-->>Manager: definitions
    Manager->>Manager: sort by priority
    Manager->>Factory: make(definition)
    Factory-->>Manager: provider
    Manager->>Provider: execute query
    Provider-->>Manager: SearchResultCollection
    Manager-->>Code: SearchProviderExecutionResult
```

::: callout warning "No built-in runtime rate limiter"
`rate_limit_per_minute` is stored and exposed as metadata, but runtime enforcement is currently advisory. Add a decorator factory if the host app must hard-enforce quotas.
:::

