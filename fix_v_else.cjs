const fs = require('fs');
const files = [
    'resources/js/Pages/WorkOrders/Form.vue',
    'resources/js/Pages/WorkOrders/Show.vue',
    'resources/js/Pages/POS/Index.vue',
    'resources/js/Pages/POS/Show.vue'
];

for (const f of files) {
    if (!fs.existsSync(f)) continue;
    let content = fs.readFileSync(f, 'utf8');

    // Move v-if from table to the wrapper div
    // Pattern: <div class="overflow-x-auto"><table class="w-full text-sm whitespace-nowrap" v-if="form.items.length">
    // Should be: <div class="overflow-x-auto" v-if="form.items.length"><table class="w-full text-sm whitespace-nowrap">
    content = content.replace(/<div class="overflow-x-auto">\s*<table([^>]*)v-if="([^"]+)"([^>]*)>/g, '<div class="overflow-x-auto" v-if="$2"><table$1$3>');

    fs.writeFileSync(f, content);
}
