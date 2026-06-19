---
title: Gotchas & Limits
description: Known limitations and integration pitfalls.
---

# Gotchas & Limits

::: callout warning "DuckDuckGo can throttle datacenter IPs"
The DuckDuckGo HTML lite driver has no API key, but shared CI or server IPs may receive anti-bot responses. Treat it as a fallback, not a high-volume primary.
:::

::: callout warning "Image support differs by driver"
Web-only drivers are skipped for `searchImages()`. This is expected and appears in the execution attempts.
:::

::: callout warning "Rate limit metadata is not enforcement"
`rate_limit_per_minute` helps document provider capacity but does not throttle calls by itself.
:::

## Limits to model in host apps

- Provider quota and billing.
- Regional API behavior.
- Search result freshness.
- Duplicate image URLs across providers.
- Page URLs that redirect or block image scraping.

