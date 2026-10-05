# Rule: docmd Docs Sync

When repository behavior changes, update `docs-site` in the same change.

## Required checks

- Update affected guide, architecture, operations, and reference pages.
- Keep sidebar navigation complete.
- Keep Markdown free of raw HTML and forbidden button containers.
- Run from `docs-site/` (the only `package.json` in the repository):

```bash
cd docs-site
npm run check
npm run build
```

## Search index

The semantic search config is source-controlled at `docs-site/.docmd-search/config.json`. Generated search artifacts remain ignored unless explicitly required by a release process.
