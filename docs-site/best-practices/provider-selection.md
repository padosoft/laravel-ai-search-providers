---
title: Provider Selection
description: Choose provider priority and fallback order.
---

# Provider Selection

Start with the provider that best matches the query type and domain. Put cheaper or free fallbacks later if they have weaker coverage or higher anti-bot risk.

| Need | Good first choices |
|---|---|
| Product images | Brave, Tavily, Exa, Firecrawl, SearchAPI.io |
| Organic web results | Brave, Tavily, WebSearchAPI.ai, SearchAPI.io, You.com |
| Free smoke test | Fake, DuckDuckGo |
| Offline CI | Fake |

::: callout info "Priority is ascending"
Lower `priority` values run earlier.
:::

