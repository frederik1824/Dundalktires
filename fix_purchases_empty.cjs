const fs = require('fs');
let file = 'resources/js/Pages/Purchases/Form.vue';
let content = fs.readFileSync(file, 'utf8');

// Replace the incorrect empty state
let correctEmpty = `
                        <td colspan="100%" class="px-4 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-12 h-12 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center mb-3">
                                    <Icon name="box" class="w-6 h-6" />
                                </div>
                                <h3 class="text-sm font-semibold text-slate-700 mb-1">Sin productos</h3>
                                <p class="text-xs text-slate-500 mb-4">Usa el botón "Agregar producto" para empezar la orden de compra.</p>
                                <button type="button" @click="showProductSearch = true" class="btn btn-primary btn-sm"><Icon name="plus" /> Agregar</button>
                            </div>
                        </td>
`;
content = content.replace(/<td colspan="100%" class="px-4 py-16 text-center">[\s\S]*?<\/td>/, correctEmpty);
fs.writeFileSync(file, content);
