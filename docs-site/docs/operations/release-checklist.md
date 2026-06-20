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
   npm run check
   npm run build
   ```

3. **Verify generated artifacts**

   Confirm `_site/index.html`, `_site/sitemap.xml`, `_site/llms.txt`, and `_site/.docmd-search/manifest.json`.

4. **Review README link**

   Keep the official docs URL near the top of `README.md`.
:::

