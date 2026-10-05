---
title: Release Checklist
description: Prepare a package or docs release.
---

# Release Checklist

::: steps
1. **Run package tests**

   ```bash
   vendor/bin/phpunit --testsuite Unit,Feature
   ```

2. **Run docs checks**

   ```bash
   cd docs-site
   npm run check
   npm run build
   ```

3. **Verify generated artifacts**

   Confirm `docs-site/_site/index.html`, `docs-site/_site/sitemap.xml`, `docs-site/_site/llms.txt`, and `docs-site/_site/.docmd-search/manifest.json`.

4. **Verify the Composer dist archive**

   ```bash
   git archive HEAD | tar -t
   ```

   The archive must contain only `src/`, `config/`, `database/`, `composer.json`, `README.md`, `LICENSE` and `CHANGELOG.md`. Anything else (docs tooling, Node lockfiles, tests, CI) belongs in `.gitattributes` as `export-ignore`.

5. **Review README link**

   Keep the official docs URL near the top of `README.md`.
:::

