# Laravel AI Search Providers — Progress

Tracker for the multi-PR effort that extracts the search layer from `padosoft/product-image-discovery` into a standalone, community-grade package shipped on Packagist.

## Phase A — Build the new package

| # | PR | Branch | Status |
|---|---|---|---|
| A1 | Scaffold package | `feat/scaffold` | ✅ merged (PR #1) |
| A2 | Contracts + DTOs | `feat/contracts-dtos` | ✅ merged (PR #2) |
| A3 | Manager + Abstract + Fake + factories | `feat/manager-and-abstract` | ✅ merged (PR #3) |
| A4 | 6 live providers (Brave, Tavily, Exa, Firecrawl, WebSearchAPI, DuckDuckGo) | `feat/providers` | ✅ merged (PR #4) |
| A5 | Persistence: Eloquent model + migration + repository | `feat/persistence` | ✅ merged (PR #5) |
| A6 | Community README + live E2E + tag v1.0.0 | `feat/docs-and-live` | ✅ merged + released |
| — | Banner asset + badge URL verification (docs-only patch) | `docs/banner` | ✅ released as v1.0.1 |
| — | SearchAPI.io + You.com drivers (Google CSE skipped as deprecated) | `feat/searchapi-and-youcom` | 🟡 in progress (target v1.2.0) |

## Phase B — Re-wire `product-image-discovery` consumer

| # | PR | Branch | Status |
|---|---|---|---|
| B1 | Depend on package, drop in-tree search layer | `refactor/depend-on-ai-search-providers` | ⬜ pending |
| B2 | Tag `product-image-discovery` v1.0.0 post-extraction | `chore/v1-release` | ⬜ pending |

## Per-PR gates

Every PR satisfies all of:

- Local PHPUnit gate green.
- Local `composer validate --strict` green (where applicable).
- Local Copilot review on the diff.
- Push, open PR, GitHub Actions CI green on every job.
- Copilot PR review loop until zero new comments.
- Squash-merge into `main`.
- `docs/PROGRESS.md` updated and `docs/LESSON.md` appended.

## Session 2026-05-23

### A1 — Scaffold

In progress. Completed sub-tasks:

- Created `composer.json` with name `padosoft/laravel-ai-search-providers`, Apache-2.0 license, PHP `^8.3`, illuminate `^11.0 || ^12.0 || ^13.0`, Orchestra Testbench + PHPUnit dev deps, PSR-4 autoload, Laravel auto-discoverable service provider.
- Created `phpunit.xml.dist` with `Unit`/`Feature`/`E2E` testsuites.
- Created `.gitignore` (vendor, .phpunit.cache, .env, .idea, .vscode, .lock).
- Created `.github/workflows/ci.yml` with `php-tests` matrix on PHP 8.3 and 8.4.
- Created stub `src/LaravelAiSearchProvidersServiceProvider.php` with `mergeConfigFrom` + publishable config.
- Created `config/ai-search-providers.php` with `table` (default `search_providers`), `model`, `load_migrations`, `factories` keys.
- Created `tests/TestCase.php` (Orchestra Testbench base with SQLite in-memory) and `tests/Unit/ServiceProviderTest.php` (3 sanity tests).
- Created `README.md` stub (badges + under construction banner) and `CHANGELOG.md` skeleton.
- Created `docs/PROGRESS.md` (this file) and `docs/LESSON.md`.

Gates verified:

- `composer install` succeeded locally.
- `vendor/bin/phpunit --testsuite Unit` PASS — 3 tests, 7 assertions.
- `composer validate --strict --no-check-publish` PASS.
- CI green on PHP 8.3 + 8.4 (after a placeholder `tests/Feature/.gitkeep` and `tests/E2E/.gitkeep` were added — PHPUnit 12 requires every declared testsuite directory to exist).
- Merged as PR #1.

### A2 — Contracts + DTOs

In progress. Completed sub-tasks:

- `src/Contracts/SearchProviderInterface.php` (renamed from `ProductImageSearchProviderInterface`) — `searchImages`, `searchWeb`, `supportsImageSearch`, `supportsSiteFilter`.
- `src/Contracts/SearchProviderFactoryInterface.php`.
- `src/Contracts/SearchProviderConfigRepositoryInterface.php`.
- `src/Contracts/SearchEventLoggerInterface.php`.
- `src/Data/SearchQueryData.php` (renamed from `ProductImageSearchQueryData`).
- `src/Data/SearchResult.php` (renamed from `ProductImageSearchResult`).
- `src/Data/SearchResultCollection.php` (renamed from `ProductImageSearchResultCollection`).
- `src/Data/SearchProviderDefinition.php`.
- `src/Data/SearchProviderExecutionResult.php`.
- 18 new unit tests across `tests/Unit/Data/{SearchQueryData,SearchResult,SearchResultCollection,SearchProviderDefinition}Test.php`.

Gate: `vendor/bin/phpunit --testsuite Unit,Feature,E2E` PASS — 21 tests, 66 assertions. CI green. Merged as PR #2.

### A3 — Manager + Abstract base + Fake + factories

In progress. Completed sub-tasks:

- `src/Providers/AbstractHttpSearchProvider.php` — shared HTTP/parsing helpers (pickUrl, pick, dotGet, extractDomain, normalizeDomain, normalizeInt, normalizeFloat, applySiteFilter, assertHttpClientAvailable).
- `src/Providers/FakeSearchProvider.php` — deterministic provider for tests/smoke runs. Supports `throw`/`throw_for` failure modes and `supports_image_search`/`supports_site_filter` config overrides.
- `src/CallableSearchProviderFactory.php` — closure-based factory.
- `src/SearchProviderManager.php` — depends on `SearchProviderConfigRepositoryInterface` and optional `SearchEventLoggerInterface`. Skips providers that don't support image search or site filter; falls back across active providers in priority order; logs success/failure when a logger is bound.
- `src/EmptyConfigRepository.php` (internal) — bootstrap-safe stand-in used by the service provider until the Eloquent repository lands in PR A5. Returns no providers so the manager degrades gracefully.
- `tests/Support/InMemorySearchProviderConfigRepository.php` — test helper.
- `src/LaravelAiSearchProvidersServiceProvider.php` — extended:
  - Registers `SearchProviderManager` as singleton with the default `fake` factory installed.
  - Merges any user-provided factories from `config('ai-search-providers.factories')` on top of the defaults. Accepts closure, callable, FQCN of `SearchProviderFactoryInterface` or already-resolved instance.
- 11 new unit tests across `tests/Unit/SearchProviderManagerTest.php` (5 scenarios: empty active set, fallback + safe attempts, skip unsupported image search, skip unsupported site filter, raise on missing factory) and `tests/Unit/Providers/FakeSearchProviderTest.php` (6 scenarios).

Gate: `vendor/bin/phpunit --testsuite Unit,Feature,E2E` PASS — 32 tests, 94 assertions. CI green. Merged as PR #3.

### A4 — Provider implementations

In progress. Completed sub-tasks:

- Moved 6 driver classes from `product-image-discovery/src/Services/Search/` to `src/Providers/`, rewriting namespaces to `Padosoft\LaravelAiSearchProviders\Providers\` and DTO imports to `Padosoft\LaravelAiSearchProviders\Data\{SearchQueryData,SearchResultCollection,SearchProviderDefinition}`. No behavior change.
  - `BraveSearchProvider`, `TavilySearchProvider`, `ExaSearchProvider`, `FirecrawlSearchProvider`, `WebSearchApiSearchProvider`, `DuckDuckGoSearchProvider`.
- Moved 6 unit tests + DDG fixture to `tests/Unit/Providers/` with the same namespace/import rewrites.
- ServiceProvider now registers all 7 driver factories (`fake`, `brave`, `tavily`, `exa`, `firecrawl`, `websearchapi`, `duckduckgo`) as defaults, merged with any user overrides from `config('ai-search-providers.factories')`.

Gate: `vendor/bin/phpunit --testsuite Unit,Feature,E2E` PASS — 59 tests, 162 assertions. CI green. Merged as PR #4.

### A5 — Persistence

In progress. Completed sub-tasks:

- `src/Models/SearchProviderConfig.php` — Eloquent model with `active`/`ordered` scopes. `getTable()` reads `config('ai-search-providers.table')` at runtime (default `search_providers`).
- `database/migrations/2026_05_23_000001_create_search_providers_table.php` — creates the table using the configured name, wrapped in `Schema::hasTable()` skip-if-exists so host apps that already have a legacy table (e.g. `product_image_search_providers`) don't break.
- `src/Repositories/EloquentSearchProviderConfigRepository.php` — accepts optional `?string $providerModel` constructor override; resolves model class via constructor → `config('ai-search-providers.model')` → package default.
- `LaravelAiSearchProvidersServiceProvider`:
  - Binds `SearchProviderConfigRepositoryInterface` to `EloquentSearchProviderConfigRepository`.
  - `loadMigrationsFrom()` when `config('ai-search-providers.load_migrations')` is true.
  - `publishes()` migrations for the `ai-search-providers-migrations` tag.
- Removed the now-redundant `EmptyConfigRepository` internal helper.
- `tests/Feature/Persistence/EloquentSearchProviderConfigRepositoryTest.php` — 5 feature tests covering default table name, config-driven override, active+ordered query, constructor override priority, empty-state behavior.

Gate: `vendor/bin/phpunit --testsuite Unit,Feature,E2E` PASS — 64 tests, 170 assertions. CI green. Merged as PR #5.

### A6 — Live E2E + community README + tag v1.0.0

In progress. Completed sub-tasks:

- `tests/Concerns/ReadsLocalEnv.php` trait that reads keys from process env or the package-local `.env` file.
- 6 opt-in live E2E tests under `tests/E2E/` — `LiveBraveSearchProviderTest`, `LiveTavilySearchProviderTest`, `LiveExaSearchProviderTest`, `LiveFirecrawlSearchProviderTest`, `LiveWebSearchApiSearchProviderTest`, `LiveDuckDuckGoSearchProviderTest`. Each skips cleanly without the relevant env key. DDG additionally skips on `CI=true` and on 403/429/503 anti-bot responses.
- `.env.example` documenting every env key with empty values.
- Community-grade `README.md` with Packagist/PHP/Laravel/license/CI badges, TOC, value proposition, feature bullets, full provider matrix, junior-friendly 5-minute Quick Start using the fake driver, per-provider activation snippets, mermaid architecture diagram, configuration reference, custom-driver extension example (Serper.dev), testing section, BC tips for host apps, roadmap, contributing.
- `CHANGELOG.md` entry for v1.0.0 covering the full package surface.

Gates verified:
- `vendor/bin/phpunit --testsuite Unit,Feature,E2E` PASS — 70 tests, 195 assertions, 0 skipped (with all live keys present in local `.env`).
- All 5 live API providers (Brave, Tavily, Exa, Firecrawl, WebSearchAPI) returned ≥ 1 result on the Nike smoke query against real APIs.
- DuckDuckGo HTML lite live run returned ≥ 1 result without a key.
- `composer validate --strict --no-check-publish` PASS.
