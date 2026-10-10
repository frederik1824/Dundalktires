const fs = require('fs');

let css = fs.readFileSync('resources/css/app.css', 'utf8');

// Update .card
css = css.replace(/\.card\s*\{[^}]+\}/, `.card {
    background-color: var(--color-surface);
    border: 1px solid theme('colors.slate.200');
    @apply rounded-2xl p-5 shadow-sm transition-all duration-300 hover:shadow-md;
}`);

// Update .input
css = css.replace(/\.input\s*\{[^}]+\}/, `.input {
    @apply w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-text placeholder-text-3 outline-none transition-all duration-200 focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10;
}`);

// Update badges
const badges = `
/* ── Badges ── */
.badge { @apply inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold uppercase tracking-wide; }
.badge-green  { @apply badge bg-emerald-50 text-emerald-600 border border-emerald-100; }
.badge-gray   { @apply badge bg-slate-100 text-slate-500 border border-slate-200; }
.badge-blue   { @apply badge bg-brand-50 text-brand-600 border border-brand-100; }
.badge-amber  { @apply badge bg-amber-50 text-amber-600 border border-amber-100; }
.badge-purple { @apply badge bg-violet-50 text-violet-600 border border-violet-100; }
.badge-red    { @apply badge bg-red-50 text-red-600 border border-red-100; }
`;
css = css.replace(/\/\*\s*── Badges ──\s*\*\/[\s\S]*?(?=\/\*|$)/, badges);

fs.writeFileSync('resources/css/app.css', css);
