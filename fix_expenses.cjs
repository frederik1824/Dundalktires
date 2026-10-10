const fs = require('fs');
let file = 'resources/js/Pages/Expenses/Index.vue';
let content = fs.readFileSync(file, 'utf8');

content = content.replace(/function destroy\(id\)\s*\{\s*if\s*\(confirm\((.*?)\)\)\s*(.*?);\s*\}/, 
    "async function destroy(id) {\n    if (await confirmDelete($1)) { $2; }\n}");

if (!content.includes('@/utils/alerts')) {
    content = content.replace(/<script setup>([\s\S]*?)<\/script>/, "<script setup>\nimport { confirmDelete } from '@/utils/alerts';\n$1</script>");
}

fs.writeFileSync(file, content);
