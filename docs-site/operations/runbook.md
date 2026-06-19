---
title: Runbook
description: Diagnose provider failures and fallback behavior.
---

# Runbook

## Provider returns no results

::: steps
1. **Check active rows**

   Confirm at least one active row exists and has the expected `driver`.

2. **Inspect attempts**

   Review `SearchProviderExecutionResult::toArray()['attempts']`.

3. **Check support flags**

   A skipped image provider may simply be web-only.

4. **Validate credentials**

   Rotate or reinsert encrypted keys if provider APIs return authorization failures.
:::

## Emergency fallback

Deactivate failing providers or increase their `priority` value so another provider runs first.

