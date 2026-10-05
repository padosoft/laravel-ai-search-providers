import { readdirSync, statSync, readFileSync } from 'node:fs';
import { join } from 'node:path';
const DOCS = join(process.cwd(), 'docs');
const TAG = /<\/?[A-Za-z][\w:-]*(\s[^>]*)?\/?>/g;
const BUTTON = /:::\s*button\b/;
const bad = [];
(function walk(d){ for (const n of readdirSync(d)) { const p = join(d, n);
  if (statSync(p).isDirectory()) { walk(p); continue; }
  if (!n.endsWith('.md')) continue;
  let fence = false;
  readFileSync(p,'utf8').split('\n').forEach((l,i)=>{
    // Code samples may legitimately show markup: skip fenced blocks and inline code.
    if (/^\s*(```|~~~)/.test(l)) { fence = !fence; return; }
    if (fence) return;
    const prose = l.replace(/`[^`]*`/g, '');
    const m = prose.match(TAG);
    if (m) bad.push(`${p}:${i+1} raw HTML ${m.join(' ')}`);
    if (BUTTON.test(prose)) bad.push(`${p}:${i+1} forbidden ::: button container`);
  });
}})(DOCS);
if (bad.length) { console.error('Docs Markdown guard failed:\n'+bad.join('\n')); process.exit(1); }
console.log('OK: no raw HTML or forbidden button containers.');
