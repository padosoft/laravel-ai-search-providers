# Laravel AI Search Providers — Progress

Tracker for the multi-PR effort that extracts the search layer from `padosoft/product-image-discovery` into a standalone, community-grade package shipped on Packagist.

## Phase A — Build the new package

| # | PR | Branch | Status |
|---|---|---|---|
| A1 | Scaffold package | `feat/scaffold` | 🟡 in progress |
| A2 | Contracts + DTOs | `feat/contracts-dtos` | ⬜ pending |
| A3 | Manager + Abstract + Fake + factories | `feat/manager-and-abstract` | ⬜ pending |
| A4 | 6 live providers (Brave, Tavily, Exa, Firecrawl, WebSearchAPI, DuckDuckGo) | `feat/providers` | ⬜ pending |
| A5 | Persistence: Eloquent model + migration + repository | `feat/persistence` | ⬜ pending |
| A6 | Community README + live E2E + tag v1.0.0 | `feat/docs-and-live` | ⬜ pending |

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

Gates to verify before push:

- `composer install` succeeds.
- `vendor/bin/phpunit --testsuite Unit` PASS (3 tests).
- `composer validate --strict --no-check-publish` PASS.
