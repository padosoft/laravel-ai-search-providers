# Laravel AI Search Providers

[![Latest Version on Packagist](https://img.shields.io/packagist/v/padosoft/laravel-ai-search-providers.svg?style=flat-square)](https://packagist.org/packages/padosoft/laravel-ai-search-providers)
[![PHP](https://img.shields.io/badge/PHP-8.3%2B-777bb4.svg?style=flat-square)](https://www.php.net/)
[![Laravel](https://img.shields.io/badge/Laravel-11%20%7C%2012%20%7C%2013-ff2d20.svg?style=flat-square)](https://laravel.com/)
[![License](https://img.shields.io/packagist/l/padosoft/laravel-ai-search-providers.svg?style=flat-square)](LICENSE)
[![Tests](https://img.shields.io/github/actions/workflow/status/padosoft/laravel-ai-search-providers/ci.yml?branch=main&label=tests&style=flat-square)](https://github.com/padosoft/laravel-ai-search-providers/actions/workflows/ci.yml)

> 🚧 **Under construction**. The full feature set (7 drivers, Eloquent persistence, live E2E suite, junior Quick Start) lands across PRs A2–A6. Track progress in `docs/PROGRESS.md`.

A plug-and-play Laravel package that exposes a single contract over the modern AI-friendly search and content APIs:

- 🦁 **Brave Search**
- 🌐 **Tavily**
- 🔬 **Exa.ai**
- 🕷️ **Firecrawl**
- 🌎 **WebSearchAPI.ai**
- 🦆 **DuckDuckGo** (HTML lite, no API key)

Use it as the search/extraction backbone for AI agents, RAG pipelines, product catalogs, price comparison engines, brand monitoring — anywhere you need to call multiple search providers from the same Laravel app without rewriting plumbing per API.

## Status

This is the v0.x scaffold. The full v1.0.0 lands in PR A6 with badges, junior Quick Start, per-provider docs, mermaid architecture diagram, and an opt-in live E2E suite. Until then please refer to the [sibling project that pioneered these drivers](https://github.com/padosoft/product_image_discovery) for usage examples.

## License

Apache-2.0. See [LICENSE](LICENSE).
