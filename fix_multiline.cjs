const fs = require('fs');
const path = require('path');

const files = [
    'resources/js/Pages/Customers/Index.vue',
    'resources/js/Pages/Inventory/Products/Index.vue',
    'resources/js/Pages/Inventory/Suppliers/Index.vue',
    'resources/js/Pages/Purchases/Index.vue',
    'resources/js/Pages/POS/Index.vue'
];

files.forEach(file => {
    let content = fs.readFileSync(file, 'utf8');
    let original = content;

    // We can just add the icon before the text {{ t(...) }} or Add ...
    content = content.replace(/(class="[^"]*btn-primary[^"]*"(?:\s|.)*?>\s*)(?:\{\{\s*t\('([^']+)'\)\s*\}\}|Add ([A-Za-z]+))/g, (match, prefix, tKey, addWord) => {
        if (match.includes('<Icon name="plus" />')) return match;
        const text = tKey ? `{{ t('${tKey}') }}` : `Add ${addWord}`;
        return `${prefix}<Icon name="plus" />\n                                ${text}`;
    });

    if (content !== original) {
        if (!content.includes("import Icon from '@/Components/Icon.vue'")) {
            content = content.replace(/<script setup>([\s\S]*?)<\/script>/, (match, scriptBody) => {
                let newBody = "\nimport Icon from '@/Components/Icon.vue';\n" + scriptBody.trim();
                return `<script setup>\n${newBody}\n<\/script>`;
            });
        }
        fs.writeFileSync(file, content);
        console.log(`Updated ${file}`);
    }
});
