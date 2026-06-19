---
title: Motivazione
description: Why the package exists.
---

# Motivazione

Le applicazioni AI hanno spesso bisogno dello stesso primitivo: cercare il web, normalizzare i risultati, e consegnarli a pipeline successive. Ogni provider espone payload, limiti e semantiche diverse.

::: callout info "Obiettivo"
Ridurre il costo di cambiare provider da una riscrittura applicativa a una modifica di configurazione e una riga nel database.
:::

## Problema

Senza astrazione, ogni integrazione introduce codice duplicato: autenticazione, timeout, parsing, retry, normalizzazione, test, log e gestione dei segreti.

## Soluzione

`SearchProviderManager` incapsula la scelta del provider e restituisce un contratto comune. I driver restano piccoli e specifici del provider.

