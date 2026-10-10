const fs = require('fs');
const glob = require('fs').readdirSync('resources/js/Pages', { recursive: true }).filter(f => f.endsWith('.vue'));

for (const f of glob) {
    let filePath = 'resources/js/Pages/' + f;
    let content = fs.readFileSync(filePath, 'utf8');
    let original = content;

    // Convert old label classes to .label
    content = content.replace(/class="block text-sm font-medium text-slate-700 mb-1"/g, 'class="label"');
    
    // Convert old input classes to .input
    content = content.replace(/class="w-full rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-brand-500"/g, 'class="input"');
    
    // Convert old card classes to .card
    content = content.replace(/class="rounded-xl shadow-sm p-6 space-y-4"[\s\S]*?style="[^"]*"/, 'class="card space-y-4"');
    content = content.replace(/class="rounded-xl shadow-sm p-6"[\s\S]*?style="[^"]*"/, 'class="card"');
    content = content.replace(/class="bg-white rounded-xl shadow-sm border border-slate-200 p-6"/g, 'class="card"');

    // Remove old inline borders for forms
    content = content.replace(/\s*style="border:1px solid var\(--color-border\);"/g, '');

    // Buttons
    content = content.replace(/class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700 disabled:opacity-60 transition"/g, 'class="btn btn-primary"');
    content = content.replace(/class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700"/g, 'class="btn btn-primary"');

    if (content !== original) {
        fs.writeFileSync(filePath, content);
        console.log('Unified form styles in', filePath);
    }
}
