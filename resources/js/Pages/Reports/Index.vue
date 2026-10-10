<script setup>
import { ref, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';

const { t } = useI18n();

const props = defineProps({
    filters:           Object,
    salesSummary:      Object,
    dailyRevenue:      Array,
    byMethod:          Array,
    topServices:       Array,
    topProducts:       Array,
    expenseSummary:    Object,
    expensesByCategory: Array,
    workOrderStats:    Array,
    inventoryValue:    Object,
    profit:            Number,
    profitMargin:      Number,
});

const from = ref(props.filters.from);
const to   = ref(props.filters.to);

function applyFilter() {
    router.get('/reports', { from: from.value, to: to.value }, { preserveState: true, replace: true });
}

const methodLabels = { cash: 'Cash', card: 'Card', zelle: 'Zelle', cashapp: 'Cash App', transfer: 'Transfer', other: 'Other' };
const methodColors = { cash: '#22c55e', card: '#3b82f6', zelle: '#a855f7', cashapp: '#22c55e', transfer: '#0891b2', other: '#94a3b8' };

// Bar chart for daily revenue
const chartRef = ref(null);
onMounted(async () => {
    if (!chartRef.value || !props.dailyRevenue?.length) return;
    const { Chart, BarController, BarElement, CategoryScale, LinearScale, Tooltip } = await import('chart.js');
    Chart.register(BarController, BarElement, CategoryScale, LinearScale, Tooltip);
    new Chart(chartRef.value, {
        type: 'bar',
        data: {
            labels: props.dailyRevenue.map(r => r.date),
            datasets: [{
                label: 'Revenue',
                data: props.dailyRevenue.map(r => parseFloat(r.total)),
                backgroundColor: 'rgba(37,99,235,0.15)',
                borderColor: 'rgba(37,99,235,0.8)',
                borderWidth: 2,
                borderRadius: 5,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { callbacks: { label: c => `$${c.parsed.y.toFixed(2)}` } } },
            scales: {
                y: { beginAtZero: true, ticks: { callback: v => `$${v}`, color: '#94a3b8' }, grid: { color: 'rgba(148,163,184,0.15)' } },
                x: { ticks: { color: '#94a3b8' }, grid: { display: false } },
            },
        },
    });
});

function pct(val, total) {
    if (!total || total <= 0) return 0;
    return Math.round((parseFloat(val) / parseFloat(total)) * 100);
}
</script>

<template>
    <AppLayout>
        <template #header>
            <div class="page-header">
                <div>
                    <h1 class="page-title">{{ t('reports.title') }}</h1>
                    <p class="page-subtitle">{{ t('reports.subtitle') }}</p>
                </div>
                <!-- Date range filter + export -->
                <div class="flex flex-wrap items-center gap-2">
                    <input v-model="from" type="date" class="input text-sm" />
                    <span class="text-text-3 text-sm">{{ t('reports.to') }}</span>
                    <input v-model="to" type="date" class="input text-sm" />
                    <button @click="applyFilter" class="btn btn-primary btn-sm">{{ t('reports.apply') }}</button>
                    <div class="flex gap-1 flex-wrap">
                        <a href="/reports/ar" class="btn btn-ghost btn-sm" title="Cuentas por cobrar">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                            C×C
                        </a>
                        <a :href="`/reports/cash-close?date=${to}`" class="btn btn-ghost btn-sm" title="Cierre de caja">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            Caja
                        </a>
                        <a :href="`/reports/export/csv?from=${from}&to=${to}&type=sales`" class="btn btn-ghost btn-sm" title="Export sales CSV">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            Sales CSV
                        </a>
                        <a :href="`/reports/export/csv?from=${from}&to=${to}&type=expenses`" class="btn btn-ghost btn-sm" title="Export expenses CSV">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            Expenses CSV
                        </a>
                        <a :href="`/reports/export/pdf?from=${from}&to=${to}`" class="btn btn-ghost btn-sm" title="Export full PDF report">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            PDF
                        </a>
                    </div>
                </div>
            </div>
        </template>

        <!-- KPI row -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="card flex flex-col gap-1">
                <p class="text-xs text-text-3 uppercase tracking-wide font-medium">{{ t('reports.kpi.revenue') }}</p>
                <p class="text-2xl font-bold font-mono text-green-600">${{ parseFloat(salesSummary?.revenue ?? 0).toFixed(2) }}</p>
                <p class="text-xs text-text-3">{{ salesSummary?.count ?? 0 }} {{ t('reports.kpi.invoicesCollected') }}</p>
            </div>
            <div class="card flex flex-col gap-1">
                <p class="text-xs text-text-3 uppercase tracking-wide font-medium">{{ t('reports.kpi.expenses') }}</p>
                <p class="text-2xl font-bold font-mono text-red-500">${{ parseFloat(expenseSummary?.total ?? 0).toFixed(2) }}</p>
                <p class="text-xs text-text-3">{{ expenseSummary?.count ?? 0 }} {{ t('reports.kpi.records') }}</p>
            </div>
            <div class="card flex flex-col gap-1">
                <p class="text-xs text-text-3 uppercase tracking-wide font-medium">{{ t('reports.kpi.netProfit') }}</p>
                <p :class="['text-2xl font-bold font-mono', profit >= 0 ? 'text-emerald-600' : 'text-red-600']">${{ profit.toFixed(2) }}</p>
                <p class="text-xs text-text-3">{{ t('reports.kpi.margin') }}: {{ profitMargin }}%</p>
            </div>
            <div class="card flex flex-col gap-1">
                <p class="text-xs text-text-3 uppercase tracking-wide font-medium">{{ t('reports.kpi.inventoryValue') }}</p>
                <p class="text-2xl font-bold font-mono text-blue-600">${{ parseFloat(inventoryValue?.sale_value ?? 0).toFixed(2) }}</p>
                <p class="text-xs text-text-3">{{ t('reports.kpi.cost') }}: ${{ parseFloat(inventoryValue?.cost_value ?? 0).toFixed(2) }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

            <!-- Daily revenue chart -->
            <div class="lg:col-span-2 card">
                <h2 class="section-title mb-4">{{ t('reports.sections.dailyRevenue') }}</h2>
                <div class="h-48">
                    <canvas ref="chartRef"></canvas>
                </div>
                <p v-if="!dailyRevenue?.length" class="text-sm text-text-3 italic text-center py-6">{{ t('reports.noData.revenue') }}</p>
            </div>

            <!-- Payment methods -->
            <div class="card">
                <h2 class="section-title mb-3">{{ t('reports.sections.byMethod') }}</h2>
                <div v-if="byMethod?.length" class="space-y-3">
                    <div v-for="m in byMethod" :key="m.method">
                        <div class="flex items-center justify-between text-sm mb-1">
                            <span class="font-medium text-text">{{ methodLabels[m.method] ?? m.method }}</span>
                            <span class="font-mono text-text">${{ parseFloat(m.total).toFixed(2) }}</span>
                        </div>
                        <div class="h-2 bg-slate-100 rounded-full overflow-x-auto">
                            <div
                                class="h-full rounded-full"
                                :style="`width: ${pct(m.total, salesSummary?.revenue)}%; background-color: ${methodColors[m.method] ?? '#94a3b8'}`"
                            ></div>
                        </div>
                        <p class="text-xs text-text-3 mt-0.5">{{ m.count }} {{ t('reports.transactions') }} · {{ pct(m.total, salesSummary?.revenue) }}%</p>
                    </div>
                </div>
                <p v-else class="text-sm text-text-3 italic text-center py-4">{{ t('reports.noData.payment') }}</p>
            </div>

            <!-- Top services -->
            <div class="card">
                <h2 class="section-title mb-3">{{ t('reports.sections.topServices') }}</h2>
                <div class="overflow-x-auto" v-if="topServices?.length"><table  class="w-full text-sm">
                    <thead><tr class="border-b border-border">
                        <th class="pb-2 text-left text-text-2 font-medium">{{ t('reports.columns.service') }}</th>
                        <th class="pb-2 text-center text-text-2 font-medium w-10">{{ t('reports.columns.qty') }}</th>
                        <th class="pb-2 text-right text-text-2 font-medium w-24">{{ t('reports.columns.revenue') }}</th>
                    </tr></thead>
                    <tbody class="divide-y divide-border">
                        <tr v-for="(svc, i) in topServices" :key="i">
                            <td class="py-1.5 text-text text-xs">{{ svc.name }}</td>
                            <td class="py-1.5 text-center text-text-2">{{ svc.qty }}</td>
                            <td class="py-1.5 text-right font-mono text-text">${{ parseFloat(svc.revenue).toFixed(2) }}</td>
                        </tr>
                    </tbody>
                </table></div>
                <p v-else class="text-sm text-text-3 italic text-center py-4">{{ t('reports.noData.service') }}</p>
            </div>

            <!-- Top products -->
            <div class="card">
                <h2 class="section-title mb-3">{{ t('reports.sections.topProducts') }}</h2>
                <div class="overflow-x-auto" v-if="topProducts?.length"><table  class="w-full text-sm">
                    <thead><tr class="border-b border-border">
                        <th class="pb-2 text-left text-text-2 font-medium">{{ t('reports.columns.product') }}</th>
                        <th class="pb-2 text-center text-text-2 font-medium w-10">{{ t('reports.columns.qty') }}</th>
                        <th class="pb-2 text-right text-text-2 font-medium w-24">{{ t('reports.columns.revenue') }}</th>
                    </tr></thead>
                    <tbody class="divide-y divide-border">
                        <tr v-for="(prod, i) in topProducts" :key="i">
                            <td class="py-1.5 text-text text-xs">{{ prod.name }}</td>
                            <td class="py-1.5 text-center text-text-2">{{ prod.qty }}</td>
                            <td class="py-1.5 text-right font-mono text-text">${{ parseFloat(prod.revenue).toFixed(2) }}</td>
                        </tr>
                    </tbody>
                </table></div>
                <p v-else class="text-sm text-text-3 italic text-center py-4">{{ t('reports.noData.product') }}</p>
            </div>

            <!-- Expenses by category -->
            <div class="card">
                <h2 class="section-title mb-3">{{ t('reports.sections.expensesByCategory') }}</h2>
                <div v-if="expensesByCategory?.length" class="space-y-2">
                    <div v-for="cat in expensesByCategory" :key="cat.category">
                        <div class="flex items-center justify-between text-sm mb-1">
                            <div class="flex items-center gap-2">
                                <div class="w-2.5 h-2.5 rounded-full shrink-0" :style="`background-color: ${cat.color}`"></div>
                                <span class="text-text">{{ cat.category }}</span>
                            </div>
                            <span class="font-mono text-red-600">${{ parseFloat(cat.total).toFixed(2) }}</span>
                        </div>
                        <div class="h-1.5 bg-slate-100 rounded-full overflow-x-auto">
                            <div class="h-full rounded-full" :style="`width: ${pct(cat.total, expenseSummary?.total)}%; background-color: ${cat.color}`"></div>
                        </div>
                    </div>
                </div>
                <p v-else class="text-sm text-text-3 italic text-center py-4">{{ t('reports.noData.expense') }}</p>
            </div>

            <!-- Work order stats -->
            <div class="card">
                <h2 class="section-title mb-3">{{ t('reports.sections.workOrders') }}</h2>
                <div v-if="workOrderStats?.length" class="space-y-3">
                    <div v-for="stat in workOrderStats" :key="stat.status"
                        class="flex items-center justify-between py-2 border-b border-border last:border-0">
                        <div>
                            <span class="badge" :class="{
                                pending:     'badge-amber',
                                in_progress: 'badge-blue',
                                completed:   'badge-green',
                                cancelled:   'badge-gray',
                            }[stat.status] ?? 'badge-gray'">{{ t('workOrders.statuses.' + stat.status) }}</span>
                            <span class="text-xs text-text-3 ml-2">{{ stat.count }} {{ t('reports.columns.orders') }}</span>
                        </div>
                        <span class="font-mono text-sm text-text">${{ parseFloat(stat.value ?? 0).toFixed(2) }}</span>
                    </div>
                </div>
                <p v-else class="text-sm text-text-3 italic text-center py-4">{{ t('reports.noData.workOrders') }}</p>
            </div>

        </div>
    </AppLayout>
</template>
