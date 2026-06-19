# docmd Docs Skill

Use this skill when editing the `docs-site` documentation for `laravel-ai-search-providers`.

## Rules

- Keep documentation source in Markdown only.
- Do not use MDX, JSX, raw HTML, or `::: button`.
- Use docmd containers for rich structure: `callout`, `tabs`, `steps`, `collapsible`, `grids`, `grid`, and `card`.
- Keep every page listed in `docs-site/docmd.config.json` navigation.
- Run `npm run check` and `npm run build` before committing docs changes.
- Keep `.docmd-search/config.json` committed and keep generated `.docmd-search/*` ignored.
- Preserve the official docs URL: `https://doc.laravel-ai-search-providers.padosoft.com`.

## Expected files

- `docs-site/docmd.config.json`
- `docs-site/**/*.md`
- `assets/favicon.svg`
- `assets/custom.css`
- `scripts/check-no-raw-html.mjs`
- `.docmd-search/config.json`
