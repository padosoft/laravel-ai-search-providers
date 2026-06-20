---
title: Design
description: Design decisions and provider boundaries.
---

# Design

The design keeps provider differences behind narrow driver classes and keeps Laravel application code pointed at the manager.

```mermaid
classDiagram
    class SearchProviderManager {
      +searchImages(SearchQueryData)
      +searchWeb(SearchQueryData)
      -execute(method, query)
    }
    class SearchProviderInterface {
      +searchImages(query)
      +searchWeb(query)
      +supportsImageSearch()
      +supportsSiteFilter()
    }
    class SearchProviderDefinition
    class SearchResultCollection
    SearchProviderManager --> SearchProviderInterface
    SearchProviderManager --> SearchProviderDefinition
    SearchProviderInterface --> SearchResultCollection
```

::: callout tip "Boundary rule"
Application code should not parse Brave, Tavily, Exa, or Firecrawl payloads directly. Put provider-specific parsing in a driver and return normalized results.
:::

