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

## [1.2.1] — 2026-05-23

### Changed

- README copy alignment with the v1.2.0 driver count: the tagline ("9 providers. 1 interface. 0 boilerplate.") and the lead Features bullet ("9 providers out of the box — …") now match the provider matrix and the `SUPPORTED PROVIDERS` section. The lead sentence credits all eight live drivers explicitly (was missing SearchAPI.io and You.com).
- Roadmap entry added to rerender the community banner artwork — the current PNG, baked at v1.0.0, still reads "7 providers".

## [1.2.0] — 2026-05-23

### Added

- **`SearchApiSearchProvider`** (driver `searchapi`) — single `GET /api/v1/search` endpoint with `engine=google_images` for image search and `engine=google` for web search. `Authorization: Bearer` auth. Site filter applied as `site:<host>` operator. Parses `images[]` (`original.link`, `original.{width,height}`, `thumbnail`, `source.{link,name}`) and `organic_results[]` (`link`, `title`, `snippet`, `source`, `thumbnail`).
- **`YouComSearchProvider`** (driver `youcom`) — `GET /v1/search` against `https://ydc-index.io` with `X-API-Key` header. Web-only (`supportsImageSearch() === false`) because You.com does not expose a dedicated image-search endpoint as of 2026-05. Parses `results.web[]` with `title`, `url`, `description`, `snippets[]`, `thumbnail_url`, `page_age`, `favicon_url`. Site filter propagated as `include_domains` (comma-separated). Falls back to the first non-empty `snippets[]` entry when `description` is missing.
- Service provider registers both factories alongside the previous 7.
- 11 new unit tests (5 for SearchAPI.io, 6 for You.com) + 2 opt-in live E2E tests. Total package suite now 83 / 232.
- README provider matrix expanded to 9 drivers; per-provider activation sections added.
- Roadmap notes the intentional skip of Google Custom Search JSON API (closed to new customers in 2026; new CSE engines can no longer index the full web) and pencils in a Vertex AI Search / Agent Search adapter as a future sub-package.

## [1.0.1] — 2026-05-23

### Added

- Community banner in README (`resources/laravel-ai-search-providers-banner.png`), placed between the badges and the table of contents. Showcases the 7-providers / 1-interface / 0-boilerplate pitch + the SearchProviderManager routing diagram + the Quick Install snippet + the eight feature pillars (priority + fallback, fallback-ready, secrets-safe, test-first, production-ready, Laravel-native).

[1.0.0]: https://github.com/padosoft/laravel-ai-search-providers/releases/tag/v1.0.0
[1.0.1]: https://github.com/padosoft/laravel-ai-search-providers/releases/tag/v1.0.1
[1.2.0]: https://github.com/padosoft/laravel-ai-search-providers/releases/tag/v1.2.0
[1.2.1]: https://github.com/padosoft/laravel-ai-search-providers/releases/tag/v1.2.1
