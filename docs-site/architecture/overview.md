---
title: Architecture Overview
description: Main components and dependencies.
---

# Architecture Overview

```mermaid
flowchart LR
    App[Laravel app] --> Manager[SearchProviderManager]
    Manager --> Repo[SearchProviderConfigRepositoryInterface]
    Repo --> Model[SearchProviderConfig]
    Model --> DB[(search_providers)]
    Manager --> Factories[Factory registry]
    Factories --> Drivers[Provider drivers]
    Drivers --> APIs[External APIs]
    Drivers --> Results[SearchResultCollection]
    Manager -. optional .-> Logger[SearchEventLoggerInterface]
```

## Components

| Component | Responsibility |
|---|---|
| `SearchProviderManager` | Orchestration, fallback, skip/failure accounting. |
| `SearchProviderConfigRepositoryInterface` | Supplies active runtime definitions. |
| `CallableSearchProviderFactory` | Wraps closures into factory instances. |
| Provider classes | Translate provider-specific APIs into normalized results. |
| Data objects | Stable query, definition, result, and execution contracts. |

