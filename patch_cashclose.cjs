const fs = require('fs');
const file = 'resources/js/Pages/Reports/CashClose.vue';
let content = fs.readFileSync(file, 'utf8');

// 1. Wrap everything after </template> (the header) inside the new Grid.
// We'll split the content.
const headerEndIdx = content.indexOf('</template>') + '</template>'.length;
const appLayoutEndIdx = content.lastIndexOf('</AppLayout>');

const headerStr = content.slice(0, headerEndIdx);
const bodyStr = content.slice(headerEndIdx, appLayoutEndIdx);
const footerStr = content.slice(appLayoutEndIdx);

// Extract the two cards from bodyStr
const resumenStart = bodyStr.indexOf('<!-- ── Resumen de caja ───────────────────────────── -->');
const detalleStart = bodyStr.indexOf('<!-- ── Detalle de pagos ───────────────────────────── -->');

let resumenHtml = bodyStr.slice(resumenStart, detalleStart).trim();
let detalleHtml = bodyStr.slice(detalleStart).trim();

// Clean up margins in the cards
resumenHtml = resumenHtml.replace(/<div class="card p-5 mb-5">/, `<div class="card p-5 border-t-4 shadow-md" :class="isClosed ? 'border-t-emerald-500' : 'border-t-brand-600'">`);
// Put total row on a new level of styling
resumenHtml = resumenHtml.replace(/class="btn btn-primary"/, `class="btn btn-primary w-full justify-center py-3 text-base shadow-sm"`);

const newBody = `
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-start mt-2">
            <!-- Columna Izquierda: Detalle -->
            <div class="xl:col-span-7 order-2 xl:order-1">
                ${detalleHtml}
            </div>

            <!-- Columna Derecha: Formulario de Cierre -->
            <div class="xl:col-span-5 order-1 xl:order-2 sticky top-6">
                ${resumenHtml}
            </div>
        </div>
`;

fs.writeFileSync(file, headerStr + newBody + footerStr);
