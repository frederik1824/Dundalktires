const fs = require('fs');
let content = fs.readFileSync('resources/js/Pages/Customers/Index.vue', 'utf8');

const emptyState = `<td colspan="5" class="px-4 py-16 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center mb-4">
                                    <Icon name="user" class="w-8 h-8" />
                                </div>
                                <h3 class="text-sm font-semibold text-slate-700 mb-1">{{ t('customers.noResults') }}</h3>
                                <p class="text-xs text-slate-500 mb-4">Empieza agregando tu primer cliente a la base de datos.</p>
                                <Link href="/customers/create" class="btn btn-primary shadow-sm">
                                    <Icon name="plus" /> {{ t('customers.addFirst') }}
                                </Link>
                            </div>
                        </td>`;

content = content.replace(/<td colspan="5"[^>]*>[\s\S]*?\{\{\s*t\('customers\.noResults'\)\s*\}\}[\s\S]*?<\/td>/, emptyState);

fs.writeFileSync('resources/js/Pages/Customers/Index.vue', content);
