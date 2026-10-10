<script setup>
import { ref, computed } from 'vue';
import { router, Link, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';
import { confirmAction } from '@/utils/alerts';

const { t } = useI18n();

const props = defineProps({
    date:      String,
    payments:  Array,
    byMethod:  Array,
    total:     Number,
    count:     Number,
    cashClose: { type: Object, default: null },
});

const selectedDate = ref(props.date);

function changeDate() {
    router.get('/reports/cash-close', { date: selectedDate.value }, { preserveState: true, replace: true });
}

const methodMeta = {
    cash:     { icon: '💵', color: 'green' },
    card:     { icon: '💳', color: 'blue' },
    zelle:    { icon: '⚡', color: 'purple' },
    cashapp:  { icon: '💚', color: 'emerald' },
    transfer: { icon: '🏦', color: 'indigo' },
    check:    { icon: '📋', color: 'amber' },
    other:    { icon: '📦', color: 'slate' },
};

const methodColorClasses = {
    green:   { border: 'border-l-green-400',   dot: 'bg-green-400',   text: 'text-green-700',   badge: 'bg-green-100 text-green-700' },
    blue:    { border: 'border-l-blue-400',    dot: 'bg-blue-400',    text: 'text-blue-700',    badge: 'bg-blue-100 text-blue-700' },
    purple:  { border: 'border-l-purple-400',  dot: 'bg-purple-400',  text: 'text-purple-700',  badge: 'bg-purple-100 text-purple-700' },
    emerald: { border: 'border-l-emerald-400', dot: 'bg-emerald-400', text: 'text-emerald-700', badge: 'bg-emerald-100 text-emerald-700' },
    indigo:  { border: 'border-l-indigo-400',  dot: 'bg-indigo-400',  text: 'text-indigo-700',  badge: 'bg-indigo-100 text-indigo-700' },
    amber:   { border: 'border-l-amber-400',   dot: 'bg-amber-400',   text: 'text-amber-700',   badge: 'bg-amber-100 text-amber-700' },
    slate:   { border: 'border-l-slate-400',   dot: 'bg-slate-400',   text: 'text-slate-600',   badge: 'bg-slate-100 text-slate-600' },
};

function meta(method)   { return methodMeta[method] ?? methodMeta.other; }
function colors(method) { return methodColorClasses[meta(method).color]; }

function fmt(n) { return Number(n).toLocaleString('en-US', { minimumFractionDigits: 2 }); }
function fmtTime(dt) {
    if (!dt) return '—';
    return new Date(dt).toLocaleTimeString('es-US', { hour: '2-digit', minute: '2-digit' });
}
function fmtDateTime(dt) {
    if (!dt) return '—';
    return new Date(dt).toLocaleString('es-US', { dateStyle: 'medium', timeStyle: 'short' });
}
function print() { window.print(); }

// ── Estados ───────────────────────────────────────────────
// sin registro → apertura | abierta → cierre | cerrada → resumen
const isNotOpened = computed(() => !props.cashClose);
const isOpen      = computed(() => props.cashClose?.status === 'open');
const isClosed    = computed(() => props.cashClose?.status === 'closed');

// ── Formulario de apertura ────────────────────────────────
const openForm = useForm({
    date:         props.date,
    opening_fund: '',
    notes:        '',
});

async function submitOpen() {
    const ok = await confirmAction(
        `¿Abrir caja para el ${props.date}?`,
        `Fondo inicial: $${fmt(parseFloat(openForm.opening_fund) || 0)}`,
        'Sí, abrir caja'
    );
    if (!ok) return;
    openForm.post(route('reports.cash-close.open'), { preserveScroll: true });
}

// ── Formulario de cierre ──────────────────────────────────
const initialPhysical = Object.fromEntries(
    (props.byMethod ?? []).map(m => [m.method, props.cashClose?.physical_amounts?.[m.method] ?? ''])
);

const closeForm = useForm({
    date:             props.date,
    physical_amounts: initialPhysical,
    notes:            props.cashClose?.status === 'closed' ? (props.cashClose?.notes ?? '') : '',
});

const physicalTotal = computed(() =>
    Object.values(closeForm.physical_amounts)
        .reduce((s, v) => s + (parseFloat(v) || 0), 0)
);

const difference = computed(() => physicalTotal.value - (props.total ?? 0));

function rowDiff(method, total) {
    const v = parseFloat(closeForm.physical_amounts[method]);
    if (isNaN(v) || closeForm.physical_amounts[method] === '') return null;
    return v - total;
}

async function submitClose() {
    const ok = await confirmAction(
        `¿Confirmar cierre de caja del ${props.date}?`,
        'Una vez cerrado no podrás registrar más pagos para este día sin reabrirlo.',
        'Sí, cerrar caja'
    );
    if (!ok) return;
    closeForm.post(route('reports.cash-close.store'), { preserveScroll: true });
}

async function reopenClose() {
    const ok = await confirmAction(
        `¿Reabrir el cierre del ${props.date}?`,
        'El cierre quedará en estado abierto y podrá modificarse.',
        'Sí, reabrir'
    );
    if (!ok) return;
    router.post(route('reports.cash-close.reopen'), { date: props.date }, { preserveScroll: true });
}
</script>

<template>
    <AppLayout>
        <template #header>
            <div class="page-header">
                <div>
                    <h1 class="page-title">{{ t('cashClose.title') }}</h1>
                    <p class="page-subtitle flex items-center gap-2 flex-wrap">
                        <span>{{ date }}</span>
                        <span v-if="isNotOpened" class="inline-flex items-center gap-1 text-xs font-semibold px-2 py-0.5 rounded-full bg-slate-100 text-slate-500">
                            ⭕ Sin abrir
                        </span>
                        <span v-else-if="isOpen" class="inline-flex items-center gap-1.5 text-xs font-semibold px-2 py-0.5 rounded-full bg-amber-100 text-amber-700">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                            Caja abierta
                        </span>
                        <span v-else class="inline-flex items-center gap-1 text-xs font-semibold px-2 py-0.5 rounded-full bg-green-100 text-green-700">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/></svg>
                            Caja cerrada
                        </span>
                    </p>
                </div>
                <div class="flex gap-2 items-center flex-wrap">
                    <input v-model="selectedDate" @change="changeDate" type="date" class="input" />
                    <button @click="print" class="btn btn-ghost">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        Imprimir
                    </button>
                    <Link href="/reports" class="btn btn-ghost">← Reportes</Link>
                </div>
            </div>
        </template>

        <!-- ══ ESTADO 1: CAJA SIN ABRIR ══════════════════════════ -->
        <div v-if="isNotOpened" class="max-w-md mx-auto mt-8">
            <div class="card border-t-4 border-t-brand-500 shadow-lg p-8 text-center">
                <!-- Ícono -->
                <div class="w-16 h-16 rounded-full bg-brand-50 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-text mb-1">Abrir Caja</h2>
                <p class="text-sm text-text-3 mb-6">{{ date }} — Registra el fondo inicial antes de comenzar las operaciones del día.</p>

                <div class="space-y-4 text-left">
                    <div>
                        <label class="label">Fondo inicial en efectivo <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-text-3 pointer-events-none">$</span>
                            <input
                                v-model="openForm.opening_fund"
                                type="number" step="0.01" min="0"
                                placeholder="0.00"
                                class="input input-currency w-full text-lg font-mono"
                            />
                        </div>
                        <p class="text-xs text-text-3 mt-1">Dinero en efectivo con el que inicia la caja.</p>
                    </div>

                    <div>
                        <label class="label">Observaciones <span class="text-text-3 font-normal">(opcional)</span></label>
                        <textarea v-model="openForm.notes" class="input w-full" rows="2" placeholder="Notas de apertura..."></textarea>
                    </div>

                    <button
                        @click="submitOpen"
                        :disabled="openForm.processing || !openForm.opening_fund"
                        class="btn btn-primary w-full justify-center py-3 text-base"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                        {{ openForm.processing ? 'Abriendo...' : 'Abrir Caja' }}
                    </button>
                </div>
            </div>
        </div>

        <!-- ══ ESTADOS 2 y 3: CAJA ABIERTA o CERRADA ════════════ -->
        <template v-else>

            <!-- Banner de apertura (solo si se abrió formalmente) -->
            <div v-if="cashClose.opened_at" class="card mb-5 flex items-center gap-4 py-3 px-4 bg-slate-50 border border-border">
                <div class="w-8 h-8 rounded-full bg-brand-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                </div>
                <div class="flex-1 flex flex-wrap gap-x-6 gap-y-1 text-sm">
                    <span class="text-text-2">
                        <span class="font-medium text-text">Fondo inicial:</span>
                        <span class="font-mono font-bold text-brand-700 ml-1">${{ fmt(cashClose.opening_fund) }}</span>
                    </span>
                    <span class="text-text-2">
                        <span class="font-medium text-text">Abierta:</span>
                        <span class="ml-1">{{ fmtDateTime(cashClose.opened_at) }}</span>
                    </span>
                    <span v-if="cashClose.openedBy" class="text-text-2">
                        <span class="font-medium text-text">Por:</span>
                        <span class="ml-1">{{ cashClose.openedBy?.name }}</span>
                    </span>
                </div>
            </div>

            <!-- Tarjetas resumen por método -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-5">
                <div
                    v-for="m in byMethod" :key="m.method"
                    :class="['card border-l-4 flex flex-col gap-2 py-4', colors(m.method).border]"
                >
                    <div class="flex items-center justify-between">
                        <span :class="['text-xs font-bold uppercase tracking-wide', colors(m.method).text]">
                            {{ t('cashClose.methods.' + m.method) }}
                        </span>
                        <span :class="['text-xs px-1.5 py-0.5 rounded font-medium', colors(m.method).badge]">{{ m.count }}</span>
                    </div>
                    <p class="text-xl font-black font-mono text-text">${{ fmt(m.total) }}</p>
                    <p class="text-xs text-text-3">{{ meta(m.method).icon }} {{ m.count }} pago{{ m.count !== 1 ? 's' : '' }}</p>
                </div>
                <div v-if="!byMethod.length" class="col-span-4 card text-center text-text-3 py-8">
                    <p class="text-lg mb-1">📭</p>
                    Sin pagos registrados para {{ date }}.
                </div>
            </div>

            <!-- Banner total del día -->
            <div v-if="byMethod.length" class="card mb-5 flex items-center justify-between gap-4 bg-gradient-to-r from-brand-600 to-brand-700 text-white border-0">
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest opacity-80">Total recaudado</p>
                    <p class="text-sm opacity-70 mt-0.5">{{ date }} · {{ payments.length }} transacciones</p>
                </div>
                <span class="text-3xl font-black font-mono tabular-nums">${{ fmt(total) }}</span>
            </div>

            <!-- Grid: izquierda=detalle pagos / derecha=panel cierre -->
            <div class="grid grid-cols-1 xl:grid-cols-12 gap-5 items-start">

                <!-- ── Detalle de pagos ── -->
                <div class="xl:col-span-7 order-2 xl:order-1">
                    <div class="card p-0 overflow-hidden">
                        <div class="px-4 py-3 border-b border-border flex items-center justify-between">
                            <h2 class="section-title !mb-0">{{ t('cashClose.detail') }}</h2>
                            <span class="text-xs text-text-3 font-mono">{{ payments.length }} registros</span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-slate-50 border-b border-border">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-medium text-text-2 whitespace-nowrap">Hora</th>
                                        <th class="px-4 py-3 text-left font-medium text-text-2 whitespace-nowrap">Factura</th>
                                        <th class="px-4 py-3 text-left font-medium text-text-2 whitespace-nowrap">Cliente</th>
                                        <th class="px-4 py-3 text-left font-medium text-text-2 whitespace-nowrap">Método</th>
                                        <th class="px-4 py-3 text-left font-medium text-text-2 whitespace-nowrap">Referencia</th>
                                        <th class="px-4 py-3 text-right font-medium text-text-2 whitespace-nowrap">Monto</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-border">
                                    <tr v-for="p in payments" :key="p.id" class="hover:bg-slate-50 transition-colors">
                                        <td class="px-4 py-3 text-xs font-mono text-text-3 whitespace-nowrap">{{ fmtTime(p.paid_at) }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <Link :href="`/invoices/${p.id}`" class="font-mono text-xs font-semibold text-brand-600 hover:underline">
                                                {{ p.invoice_number }}
                                            </Link>
                                        </td>
                                        <td class="px-4 py-3 text-text">{{ p.customer }}</td>
                                        <td class="px-4 py-3">
                                            <span :class="['inline-flex items-center gap-1 text-xs font-medium px-2 py-0.5 rounded-full', colors(p.method).badge]">
                                                {{ meta(p.method).icon }} {{ t('cashClose.methods.' + p.method) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-xs text-text-3 font-mono">{{ p.reference ?? '—' }}</td>
                                        <td class="px-4 py-3 text-right font-mono font-semibold text-text tabular-nums">${{ fmt(p.amount) }}</td>
                                    </tr>
                                    <tr v-if="!payments.length">
                                        <td colspan="6" class="px-4 py-12 text-center text-text-3">
                                            <p class="text-2xl mb-2">📭</p>
                                            Sin pagos para {{ date }}.
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot v-if="payments.length" class="bg-slate-50 border-t-2 border-border">
                                    <tr>
                                        <td colspan="5" class="px-4 py-3 text-right text-sm font-bold text-text">Total</td>
                                        <td class="px-4 py-3 text-right font-mono font-black text-brand-700 tabular-nums text-base">${{ fmt(total) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- ── Panel de cierre / resumen ── -->
                <div class="xl:col-span-5 order-1 xl:order-2 sticky top-6">

                    <!-- CERRADO: solo lectura -->
                    <div v-if="isClosed" class="card border-t-4 border-t-emerald-500 shadow-md">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="section-title !mb-0">Cuadre de caja</h2>
                            <span class="inline-flex items-center gap-1 text-xs font-bold px-2.5 py-1 rounded-full bg-green-100 text-green-700">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/></svg>
                                Cerrada
                            </span>
                        </div>

                        <div class="overflow-x-auto rounded-xl border border-border mb-4">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="bg-slate-50 border-b border-border">
                                        <th class="px-3 py-2 text-left text-xs font-semibold text-text-2 uppercase tracking-wide">Método</th>
                                        <th class="px-3 py-2 text-right text-xs font-semibold text-text-2 uppercase tracking-wide">Sistema</th>
                                        <th class="px-3 py-2 text-right text-xs font-semibold text-text-2 uppercase tracking-wide">Físico</th>
                                        <th class="px-3 py-2 text-right text-xs font-semibold text-text-2 uppercase tracking-wide">Dif.</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-border">
                                    <tr v-for="m in byMethod" :key="m.method" class="hover:bg-slate-50">
                                        <td class="px-3 py-2.5 font-medium text-text">
                                            <span class="flex items-center gap-1.5">
                                                <span :class="['w-2 h-2 rounded-full flex-shrink-0', colors(m.method).dot]"></span>
                                                {{ meta(m.method).icon }} {{ t('cashClose.methods.' + m.method) }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-2.5 text-right font-mono text-xs text-text-2">${{ fmt(cashClose.system_amounts?.[m.method] ?? 0) }}</td>
                                        <td class="px-3 py-2.5 text-right font-mono text-xs font-semibold text-text">${{ fmt(cashClose.physical_amounts?.[m.method] ?? 0) }}</td>
                                        <td class="px-3 py-2.5 text-right font-mono text-xs font-semibold">
                                            <span :class="(cashClose.physical_amounts?.[m.method] ?? 0) - (cashClose.system_amounts?.[m.method] ?? 0) === 0 ? 'text-green-600' : 'text-red-500'">
                                                {{ ((cashClose.physical_amounts?.[m.method] ?? 0) - (cashClose.system_amounts?.[m.method] ?? 0)) >= 0 ? '+' : '' }}${{ fmt((cashClose.physical_amounts?.[m.method] ?? 0) - (cashClose.system_amounts?.[m.method] ?? 0)) }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot class="bg-slate-50 border-t-2 border-border">
                                    <tr>
                                        <td class="px-3 py-3 font-bold text-text text-sm">Total</td>
                                        <td class="px-3 py-3 text-right font-mono font-bold text-text text-xs">${{ fmt(cashClose.system_total) }}</td>
                                        <td class="px-3 py-3 text-right font-mono font-bold text-text text-xs">${{ fmt(cashClose.physical_total) }}</td>
                                        <td class="px-3 py-3 text-right font-mono font-black"
                                            :class="Number(cashClose.difference) === 0 ? 'text-green-600' : 'text-red-500'">
                                            {{ Number(cashClose.difference) >= 0 ? '+' : '' }}${{ fmt(cashClose.difference) }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div v-if="cashClose.notes" class="flex items-start gap-2 text-sm text-text-2 bg-slate-50 rounded-lg px-3 py-2.5 mb-4">
                            <svg class="w-4 h-4 mt-0.5 flex-shrink-0 text-text-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            <span class="italic text-xs">{{ cashClose.notes }}</span>
                        </div>

                        <div class="flex items-center justify-between flex-wrap gap-3">
                            <p class="text-xs text-text-3">
                                Cerrada {{ fmtDateTime(cashClose.closed_at) }}
                                <span v-if="cashClose.closedBy"> · {{ cashClose.closedBy?.name }}</span>
                            </p>
                            <button @click="reopenClose" class="btn btn-ghost btn-sm text-xs text-amber-600 border-amber-200 hover:border-amber-400">
                                Reabrir
                            </button>
                        </div>
                    </div>

                    <!-- ABIERTA: formulario de cierre -->
                    <div v-else class="card border-t-4 border-t-brand-500 shadow-md">
                        <div class="flex items-center justify-between mb-1">
                            <h2 class="section-title !mb-0">Cerrar caja</h2>
                            <span class="inline-flex items-center gap-1.5 text-xs font-bold px-2.5 py-1 rounded-full bg-amber-100 text-amber-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                                En operación
                            </span>
                        </div>
                        <p class="text-xs text-text-3 mb-4">Ingresa el monto físico contado para cada método de pago.</p>

                        <div v-if="byMethod.length" class="overflow-x-auto rounded-xl border border-border mb-4">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="bg-slate-50 border-b border-border">
                                        <th class="px-3 py-2 text-left text-xs font-semibold text-text-2 uppercase tracking-wide">Método</th>
                                        <th class="px-3 py-2 text-right text-xs font-semibold text-text-2 uppercase tracking-wide">Sistema</th>
                                        <th class="px-3 py-2 text-left text-xs font-semibold text-text-2 uppercase tracking-wide w-36">Físico</th>
                                        <th class="px-3 py-2 text-right text-xs font-semibold text-text-2 uppercase tracking-wide">Dif.</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-border">
                                    <tr v-for="m in byMethod" :key="m.method" class="hover:bg-slate-50">
                                        <td class="px-3 py-2.5 font-medium text-text">
                                            <span class="flex items-center gap-1.5">
                                                <span :class="['w-2 h-2 rounded-full flex-shrink-0', colors(m.method).dot]"></span>
                                                {{ meta(m.method).icon }} {{ t('cashClose.methods.' + m.method) }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-2.5 text-right font-mono text-xs text-text-2">${{ fmt(m.total) }}</td>
                                        <td class="px-3 py-2">
                                            <div class="relative">
                                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-text-3 text-xs pointer-events-none">$</span>
                                                <input
                                                    v-model="closeForm.physical_amounts[m.method]"
                                                    type="number" step="0.01" min="0"
                                                    :placeholder="fmt(m.total)"
                                                    class="input input-currency w-full py-1 text-xs"
                                                />
                                            </div>
                                        </td>
                                        <td class="px-3 py-2.5 text-right font-mono text-xs font-semibold">
                                            <template v-if="rowDiff(m.method, m.total) !== null">
                                                <span :class="rowDiff(m.method, m.total) === 0 ? 'text-green-600' : 'text-red-500'">
                                                    {{ rowDiff(m.method, m.total) >= 0 ? '+' : '' }}${{ fmt(rowDiff(m.method, m.total)) }}
                                                </span>
                                            </template>
                                            <span v-else class="text-text-3">—</span>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot class="border-t-2 border-border">
                                    <tr :class="difference === 0 ? 'bg-green-50' : 'bg-red-50'">
                                        <td class="px-3 py-3 font-bold text-text text-sm">Total</td>
                                        <td class="px-3 py-3 text-right font-mono text-xs font-bold text-text">${{ fmt(total) }}</td>
                                        <td class="px-3 py-3 font-mono text-xs font-bold text-text pl-3">${{ fmt(physicalTotal) }}</td>
                                        <td class="px-3 py-3 text-right font-mono font-black"
                                            :class="difference === 0 ? 'text-green-600' : 'text-red-500'">
                                            {{ difference >= 0 ? '+' : '' }}${{ fmt(difference) }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="mb-4">
                            <label class="label">Observaciones de cierre</label>
                            <textarea v-model="closeForm.notes" class="input w-full" rows="2" placeholder="Notas del cierre..."></textarea>
                        </div>

                        <div v-if="!byMethod.length" class="text-sm text-text-3 italic mb-4 text-center py-3">
                            Sin pagos registrados para {{ date }}.
                        </div>

                        <button
                            @click="submitClose"
                            :disabled="closeForm.processing || !byMethod.length"
                            class="btn btn-primary w-full justify-center py-3 text-base"
                        >
                            <svg v-if="!closeForm.processing" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            {{ closeForm.processing ? 'Cerrando...' : 'Confirmar Cierre de Caja' }}
                        </button>
                    </div>

                </div>
            </div>
        </template>

    </AppLayout>
</template>
