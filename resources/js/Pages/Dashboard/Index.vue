<script setup>
import Icon from '@/Components/Icon.vue';
import { ref, computed, onMounted, watch } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';

const { t } = useI18n();

const props = defineProps({
    period:          String,
    stats:           Object,
    chart_revenue:   Array,
    recent_orders:   Array,
    low_stock_items: Array,
});

// ── Period filter ──────────────────────────────────────────
const periods = [
    { key: 'day',   label: 'Hoy' },
    { key: 'week',  label: 'Semana' },
    { key: 'month', label: 'Mes' },
];

function setPeriod(p) {
    router.get('/dashboard', { period: p }, { preserveState: false, replace: true });
}

const periodLabel = computed(() => ({ day: 'hoy', week: 'esta semana', month: 'este mes' }[props.period] ?? 'este mes'));
const chartTitle  = computed(() => ({ day: 'Ingresos de hoy (por hora)', week: 'Ingresos — últimos 7 días', month: 'Ingresos — últimos 30 días' }[props.period] ?? ''));

// ── KPIs ───────────────────────────────────────────────────
const statusColors = { pending: 'badge-amber', in_progress: 'badge-blue', completed: 'badge-green', cancelled: 'badge-gray' };

const kpis = computed(() => [
    { label: 'Ventas',     value: `$${props.stats.sales_period}`,    sub: periodLabel.value, icon: 'revenue',   color: 'text-emerald-600', bg: 'bg-emerald-50' },
    { label: 'Gastos',     value: `$${props.stats.expenses_period}`, sub: periodLabel.value, icon: 'trend-down', color: 'text-red-600',     bg: 'bg-red-50' },
    { label: 'Utilidad',   value: `$${props.stats.profit_period}`,   sub: periodLabel.value, icon: 'trophy',    color: 'text-brand-600',   bg: 'bg-brand-50' },
    { label: 'Facturas',   value: props.stats.invoices_period,       sub: periodLabel.value, icon: 'trend-up',  color: 'text-violet-600',  bg: 'bg-violet-50' },
    { label: 'Pendientes', value: props.stats.open_orders,           sub: 'órdenes abiertas', icon: 'wrench',   color: 'text-amber-600',   bg: 'bg-amber-50' },
    { label: 'En proceso', value: props.stats.in_progress_orders,    sub: 'en taller ahora',  icon: 'cog',      color: 'text-blue-600',    bg: 'bg-blue-50' },
    { label: 'Stock bajo', value: props.stats.low_stock,             sub: 'productos',        icon: 'alert',    color: 'text-red-600',     bg: 'bg-red-50' },
    { label: 'Compras',    value: props.stats.pending_purchases,     sub: 'por recibir',      icon: 'cart',     color: 'text-violet-600',  bg: 'bg-violet-50' },
]);

// ── Chart ──────────────────────────────────────────────────
const chartRef = ref(null);
let chartInstance = null;

async function buildChart() {
    if (!chartRef.value || !props.chart_revenue) return;
    if (chartInstance) { chartInstance.destroy(); chartInstance = null; }

    const { Chart, BarController, BarElement, CategoryScale, LinearScale, Tooltip } = await import('chart.js');
    Chart.register(BarController, BarElement, CategoryScale, LinearScale, Tooltip);

    let labels = [];
    let data   = [];

    if (props.period === 'day') {
        const hourMap = {};
        for (const row of props.chart_revenue) {
            hourMap[Number(row.period_key)] = parseFloat(row.total);
        }
        for (let h = 0; h < 24; h++) {
            labels.push(h === 0 ? '12am' : h < 12 ? `${h}am` : h === 12 ? '12pm' : `${h - 12}pm`);
            data.push(hourMap[h] ?? 0);
        }
    } else {
        const days = props.period === 'week' ? 7 : 30;
        const today = new Date();
        const dateMap = {};
        for (const row of props.chart_revenue) {
            dateMap[row.period_key] = parseFloat(row.total);
        }
        for (let i = days - 1; i >= 0; i--) {
            const d = new Date(today);
            d.setDate(d.getDate() - i);
            const key = d.toISOString().slice(0, 10);
            labels.push(d.toLocaleDateString('es-DO', { month: 'short', day: 'numeric' }));
            data.push(dateMap[key] ?? 0);
        }
    }

    chartInstance = new Chart(chartRef.value, {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Ingresos ($)',
                data,
                backgroundColor: 'rgba(220, 38, 38, 0.12)',
                borderColor: 'rgba(220, 38, 38, 0.75)',
                borderWidth: 2,
                borderRadius: 6,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: { duration: 400, easing: 'easeInOutQuart' },
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: (ctx) => ` $${ctx.parsed.y.toFixed(2)}` } },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(148,163,184,0.12)' },
                    ticks: { callback: (v) => `$${v}`, color: '#94a3b8', font: { size: 11 } },
                },
                x: { grid: { display: false }, ticks: { color: '#94a3b8', font: { size: 11 } } },
            },
        },
    });
}

