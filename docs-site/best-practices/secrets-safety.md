---
title: Secrets & Safety
description: Handling provider credentials safely.
---

# Secrets & Safety

Use Laravel encrypted casts by writing credentials into `api_key_encrypted` and `api_secret_encrypted`.

::: callout danger "Do not log provider definitions directly"
Use `SearchProviderDefinition::toSafeArray()` for logs and audit metadata. It redacts secret values by design.
:::

## Operational checklist

::: steps
1. **Set provider keys in environment-specific secret storage.**
2. **Insert or update encrypted columns through Eloquent.**
3. **Keep `attempts` for debugging, not raw HTTP payloads with headers.**
4. **Rotate keys provider by provider using priority fallback.**
:::

