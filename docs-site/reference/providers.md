---
title: Providers
description: Built-in provider reference.
---

# Providers

| Driver | Class | Image | Web |
|---|---|---:|---:|
| `fake` | `FakeSearchProvider` | yes | yes |
| `brave` | `BraveSearchProvider` | yes | yes |
| `tavily` | `TavilySearchProvider` | yes | yes |
| `exa` | `ExaSearchProvider` | yes | yes |
| `firecrawl` | `FirecrawlSearchProvider` | yes | yes |
| `websearchapi` | `WebSearchApiSearchProvider` | no | yes |
| `duckduckgo` | `DuckDuckGoSearchProvider` | no | yes |
| `searchapi` | `SearchApiSearchProvider` | yes | yes |
| `youcom` | `YouComSearchProvider` | no | yes |

::: callout tip "Provider metadata is preserved"
Use `provider_metadata` for provider-specific fields that do not belong in the normalized top-level result contract.
:::