onMounted(() => buildChart());
watch(() => props.chart_revenue, () => buildChart(), { deep: true });
</script>

<template>
    <AppLayout>
        <!-- Header + filtros -->
        <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Panel de Control</h1>
                <p class="text-sm text-slate-500 mt-1">Resumen y rendimiento de tu negocio.</p>
            </div>

            <!-- Botones de período -->
            <div class="flex rounded-lg border border-slate-200 overflow-hidden shadow-sm">
                <button
                    v-for="p in periods" :key="p.key"
                    @click="setPeriod(p.key)"
                    :class="[
                        'px-4 py-2 text-sm font-semibold transition-colors',
                        period === p.key
                            ? 'bg-brand-600 text-white'
                            : 'bg-white text-slate-500 hover:bg-slate-50'
                    ]"
                >{{ p.label }}</button>
            </div>
        </div>

        <!-- KPIs Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div
                v-for="kpi in kpis" :key="kpi.label"
                class="card flex flex-col p-5 hover:shadow-md transition-all duration-300 relative overflow-hidden group border border-slate-100"
            >
                <div class="absolute -right-4 -top-4 opacity-5 group-hover:scale-125 transition-transform duration-500">
                    <Icon :name="kpi.icon" class="w-24 h-24" />
                </div>
                <div class="flex items-center justify-between mb-3 z-10">
                    <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wide">{{ kpi.label }}</h3>
                    <div :class="['w-8 h-8 rounded-full flex items-center justify-center shadow-sm', kpi.bg, kpi.color]">
                        <Icon :name="kpi.icon" class="w-4 h-4" />
                    </div>
                </div>
                <div class="z-10">
                    <p :class="['text-2xl font-black font-mono tracking-tight transition-all duration-300', kpi.color]">{{ kpi.value }}</p>
                    <p class="text-[10px] text-slate-400 font-medium mt-1 uppercase">{{ kpi.sub }}</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Revenue Chart -->
            <div class="lg:col-span-2 card flex flex-col border border-slate-100">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-bold text-slate-700">{{ chartTitle }}</h2>
                </div>
                <div v-if="!chart_revenue" class="flex-1 min-h-[250px] flex items-end gap-3 p-4 bg-slate-50/50 rounded-xl animate-pulse">
                    <div v-for="i in 12" :key="i" class="bg-slate-200/80 rounded-t flex-1" :style="{ height: (25 + (i * 13) % 65) + '%' }"></div>
                </div>
                <div v-else class="flex-1 min-h-[250px] relative">
                    <canvas ref="chartRef"></canvas>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card border border-slate-100 bg-slate-50/50">
                <h2 class="font-bold text-slate-700 mb-4">Acciones Rápidas</h2>
                <div class="grid grid-cols-1 gap-3">
                    <Link href="/pos" class="flex items-center p-3 rounded-xl bg-white border border-slate-200 hover:border-brand-500 hover:shadow-sm transition-all group">
                        <div class="w-10 h-10 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center mr-3 group-hover:scale-110 transition-transform"><Icon name="revenue" /></div>
                        <div>
                            <p class="text-sm font-bold text-slate-700 group-hover:text-brand-700 transition-colors">Nueva Venta (POS)</p>
                            <p class="text-xs text-slate-400">Crear factura rápida</p>
                        </div>
                    </Link>
                    <Link href="/work-orders/create" class="flex items-center p-3 rounded-xl bg-white border border-slate-200 hover:border-amber-500 hover:shadow-sm transition-all group">
                        <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center mr-3 group-hover:scale-110 transition-transform"><Icon name="wrench" /></div>
                        <div>
                            <p class="text-sm font-bold text-slate-700 group-hover:text-amber-700 transition-colors">Nueva Orden de Trabajo</p>
                            <p class="text-xs text-slate-400">Ingresar vehículo al taller</p>
                        </div>
                    </Link>
                    <Link href="/customers/create" class="flex items-center p-3 rounded-xl bg-white border border-slate-200 hover:border-emerald-500 hover:shadow-sm transition-all group">
                        <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center mr-3 group-hover:scale-110 transition-transform"><Icon name="user" /></div>
                        <div>
                            <p class="text-sm font-bold text-slate-700 group-hover:text-emerald-700 transition-colors">Nuevo Cliente</p>
                            <p class="text-xs text-slate-400">Registrar cliente y vehículo</p>
                        </div>
                    </Link>
                    <Link href="/purchases/create" class="flex items-center p-3 rounded-xl bg-white border border-slate-200 hover:border-violet-500 hover:shadow-sm transition-all group">
                        <div class="w-10 h-10 rounded-lg bg-violet-50 text-violet-600 flex items-center justify-center mr-3 group-hover:scale-110 transition-transform"><Icon name="cart" /></div>
                        <div>
                            <p class="text-sm font-bold text-slate-700 group-hover:text-violet-700 transition-colors">Nueva Compra</p>
                            <p class="text-xs text-slate-400">Ingresar inventario</p>
                        </div>
                    </Link>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent work orders -->
            <div class="card border border-slate-100 p-0 overflow-hidden flex flex-col">
                <div class="p-5 flex items-center justify-between border-b border-border">
                    <h2 class="font-bold text-slate-700">Órdenes Recientes</h2>
                    <Link href="/work-orders" class="text-xs text-brand-600 font-semibold hover:underline">Ver todas</Link>
                </div>
                <div v-if="!recent_orders" class="p-5 space-y-3">
                    <div v-for="i in 4" :key="i" class="h-12 bg-slate-100 rounded-lg animate-pulse"></div>
                </div>
                <div v-else class="overflow-x-auto">
                    <table class="w-full text-sm" v-if="recent_orders.length">
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="order in recent_orders" :key="order.id" class="hover:bg-slate-50 transition-colors">
                                <td class="p-4">
                                    <Link :href="`/work-orders/${order.id}`" class="font-mono text-sm font-bold text-brand-700 hover:underline block mb-0.5">
                                        {{ order.number }}
                                    </Link>
                                    <span :class="['badge', statusColors[order.status] ?? 'badge-gray']">{{ t('workOrders.statuses.' + order.status) }}</span>
                                </td>
                                <td class="p-4">
                                    <p class="text-sm text-slate-800 font-medium">{{ order.customer?.name ?? t('common.walkIn') }}</p>
                                    <p v-if="order.vehicle" class="text-xs text-slate-500 mt-0.5">
                                        {{ order.vehicle.year }} {{ order.vehicle.make }} {{ order.vehicle.model }}
                                    </p>
                                </td>
                                <td class="p-4 text-right">
                                    <p class="font-mono font-semibold text-slate-700">${{ order.total }}</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div v-else class="p-10 flex flex-col items-center justify-center text-center">
                        <div class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center text-slate-300 mb-3"><Icon name="clipboard" class="w-6 h-6"/></div>
                        <p class="text-sm font-medium text-slate-600">No hay órdenes recientes</p>
                    </div>
                </div>
            </div>

            <!-- Low stock alert panel -->
            <div class="card border border-red-100 p-0 overflow-hidden flex flex-col" v-if="low_stock_items?.length">
                <div class="p-5 flex items-center justify-between border-b border-red-100 bg-red-50/30">
                    <h2 class="font-bold text-red-600 flex items-center gap-2">
                        <Icon name="alert" class="w-5 h-5"/>
                        Inventario Bajo ({{ low_stock_items.length }})
                    </h2>
                    <Link href="/products?low_stock=true" class="text-xs text-red-600 font-semibold hover:underline">Ver inventario</Link>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="p in low_stock_items.slice(0, 5)" :key="p.id" class="hover:bg-red-50/50 transition-colors">
                                <td class="p-4">
                                    <p class="font-medium text-slate-800">{{ p.name }}</p>
                                    <p class="font-mono text-xs text-slate-400 mt-0.5">{{ p.sku ?? '—' }}</p>
                                </td>
                                <td class="p-4 text-center">
                                    <span class="inline-block px-2 py-1 rounded bg-red-100 text-red-700 font-bold text-xs">
                                        Quedan {{ p.stock_qty }}
                                    </span>
                                </td>
                                <td class="p-4 text-right">
                                    <Link :href="`/products/${p.id}`" class="btn btn-sm bg-white border border-slate-200 text-slate-600 shadow-sm">Ver</Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div v-if="low_stock_items.length > 5" class="p-3 text-center bg-slate-50 border-t border-slate-100">
                        <Link href="/products?low_stock=true" class="text-xs font-medium text-slate-500 hover:text-brand-600">+ {{ low_stock_items.length - 5 }} productos más</Link>
                    </div>
                </div>
            </div>

            <div v-else class="card border border-emerald-100 bg-emerald-50/30 p-10 flex flex-col items-center justify-center text-center">
                <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <h3 class="font-bold text-emerald-800">Inventario Saludable</h3>
                <p class="text-sm text-emerald-600/80 mt-1">Ningún producto está por debajo de su límite mínimo.</p>
            </div>
        </div>
    </AppLayout>
</template>
