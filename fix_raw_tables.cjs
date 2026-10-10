const fs = require('fs');
const glob = require('fs').readdirSync('resources/js/Pages', { recursive: true }).filter(f => f.endsWith('.vue'));

for (const f of glob) {
    let filePath = 'resources/js/Pages/' + f;
    let content = fs.readFileSync(filePath, 'utf8');
    let original = content;

    // Wrap bare tables
    // Regex matches <table ...> that is NOT preceded by overflow-x-auto
    content = content.replace(/(?<!<div class="[^"]*overflow-[^"]*">\s*)<table([^>]*)>/g, '<div class="overflow-x-auto"><table$1>');
    // Now we must close the div. This is tricky with regex. 
    // Since we know the codebase, let's just do a manual check or simpler regex:
    // It's safer to just look for <table ...> and </table> and replace them if we know they aren't wrapped.
}
