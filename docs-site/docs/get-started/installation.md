---
title: Installation
description: Requirements, Composer install, publishing, and migration setup.
---

# Installation

## Requirements

| Requirement | Version |
|---|---|
| PHP | `^8.3` |
| Laravel components | `^11.0`, `^12.0`, or `^13.0` |
| Extensions | `ext-dom`, `ext-libxml` |

## Composer

```bash
composer require padosoft/laravel-ai-search-providers
```

Laravel package auto-discovery registers `LaravelAiSearchProvidersServiceProvider`.

## Publish optional files

::: tabs
== tab "Config"
```bash
php artisan vendor:publish --tag=ai-search-providers-config
```

== tab "Migrations"
```bash
php artisan vendor:publish --tag=ai-search-providers-migrations
```
:::

::: callout info "Migrations are auto-loaded"
The default configuration sets `load_migrations` to true, so `php artisan migrate` creates `search_providers` without publishing the migration first.
:::

