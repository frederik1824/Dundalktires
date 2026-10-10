const fs = require('fs');

let f1 = 'resources/js/Pages/Customers/Show.vue';
let c1 = fs.readFileSync(f1, 'utf8');
c1 = c1.replace(/class="text-xs text-slate-400 hover:text-brand-600"/g, 'class="btn btn-ghost btn-sm"');
c1 = c1.replace(/class="text-xs text-slate-300 hover:text-red-500"/g, 'class="btn btn-ghost btn-sm text-red-500 hover:bg-red-50 hover:border-red-200"');
c1 = c1.replace(/\{\{\s*t\('common.edit'\)\s*\}\}\n\s*<\/Link>/, '<Icon name="edit" />\n                {{ t(\'common.edit\') }}\n            </Link>');
if (!c1.includes("import Icon from")) c1 = c1.replace(/<script setup>([\s\S]*?)<\/script>/, "<script setup>\nimport Icon from '@/Components/Icon.vue';\n$1</script>");
fs.writeFileSync(f1, c1);

let f2 = 'resources/js/Pages/Purchases/Show.vue';
let c2 = fs.readFileSync(f2, 'utf8');
c2 = c2.replace(/\{\{\s*t\('common.delete'\)\s*\}\}\n\s*<\/button>/, '<Icon name="delete" />\n            {{ t(\'common.delete\') }}\n          </button>');
if (!c2.includes("import Icon from")) c2 = c2.replace(/<script setup>([\s\S]*?)<\/script>/, "<script setup>\nimport Icon from '@/Components/Icon.vue';\n$1</script>");
fs.writeFileSync(f2, c2);

