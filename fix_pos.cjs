const fs = require('fs');
let file = 'resources/js/Pages/POS/Index.vue';
let content = fs.readFileSync(file, 'utf8');

// Replace the empty state of POS
let emptyPOS = `
                    <div v-else class="flex flex-col items-center justify-center py-16 px-4 bg-slate-50/50 rounded-2xl border border-dashed border-slate-200">
                        <div class="w-16 h-16 bg-white text-slate-300 rounded-full flex items-center justify-center mb-4 shadow-sm">
                            <Icon name="cart" class="w-8 h-8" />
                        </div>
                        <h3 class="text-sm font-bold text-slate-700 mb-1">Factura vacía</h3>
                        <p class="text-xs text-slate-500 mb-4 text-center max-w-xs">Usa el buscador de arriba para agregar productos, llantas o servicios a esta factura.</p>
                    </div>
`;
content = content.replace(/<p v-else class="text-sm text-text-3 italic text-center py-4">\{\{\s*t\('common\.noResults'\)\s*\}\}<\/p>/, emptyPOS);

// Add table responsive wrappers
content = content.replace(/<table class="w-full text-sm"/g, '<div class="overflow-x-auto"><table class="w-full text-sm whitespace-nowrap"');
content = content.replace(/<\/table>/g, '</table></div>');

fs.writeFileSync(file, content);
