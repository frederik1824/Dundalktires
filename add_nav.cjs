const fs = require('fs');
let file = 'resources/js/Layouts/AppLayout.vue';
let content = fs.readFileSync(file, 'utf8');

// Add "New Sale"
let newNav = `    { group: t('nav.groups.operations'), name: t('nav.dashboard'),     href: '/dashboard',              icon: 'grid',       perm: 'view_dashboard' },
    { group: t('nav.groups.operations'), name: 'POS (Punto de Venta)', href: '/pos',                    icon: 'revenue',    perm: 'manage_sales' },`;

content = content.replace(/\{\s*group:\s*t\('nav\.groups\.operations'\),\s*name:\s*t\('nav\.dashboard'\),\s*href:\s*'\/dashboard'[^}]+\},/, newNav);

fs.writeFileSync(file, content);
