const fs = require('fs');

const files = [
    'resources/js/Pages/Inventory/Products/Show.vue',
    'resources/js/Pages/POS/Show.vue',
    'resources/js/Pages/Purchases/Form.vue',
    'resources/js/Pages/Purchases/Show.vue',
    'resources/js/Pages/Reports/CashClose.vue',
    'resources/js/Pages/Reports/Index.vue',
    'resources/js/Pages/WorkOrders/Form.vue'
];

for (const f of files) {
    if (!fs.existsSync(f)) continue;
    let content = fs.readFileSync(f, 'utf8');
    let original = content;

    // Wrap tables that are NOT already wrapped by `<div class="overflow-x-auto">`
    content = content.replace(/(?<!<div class="overflow-x-auto">)(<table[^>]*>[\s\S]*?<\/table>)/g, '<div class="overflow-x-auto">$1</div>');

    if (content !== original) {
        fs.writeFileSync(f, content);
        console.log('Wrapped tables in', f);
    }
}
