# docmd Docs Skill

Use this skill when editing the `docs-site` documentation for `laravel-ai-search-providers`.

## Rules

- Keep documentation source in Markdown only.
- Do not use MDX, JSX, raw HTML, or `::: button`.
- Use docmd containers for rich structure: `callout`, `tabs`, `steps`, `collapsible`, `grids`, `grid`, and `card`.
- Keep every page listed in `docs-site/docmd.config.json` navigation.
- Run `npm run check` and `npm run build` from `docs-site/` before committing docs changes. `docs-site/package.json` is the only `package.json` in the repository: do not add one at the root.
- Keep `docs-site/.docmd-search/config.json` committed and keep generated `.docmd-search/*` ignored.
- Preserve the official docs URL: `https://doc.laravel-ai-search-providers.padosoft.com`.

## Expected files

- `docs-site/package.json`
- `docs-site/docmd.config.json`
- `docs-site/docs/**/*.md`
- `docs-site/assets/favicon.svg`
- `docs-site/assets/custom.css`
- `docs-site/scripts/check-no-raw-html.mjs`
- `docs-site/.docmd-search/config.json`

`docs-site/` is `export-ignore`d in `.gitattributes`, so none of it ships in the Composer dist archive.
