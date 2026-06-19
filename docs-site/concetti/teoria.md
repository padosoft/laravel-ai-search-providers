---
title: Teoria
description: Provider abstraction, normalization, and fallback theory.
---

# Teoria

Un provider di ricerca è una funzione parziale:

$$
provider(query, config) \rightarrow results \mid empty \mid failure
$$

Il manager compone più provider ordinati:

$$
execution = firstSuccess(sort(activeProviders, priority))
$$

::: callout warning "La priorità non è qualità assoluta"
`priority` decide l'ordine operativo. La qualità reale dipende da dominio, lingua, quota, latenza, endpoint e tipo di ricerca.
:::

## Normalizzazione

Ogni driver traduce il payload nativo in `SearchResultCollection`. Questo sposta la complessità ai bordi e mantiene stabile il codice applicativo.

