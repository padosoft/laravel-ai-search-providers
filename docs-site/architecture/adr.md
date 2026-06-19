---
title: ADR
description: Architecture decision records.
---

# ADR

::: collapsible "ADR 001: Store provider config in the database"
Decision: use `search_providers` rows instead of only static config.

Reason: provider activation, priority, timeout, and credentials often differ by environment and can change without deploys.

Consequence: migrations and secret encryption must be handled correctly in the host app.
:::

::: collapsible "ADR 002: Use one result contract"
Decision: all providers return `SearchResultCollection`.

Reason: downstream AI/catalog workflows need a stable shape even when upstream APIs differ.

Consequence: driver tests must cover provider-specific response quirks.
:::

::: collapsible "ADR 003: Keep rate limits advisory"
Decision: store `rate_limit_per_minute` but do not enforce it in the manager.

Reason: Laravel apps may already use queues, cache locks, external throttlers, or provider-specific budgets.

Consequence: production apps that require strict quotas should wrap providers with a rate-limiting decorator.
:::

