const fs = require('fs');
const glob = require('fs').readdirSync('resources/js/Pages', { recursive: true }).filter(f => f.endsWith('.vue'));

for (const f of glob) {
    let filePath = 'resources/js/Pages/' + f;
    let content = fs.readFileSync(filePath, 'utf8');

    content = content.replace(/<div class="overflow-x-auto">\s*<table([^>]*)v-if="([^"]+)"([^>]*)>/g, '<div class="overflow-x-auto" v-if="$2"><table$1$3>');

    fs.writeFileSync(filePath, content);
}
