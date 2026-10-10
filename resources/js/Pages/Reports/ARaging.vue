<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    rows:    Array,
    summary: Object,
});

const buckets = [
    { key: 'current', label: 'Corriente',   color: 'text-emerald-600', bg: 'bg-emerald-50' },
    { key: '1-30',    label: '1 – 30 días', color: 'text-amber-600',   bg: 'bg-amber-50'   },
    { key: '31-60',   label: '31 – 60 días',color: 'text-orange-600',  bg: 'bg-orange-50'  },
    { key: '60+',     label: '+ 60 días',   color: 'text-red-600',     bg: 'bg-red-50'     },
];

const bucketRow = { current: 'badge-green', '1-30': 'badge-amber', '31-60': 'badge-amber', '60+': 'badge-red' };

function fmt(n) { return Number(n).toLocaleString('en-US', { minimumFractionDigits: 2 }); }
</script>

<template>
    <AppLayout>
        <template #header>
            <div class="page-header">
                <div>
                    <h1 class="page-title">Cuentas por Cobrar</h1>
                    <p class="page-subtitle">{{ rows.length }} facturas con saldo pendiente</p>
                </div>
                <Link href="/reports" class="btn btn-ghost">← Reportes</Link>
            </div>
        </template>

        <!-- Resumen por bucket -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div v-for="b in buckets" :key="b.key" :class="['card flex flex-col gap-1', b.bg]">
                <p class="text-xs font-bold uppercase tracking-wide text-slate-500">{{ b.label }}</p>
                <p :class="['text-2xl font-black font-mono', b.color]">${{ fmt(summary[b.key] ?? 0) }}</p>
                <p class="text-xs text-slate-400">{{ rows.filter(r => r.bucket === b.key).length }} facturas</p>
            </div>
        </div>

        <!-- Total -->
        <div class="card mb-6 flex items-center justify-between">
            <span class="text-sm font-semibold text-text-2">Total pendiente</span>
            <span class="text-2xl font-black font-mono text-red-600">${{ fmt(summary.total ?? 0) }}</span>
        </div>

        <!-- Tabla -->
        <div class="card p-0 overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-border">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-text-2">Factura</th>
                        <th class="px-4 py-3 text-left font-medium text-text-2">Cliente</th>
                        <th class="px-4 py-3 text-left font-medium text-text-2">Fecha</th>
                        <th class="px-4 py-3 text-right font-medium text-text-2">Total</th>
                        <th class="px-4 py-3 text-right font-medium text-text-2">Pagado</th>
                        <th class="px-4 py-3 text-right font-medium text-text-2">Saldo</th>
                        <th class="px-4 py-3 text-center font-medium text-text-2">Vencimiento</th>
                        <th class="px-4 py-3 text-right font-medium text-text-2">Días</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    <tr v-for="row in rows" :key="row.id" class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3">
                            <Link :href="`/invoices/${row.id}`" class="font-mono text-xs font-semibold text-brand-700 hover:underline">
                                {{ row.number }}
                            </Link>
                        </td>
                        <td class="px-4 py-3">
                            <p class="font-medium text-text">{{ row.customer }}</p>
                            <p v-if="row.phone" class="text-xs text-text-3">{{ row.phone }}</p>
                        </td>
                        <td class="px-4 py-3 text-xs text-text-2">{{ row.date }}</td>
                        <td class="px-4 py-3 text-right font-mono text-text">${{ fmt(row.total) }}</td>
                        <td class="px-4 py-3 text-right font-mono text-emerald-600">${{ fmt(row.paid) }}</td>
                        <td class="px-4 py-3 text-right font-mono font-bold text-red-600">${{ fmt(row.balance) }}</td>
                        <td class="px-4 py-3 text-center">
                            <span :class="['badge', bucketRow[row.bucket] ?? 'badge-gray']">
                                {{ buckets.find(b => b.key === row.bucket)?.label }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right font-mono text-xs"
                            :class="row.days > 60 ? 'text-red-600 font-bold' : row.days > 30 ? 'text-amber-600' : 'text-text-3'">
                            {{ row.days }}d
                        </td>
                    </tr>
                    <tr v-if="!rows.length">
                        <td colspan="8" class="px-4 py-10 text-center text-text-3">
                            No hay facturas con saldo pendiente.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AppLayout>
</template>
