---
title: Provider Model
description: Active provider rows and runtime definitions.
---

# Provider Model

`SearchProviderConfig` is the persisted Eloquent model. `SearchProviderDefinition` is the runtime immutable value object passed to factories and providers.

::: grids
::: grid
::: card "Database row" icon:database
Stores activation, priority, encrypted secrets, base URL, timeout, and custom JSON config.
:::
:::
::: grid
::: card "Definition" icon:file-code
Normalizes row attributes and exposes `toSafeArray()` for logs and attempts.
:::
:::
:::

## Safe serialization

`toSafeArray()` includes `has_api_key` and `has_api_secret`, but never includes the secret values.

