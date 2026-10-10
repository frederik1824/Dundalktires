const fs = require('fs');
const file = 'resources/js/Pages/Reports/CashClose.vue';
let content = fs.readFileSync(file, 'utf8');

const rightColHtml = `
            <div class="xl:col-span-5 order-1 xl:order-2 sticky top-6">
                <!-- ── Estado: CERRADO (solo lectura) ── -->
                <div v-if="isClosed" class="card p-5 border-t-4 shadow-md border-t-emerald-500">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="section-title !mb-0">{{ t('cashClose.summary') }}</h2>
                    </div>
                    <!-- Tabla cuadre cerrado -->
                    <div class="overflow-x-auto rounded-xl border border-border mb-4">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-slate-50 border-b border-border">
                                    <th class="px-4 py-2.5 text-left font-semibold text-text-2 text-xs uppercase tracking-wide">Método</th>
                                    <th class="px-4 py-2.5 text-right font-semibold text-text-2 text-xs uppercase tracking-wide">{{ t('cashClose.systemAmt') }}</th>
                                    <th class="px-4 py-2.5 text-right font-semibold text-text-2 text-xs uppercase tracking-wide">{{ t('cashClose.physicalAmt') }}</th>
                                    <th class="px-4 py-2.5 text-right font-semibold text-text-2 text-xs uppercase tracking-wide">{{ t('cashClose.difference') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border">
                                <tr v-for="m in cashClose.details" :key="m.method" class="hover:bg-slate-50 transition-colors">
                                    <td class="px-4 py-3 font-medium text-text">
                                        <span class="flex items-center gap-2">
                                            <span :class="['w-2 h-2 rounded-full flex-shrink-0', colors(m.method).dot]"></span>
                                            {{ meta(m.method).icon }} {{ t('cashClose.methods.' + m.method) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right font-mono text-text-2">\${{ fmt(cashClose.system_amounts?.[m.method] ?? 0) }}</td>
                                    <td class="px-4 py-3 text-right font-mono font-semibold text-text">\${{ fmt(cashClose.physical_amounts?.[m.method] ?? 0) }}</td>
                                    <td class="px-4 py-3 text-right font-mono font-semibold">
                                        <span :class="(cashClose.physical_amounts?.[m.method] ?? 0) - (cashClose.system_amounts?.[m.method] ?? 0) === 0 ? 'text-green-600' : 'text-red-500'">
                                            {{ ((cashClose.physical_amounts?.[m.method] ?? 0) - (cashClose.system_amounts?.[m.method] ?? 0)) >= 0 ? '+' : '' }}\${{ fmt((cashClose.physical_amounts?.[m.method] ?? 0) - (cashClose.system_amounts?.[m.method] ?? 0)) }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="bg-slate-50 border-t-2 border-border">
                                <tr>
                                    <td class="px-4 py-3 font-bold text-text text-sm">Total</td>
                                    <td class="px-4 py-3 text-right font-mono font-bold text-text">\${{ fmt(cashClose.system_total) }}</td>
                                    <td class="px-4 py-3 text-right font-mono font-bold text-text">\${{ fmt(cashClose.physical_total) }}</td>
                                    <td class="px-4 py-3 text-right font-mono font-black text-lg"
                                        :class="Number(cashClose.difference) === 0 ? 'text-green-600' : 'text-red-500'">
                                        {{ Number(cashClose.difference) >= 0 ? '+' : '' }}\${{ fmt(cashClose.difference) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Notas y meta -->
                    <div v-if="cashClose.notes" class="flex items-start gap-2 text-sm text-text-2 bg-slate-50 rounded-lg px-4 py-3 mb-4">
                        <svg class="w-4 h-4 mt-0.5 flex-shrink-0 text-text-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        <span class="italic">{{ cashClose.notes }}</span>
                    </div>

                    <div class="flex items-center justify-between flex-wrap gap-3">
                        <p class="text-xs text-text-3">
                            {{ t('cashClose.closedAt') }} {{ new Date(cashClose.closed_at).toLocaleString() }}
                            <span v-if="cashClose.closed_by"> · {{ t('cashClose.closedBy') }} {{ cashClose.closedBy?.name }}</span>
                        </p>
                        <button @click="reopenClose" class="btn btn-ghost btn-sm text-xs text-amber-600 border-amber-200 hover:border-amber-400">
                            {{ t('cashClose.reopenBtn') }}
                        </button>
                    </div>
                </div>

                <!-- ── Estado: ABIERTO (formulario) ── -->
                <div v-else class="card p-5 border-t-4 shadow-md border-t-brand-600">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="section-title !mb-0">{{ t('cashClose.summary') }}</h2>
                    </div>
                    <!-- Tabla de cuadre -->
                    <div v-if="byMethod.length" class="overflow-x-auto rounded-xl border border-border mb-4">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-slate-50 border-b border-border">
                                    <th class="px-4 py-2.5 text-left font-semibold text-text-2 text-xs uppercase tracking-wide">Método</th>
                                    <th class="px-4 py-2.5 text-right font-semibold text-text-2 text-xs uppercase tracking-wide">{{ t('cashClose.systemAmt') }}</th>
                                    <th class="px-4 py-2.5 text-left font-semibold text-text-2 text-xs uppercase tracking-wide w-44">{{ t('cashClose.physicalAmt') }}</th>
                                    <th class="px-4 py-2.5 text-right font-semibold text-text-2 text-xs uppercase tracking-wide">{{ t('cashClose.difference') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border">
                                <tr v-for="m in byMethod" :key="m.method" class="hover:bg-slate-50 transition-colors">
                                    <td class="px-4 py-3 font-medium text-text">
                                        <span class="flex items-center gap-2">
                                            <span :class="['w-2 h-2 rounded-full flex-shrink-0', colors(m.method).dot]"></span>
                                            {{ meta(m.method).icon }} {{ t('cashClose.methods.' + m.method) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right font-mono text-text-2">\${{ fmt(m.total) }}</td>
                                    <td class="px-4 py-2.5">
                                        <div class="relative">
                                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-text-3 text-sm pointer-events-none">$</span>
                                            <input
                                                v-model="closeForm.physical_amounts[m.method]"
                                                type="number" step="0.01" min="0"
                                                :placeholder="fmt(m.total)"
                                                class="input input-currency w-full py-1.5 text-sm"
                                            />
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-right font-mono font-semibold">
                                        <template v-if="rowDiff(m.method, m.total) !== null">
                                            <span :class="rowDiff(m.method, m.total) === 0 ? 'text-green-600' : 'text-red-500'">
                                                {{ rowDiff(m.method, m.total) >= 0 ? '+' : '' }}\${{ fmt(rowDiff(m.method, m.total)) }}
                                            </span>
                                        </template>
                                        <span v-else class="text-text-3">—</span>
                                    </td>
                                </tr>
                            </tbody>
                            <!-- Fila de totales -->
                            <tfoot class="border-t-2 border-border">
                                <tr :class="difference === 0 ? 'bg-green-50' : 'bg-red-50'">
                                    <td class="px-4 py-3 font-bold text-text text-sm">Total</td>
                                    <td class="px-4 py-3 text-right font-mono font-bold text-text">\${{ fmt(total) }}</td>
                                    <td class="px-4 py-3 font-mono font-bold text-text text-sm pl-4">\${{ fmt(physicalTotal) }}</td>
                                    <td class="px-4 py-3 text-right font-mono font-black text-lg"
                                        :class="difference === 0 ? 'text-green-600' : 'text-red-500'">
                                        {{ difference >= 0 ? '+' : '' }}\${{ fmt(difference) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Notas -->
                    <div class="mb-5">
                        <label class="label">{{ t('cashClose.notes') }}</label>
                        <textarea v-model="closeForm.notes" class="input w-full" rows="2" placeholder="Observaciones del cierre..."></textarea>
                    </div>

                    <div v-if="!byMethod.length" class="text-sm text-text-3 italic mb-4 py-4 text-center">
                        {{ t('cashClose.noPayments') }} {{ date }}.
                    </div>

                    <button
                        @click="submitClose"
                        :disabled="closeForm.processing || !byMethod.length"
                        class="btn btn-primary w-full justify-center py-3 text-base shadow-sm"
                    >
                        <svg v-if="!closeForm.processing" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        {{ closeForm.processing ? t('cashClose.confirming') : t('cashClose.confirm') }}
                    </button>
                </div>
            </div>
`;

content = content.replace(/<div class="xl:col-span-5 order-1 xl:order-2 sticky top-6">\s*<\/div>/, rightColHtml);
fs.writeFileSync(file, content);
