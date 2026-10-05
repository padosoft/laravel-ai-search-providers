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

The docs tooling is self-contained in `docs-site/`: it has the only `package.json` in the repository, and all commands run from that folder.

```bash
cd docs-site
npm ci
npm run dev
npm run check
npm run build
```

`npm run check` fails on raw HTML or `::: button` containers outside code samples. `npm run build` writes the static site to `docs-site/_site`.

## Semantic search index

`npm run build` also generates the offline semantic index in `docs-site/_site/.docmd-search`, using the source-controlled `docs-site/.docmd-search/config.json`. To index the Markdown sources on their own:

```bash
cd docs-site
npx docmd-search docs
```

## Composer dist archive

`.gitattributes` marks `docs-site/`, `docs/`, `tests/`, `.github/`, `.claude/` and the README banner as `export-ignore`. A `composer require` install contains only the runtime package, so Node tooling and its lockfile never reach `vendor/`. To inspect the archive locally:

```bash
git archive HEAD | tar -t
```

