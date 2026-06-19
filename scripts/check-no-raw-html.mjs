import { readdir, readFile } from 'node:fs/promises';
import { join, extname } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = fileURLToPath(new URL('../docs-site/', import.meta.url));
const rawHtmlPattern = /<\/?[a-z][\w:-]*(\s[^>]*)?>/i;
const errors = [];

async function walk(dir) {
  for (const entry of await readdir(dir, { withFileTypes: true })) {
    const path = join(dir, entry.name);
    if (entry.isDirectory()) {
      await walk(path);
      continue;
    }
    if (extname(entry.name) !== '.md') continue;
    const body = await readFile(path, 'utf8');
    if (rawHtmlPattern.test(body)) {
      errors.push(`${path}: raw HTML is not allowed in docs markdown`);
    }
    if (/:::\s*button\b/.test(body)) {
      errors.push(`${path}: ::: button is forbidden by the docs playbook`);
    }
  }
}

await walk(root);

if (errors.length > 0) {
  console.error(errors.join('\n'));
  process.exit(1);
}

console.log('Markdown guard passed: no raw HTML or forbidden button containers.');
