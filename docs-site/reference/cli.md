---
title: CLI
description: Package and docs commands.
---

# CLI

## Laravel package

```bash
composer require padosoft/laravel-ai-search-providers
php artisan vendor:publish --tag=ai-search-providers-config
php artisan vendor:publish --tag=ai-search-providers-migrations
php artisan migrate
vendor/bin/phpunit --testsuite Unit,Feature
```

## Docs site

```bash
npm run dev
npm run check
npm run build
```

## Semantic search index

```bash
npx docmd-search docs-site --out _site/.docmd-search
```

