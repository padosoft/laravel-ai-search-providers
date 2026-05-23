# Changelog

All notable changes to `padosoft/laravel-ai-search-providers` are documented in this file. The format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/) and the project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] — 2026-05-23

Initial public release. The package extracts the production-hardened search layer from [`padosoft/product-image-discovery`](https://github.com/padosoft/product-image-discovery) into a standalone Laravel composer package.

### Added

- **7 search providers**, each behind the same `SearchProviderInterface` contract:
  - `fake` — deterministic, for tests and the Quick Start.
  - `brave` — Brave Search API, image + web.
  - `tavily` — Tavily JSON search with `include_images` and `include_domains` site filter; handles legacy and modern `images[]` payload shapes.
  - `exa` — Exa.ai `POST /search` with `contents.extras.imageLinks` flattened 1:N into candidates.
  - `firecrawl` — Firecrawl `POST /v2/search` with `sources:[{type:"images"}]` / `[{type:"web"}]` and native `includeDomains`.
  - `websearchapi` — WebSearchAPI.ai `POST /ai-search`, web-only (`supportsImageSearch() === false`).
  - `duckduckgo` — HTML lite scraper, no API key, web-only, `uddg` redirect decoder, anti-bot-aware live test.
- `SearchProviderManager` orchestrator: priority-based fallback across active providers, never leaks `api_key`/`api_secret` in attempt metadata, optional `SearchEventLoggerInterface` hook.
- `AbstractHttpSearchProvider` base class with shared HTTP/parsing helpers.
- Eloquent persistence: `SearchProviderConfig` model (table from config), `create_search_providers_table` migration (Schema::hasTable() guarded), `EloquentSearchProviderConfigRepository` with optional model override via constructor or `config('ai-search-providers.model')`.
- Publishable config + migrations via `php artisan vendor:publish --tag=ai-search-providers-config` / `--tag=ai-search-providers-migrations`.
- Auto-discoverable service provider that registers the 7 default factories; user overrides via `config('ai-search-providers.factories')` are merged on top.
- 64 unit + feature tests via `Http::fake` and in-memory SQLite.
- 6 opt-in live E2E tests skipping cleanly when API keys are absent.
- GitHub Actions CI: PHP 8.3 + PHP 8.4 matrix, `composer validate --strict` + `vendor/bin/phpunit`.
- Community-grade README with badges, junior-friendly Quick Start, mermaid architecture diagram, per-provider activation snippets, custom-driver extension guide, configuration reference, contributing guide.
- Apache-2.0 license.

[1.0.0]: https://github.com/padosoft/laravel-ai-search-providers/releases/tag/v1.0.0
