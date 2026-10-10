const fs = require('fs');
const files = [
    'resources/js/Pages/Customers/Show.vue',
    'resources/js/Pages/Purchases/Show.vue',
    'resources/js/Pages/Expenses/Index.vue',
    'resources/js/Pages/POS/Show.vue',
    'resources/js/Pages/Inventory/Suppliers/Index.vue',
    'resources/js/Pages/Services/Index.vue',
    'resources/js/Pages/Employees/Index.vue'
];

files.forEach(file => {
    let content = fs.readFileSync(file, 'utf8');
    let original = content;

    // Fix Customers/Show.vue
    if (file.includes('Customers/Show.vue')) {
        content = content.replace(/function deleteVehicle\((.*?)\)\s*\{[\s\S]*?if\s*\(confirm\((.*?)\)\)\s*\{([\s\S]*?)\}\s*\}/, 
            "async function deleteVehicle($1) {\n    if (await confirmDelete($2)) {$3}\n}");
    }
    // Fix Purchases/Show.vue
    else if (file.includes('Purchases/Show.vue')) {
        content = content.replace(/function confirmDelete\(\)\s*\{[\s\S]*?if\s*\(confirm\((.*?)\)\)\s*\{([\s\S]*?)\}\s*\}/, 
            "async function confirmDelete() {\n  if (await confirmDeleteAlert($1)) {$2}\n}");
        if (content !== original && !content.includes('@/utils/alerts')) {
            content = content.replace(/<script setup>([\s\S]*?)<\/script>/, "<script setup>\nimport { confirmDelete as confirmDeleteAlert } from '@/utils/alerts';\n$1</script>");
            fs.writeFileSync(file, content);
            console.log("Updated", file);
            return;
        }
    }
    // Fix Expenses/Index.vue
    else if (file.includes('Expenses/Index.vue')) {
        content = content.replace(/function destroy\(id\)\s*\{\s*if\s*\(confirm\((.*?)\)\)\s*(.*?);?\s*\}/, 
            "async function destroy(id) {\n    if (await confirmDelete($1)) $2;\n}");
    }
    // Fix POS/Show.vue
    else if (file.includes('POS/Show.vue')) {
        content = content.replace(/function voidInvoice\(\)\s*\{[\s\S]*?if\s*\(confirm\((.*?)\)\)\s*\{([\s\S]*?)\}\s*\}/, 
            "async function voidInvoice() {\n    if (await confirmAction($1, '', 'Sí, anular')) {$2}\n}");
        if (content !== original && !content.includes('@/utils/alerts')) {
            content = content.replace(/<script setup>([\s\S]*?)<\/script>/, "<script setup>\nimport { confirmAction } from '@/utils/alerts';\n$1</script>");
            fs.writeFileSync(file, content);
            console.log("Updated", file);
            return;
        }
    }
    // Fix Suppliers/Index.vue
    else if (file.includes('Suppliers/Index.vue')) {
        content = content.replace(/function destroy\((.*?)\)\s*\{[\s\S]*?if\s*\(confirm\((.*?)\)\)\s*\{([\s\S]*?)\}\s*\}/, 
            "async function destroy($1) {\n  if (await confirmDelete($2)) {$3}\n}");
    }
    // Fix Services/Index.vue
    else if (file.includes('Services/Index.vue')) {
        content = content.replace(/function destroy\(id\)\s*\{[\s\S]*?if\s*\(confirm\((.*?)\)\)\s*\{([\s\S]*?)\}\s*\}/, 
            "async function destroy(id) {\n    if (await confirmDelete($1)) {$2}\n}");
    }
    // Fix Employees/Index.vue
    else if (file.includes('Employees/Index.vue')) {
        content = content.replace(/function destroy\(id\)\s*\{[\s\S]*?if\s*\(confirm\((.*?)\)\)\s*\{([\s\S]*?)\}\s*\}/, 
            "async function destroy(id) {\n    if (await confirmDelete($1)) {$2}\n}");
    }

    if (content !== original && !content.includes('@/utils/alerts')) {
        content = content.replace(/<script setup>([\s\S]*?)<\/script>/, "<script setup>\nimport { confirmDelete } from '@/utils/alerts';\n$1</script>");
        fs.writeFileSync(file, content);
        console.log("Updated", file);
    }
});
