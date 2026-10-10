const fs = require('fs');
const glob = require('fs').readdirSync('resources/js/Pages', { recursive: true }).filter(f => f.endsWith('.vue'));

const emptyStateTemplate = (resourceName, addUrl, addText) => `
                        <td colspan="100%" class="px-4 py-16 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center mb-4">
                                    <Icon name="box" class="w-8 h-8" />
                                </div>
                                <h3 class="text-sm font-semibold text-slate-700 mb-1">Aún no hay registros</h3>
                                <p class="text-xs text-slate-500 mb-4">No se encontraron datos para ${resourceName}.</p>
                                ${addUrl ? `<Link href="${addUrl}" class="btn btn-primary shadow-sm"><Icon name="plus" /> ${addText}</Link>` : ''}
                            </div>
                        </td>`;

for (const f of glob) {
    let filePath = 'resources/js/Pages/' + f;
    let content = fs.readFileSync(filePath, 'utf8');
    let original = content;

    // We don't want to break if it's already using the beautiful empty state
    if (content.includes('bg-slate-50 text-slate-300 rounded-full')) continue;

    // Find tr v-if="something.length === 0" or similar
    let regex = /<tr v-if="([^"]+\.length === 0[^"]*)">\s*<td[^>]*>[\s\S]*?<\/td>\s*<\/tr>/;
    let match = content.match(regex);
    if (match) {
        // determine addUrl based on context
        let addUrl = '';
        let addText = 'Agregar nuevo';
        if (filePath.includes('Products')) { addUrl = '/products/create'; addText = 'Nuevo Producto'; }
        else if (filePath.includes('Suppliers')) { addUrl = '/suppliers/create'; addText = 'Nuevo Proveedor'; }
        else if (filePath.includes('WorkOrders')) { addUrl = '/work-orders/create'; addText = 'Nueva Orden'; }
        else if (filePath.includes('Purchases')) { addUrl = '/purchases/create'; addText = 'Nueva Compra'; }
        else if (filePath.includes('Expenses')) { addUrl = '/expenses/create'; addText = 'Nuevo Gasto'; }
        else if (filePath.includes('Services')) { addUrl = '/services/create'; addText = 'Nuevo Servicio'; }
        else if (filePath.includes('Employees')) { addUrl = '/employees/create'; addText = 'Nuevo Empleado'; }
        
        let newContent = content.replace(regex, `<tr v-if="$1">${emptyStateTemplate('esta sección', addUrl, addText)}</tr>`);
        
        if (newContent !== original) {
            // ensure Icon is imported
            if (!newContent.includes('@/Components/Icon.vue') && !newContent.includes('Icon from')) {
                newContent = newContent.replace(/<script setup>([\s\S]*?)<\/script>/, "<script setup>\nimport Icon from '@/Components/Icon.vue';\n$1</script>");
            }
            fs.writeFileSync(filePath, newContent);
            console.log('Fixed Empty State in', filePath);
        }
    }
}
