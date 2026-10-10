const fs = require('fs');
const glob = require('fs').readdirSync('resources/js/Pages', { recursive: true }).filter(f => f.endsWith('.vue'));

for (const f of glob) {
    let filePath = 'resources/js/Pages/' + f;
    let content = fs.readFileSync(filePath, 'utf8');
    let original = content;

    // Look for <div class="card p-0 overflow-hidden"> containing a table
    content = content.replace(/<div class="card p-0 overflow-hidden">(\s*<table)/g, '<div class="card p-0 overflow-x-auto">$1');
    content = content.replace(/<div class="rounded-xl overflow-hidden shadow-sm"/g, '<div class="rounded-xl overflow-x-auto shadow-sm"');

    if (content !== original) {
        fs.writeFileSync(filePath, content);
    }
}
