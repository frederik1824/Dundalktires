const fs = require('fs');
const files = [
    'resources/js/Pages/Customers/Index.vue',
    // Let's add any other file that might have raw text links for actions
];

let content = fs.readFileSync(files[0], 'utf8');
content = content.replace(/class="text-xs text-slate-400 hover:text-brand-600 mr-3"/g, 'class="btn btn-ghost btn-sm mr-2"');
fs.writeFileSync(files[0], content);
