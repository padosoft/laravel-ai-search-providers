# Laravel AI Search Providers — Lessons Learned

Append-only knowledge base. Each new session adds entries for traps, surprising behaviors, design choices that paid off, and decisions worth remembering.

## Origin

This package is the extraction of the search layer originally built inside `padosoft/product-image-discovery`. The pre-extraction lessons (Brave image-payload shape, Tavily union-typed images[], Exa imageLinks 1:N flattening, Firecrawl v2 sources schema, WebSearchAPI ai-search response shape, DuckDuckGo HTML lite parsing + uddg redirect decoding, Node 24 for the sidecar isolation flag, etc.) are documented in the sibling repo at `docs/LESSON.md` of `padosoft/product-image-discovery`. They are reproduced here as the package matures so the package becomes the self-contained source of truth.

## Package boundary

- The package owns the **search/extraction contract** and all provider implementations.
- The host application (e.g. `product-image-discovery`, `product-pricing-comparison`) owns the **domain pipeline** that consumes the contract.
- Backward compatibility for existing host apps is preserved through two extension points: the configurable backing table (`config('ai-search-providers.table')`) and the configurable Eloquent model (`config('ai-search-providers.model')`). A host that ships a custom subclass of `SearchProviderConfig` can plug it in with one line of config; no destructive migration required.

## Scaffolding decisions

- **Laravel auto-discovery via `extra.laravel.providers`**: removes the need for the consumer to manually register the service provider — composer require is enough.
- **`Schema::hasTable()` guard in the migration**: lets the package's auto-loaded migration coexist with a host-app legacy table without throwing. This is the only way to give junior consumers a `composer require + migrate` zero-friction setup while still preserving brownfield deploys.
- **`config('ai-search-providers.factories')` is merge-on-top of the service-provider defaults**, not replace-all. Hosts can override a single driver (e.g. wrap Brave with a caching decorator) without re-listing the other six.
