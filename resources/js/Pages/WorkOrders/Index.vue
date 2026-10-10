<script setup>

import Icon from '@/Components/Icon.vue';
import { ref, computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';
import { usePermissions } from '@/composables/usePermissions.js';
import { confirmAction } from '@/utils/alerts';

const { t } = useI18n();
const { can } = usePermissions();

const props = defineProps({
    orders:      Array,
    filters:     Object,
    statuses:    Array,
    technicians: Array,
});

const view = ref(props.filters.view ?? 'kanban');

const columns = computed(() => [
    { key: 'pending',     label: t('workOrders.statuses.pending'),     color: 'bg-amber-50 border-amber-200',   dot: 'bg-amber-400' },
    { key: 'in_progress', label: t('workOrders.statuses.in_progress'),  color: 'bg-blue-50 border-blue-200',     dot: 'bg-blue-500' },
    { key: 'completed',   label: t('workOrders.statuses.completed'),    color: 'bg-green-50 border-green-200',   dot: 'bg-green-500' },
    { key: 'cancelled',   label: t('workOrders.statuses.cancelled'),    color: 'bg-slate-50 border-slate-200',   dot: 'bg-slate-400' },
]);

const grouped = computed(() => {
    const map = {};
    for (const col of columns.value) map[col.key] = [];
    for (const o of props.orders) {
        if (map[o.status]) map[o.status].push(o);
    }
    return map;
});

async function changeStatus(order, newStatus) {
    if (newStatus === 'completed' || newStatus === 'cancelled') {
        const labels = { completed: 'completada', cancelled: 'cancelada' };
        const ok = await confirmAction(
            `¿Marcar orden ${order.number} como ${labels[newStatus]}?`,
            newStatus === 'completed' ? 'Se descontará el inventario de los productos usados.' : '',
            'Sí, cambiar estado'
        );
        if (!ok) return;
    }
    router.patch(`/work-orders/${order.id}/status`, { status: newStatus }, { preserveScroll: true });
}

function statusColor(status) {
    return { pending: 'badge-amber', in_progress: 'badge-blue', completed: 'badge-green', cancelled: 'badge-gray' }[status] ?? 'badge-gray';
}
</script>

<template>
    <AppLayout>
        <template #header>
            <div class="page-header">
                <div>
                    <h1 class="page-title">{{ t('workOrders.title') }}</h1>
                    <p class="page-subtitle">{{ orders.length }} {{ t('workOrders.activeOrders') }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <!-- View toggle -->
                    <div class="flex rounded-lg border border-border overflow-x-auto">
                        <button
                            v-for="v in ['kanban','list']" :key="v"
                            @click="view = v"
                            :class="['px-3 py-1.5 text-xs font-medium transition-colors', view === v ? 'bg-brand-600 text-white' : 'text-text-2 hover:bg-slate-50']"
                        >{{ v === 'kanban' ? t('workOrders.boardView') : t('workOrders.listView') }}</button>
                    </div>
                    <Link v-if="can('manage_work_orders')" href="/work-orders/create" class="btn btn-primary">
                                <Icon name="plus" />
                                {{ t('workOrders.newOrder') }}
                            </Link>
                </div>
            </div>
        </template>

        <!-- Kanban view -->
        <div v-if="view === 'kanban'" class="flex gap-4 overflow-x-auto kanban-board">
            <div
                v-for="col in columns" :key="col.key"
                :class="['flex-shrink-0 w-72 rounded-xl border-2 p-3 flex flex-col', col.color]"
            >
                <!-- Column header — fijo en la parte superior -->
                <div class="flex items-center gap-2 mb-3 px-1 flex-shrink-0">
                    <div :class="['w-2 h-2 rounded-full', col.dot]"></div>
                    <span class="font-semibold text-sm text-text">{{ col.label }}</span>
                    <span class="ml-auto text-xs text-text-3 font-mono transition-all duration-300">{{ grouped[col.key].length }}</span>
                </div>

                <!-- Cards — área con scroll interno -->
                <TransitionGroup
                    name="card"
                    tag="div"
                    class="relative flex-1 overflow-y-auto space-y-2 min-h-[4rem] kanban-scroll"
                >
                    <div
                        v-for="order in grouped[col.key]" :key="order.id"
                        class="bg-white rounded-lg border border-border p-3 shadow-sm hover:shadow-md transition-shadow cursor-pointer"
                    >
                        <Link :href="`/work-orders/${order.id}`" class="block">
                            <div class="flex items-start justify-between gap-1 mb-1">
                                <span class="font-semibold text-xs text-brand-700">{{ order.number }}</span>
                                <span v-if="order.promised_at" class="text-xs text-text-3">
                                    {{ new Date(order.promised_at).toLocaleDateString() }}
                                </span>
                            </div>
                            <p class="text-sm font-medium text-text leading-tight">{{ order.customer?.name ?? t('common.walkIn') }}</p>
                            <p v-if="order.vehicle" class="text-xs text-text-2 mt-0.5">
                                {{ order.vehicle.year }} {{ order.vehicle.make }} {{ order.vehicle.model }}
                            </p>
                            <div class="flex items-center justify-between mt-2">
                                <span class="text-xs text-text-3">
                                    {{ order.services_count }} {{ t('workOrders.svc') }} · {{ order.products_count }} {{ t('workOrders.parts') }}
                                </span>
                                <span class="text-xs font-mono font-medium text-text">${{ order.total }}</span>
                            </div>
                        </Link>
                        <div class="flex gap-1 mt-2 pt-2 border-t border-border flex-wrap">
                            <button
                                v-for="s in statuses.filter(x => x !== col.key && !(order.invoiced && (x === 'pending' || x === 'cancelled')))" :key="s"
                                @click="changeStatus(order, s)"
                                class="text-xs px-2 py-0.5 rounded border border-border text-text-2 hover:bg-slate-100 capitalize transition-colors"
                            >→ {{ t('workOrders.statuses.' + s) }}</button>
                        </div>
                    </div>
                    <p v-if="!grouped[col.key].length" key="empty" class="text-xs text-text-3 text-center py-4 italic">{{ t('common.empty') }}</p>
                </TransitionGroup>
            </div>
        </div>

        <!-- List view -->
        <div v-else class="card p-0 overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-border">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-text-2">{{ t('workOrders.columns.number') }}</th>
                        <th class="px-4 py-3 text-left font-medium text-text-2">{{ t('workOrders.columns.customer') }}</th>
                        <th class="px-4 py-3 text-left font-medium text-text-2">{{ t('workOrders.columns.vehicle') }}</th>
                        <th class="px-4 py-3 text-left font-medium text-text-2">{{ t('workOrders.columns.assignedTo') }}</th>
                        <th class="px-4 py-3 text-left font-medium text-text-2">{{ t('workOrders.columns.status') }}</th>
                        <th class="px-4 py-3 text-right font-medium text-text-2">{{ t('workOrders.columns.total') }}</th>
                        <th class="px-4 py-3 text-right font-medium text-text-2">{{ t('workOrders.columns.actions') }}</th>
                    </tr>
                </thead>
                <TransitionGroup name="row" tag="tbody" class="divide-y divide-border">
                    <tr v-for="order in orders" :key="order.id" class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-mono font-semibold text-brand-700">{{ order.number }}</td>
                        <td class="px-4 py-3 text-text">{{ order.customer?.name ?? t('common.walkIn') }}</td>
                        <td class="px-4 py-3 text-text-2">
                            {{ order.vehicle ? `${order.vehicle.year} ${order.vehicle.make} ${order.vehicle.model}` : '—' }}
                        </td>
                        <td class="px-4 py-3 text-text-2">{{ order.assigned_to?.name ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <span :class="['badge', statusColor(order.status)]">{{ t('workOrders.statuses.' + order.status) }}</span>
                        </td>
                        <td class="px-4 py-3 text-right font-mono">${{ order.total }}</td>
                        <td class="px-4 py-3 text-right">
                            <Link :href="`/work-orders/${order.id}`" class="btn btn-ghost btn-sm">
                                <Icon name="view" />
                                {{ t('common.view') }}
                            </Link>
                        </td>
                    </tr>
                    <tr v-if="!orders.length" key="empty">
                        <td colspan="7" class="px-4 py-10 text-center text-text-3">{{ t('workOrders.noResults') }}</td>
                    </tr>
                </TransitionGroup>
            </table>
        </div>
    </AppLayout>
</template>

<style scoped>
/* ── Kanban cards ── */
.card-move,
.card-enter-active,
.card-leave-active {
    transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
}
.card-enter-from {
    opacity: 0;
    transform: translateY(-10px) scale(0.97);
}
.card-leave-to {
    opacity: 0;
    transform: translateY(6px) scale(0.97);
}
.card-leave-active {
    position: absolute;
    width: calc(100% - 1.5rem);
}

/* ── List view rows ── */
.row-enter-active,
.row-leave-active {
    transition: all 0.3s ease;
}
.row-enter-from {
    opacity: 0;
    transform: translateX(-8px);
}
.row-leave-to {
    opacity: 0;
    transform: translateX(8px);
}

/* ── Kanban board: altura del viewport menos cabecera ── */
.kanban-board {
    height: calc(100vh - 178px);
    min-height: 420px;
    align-items: flex-start;
}

/* ── Scroll fino dentro de cada columna ── */
.kanban-scroll {
    scrollbar-width: thin;
    scrollbar-color: rgba(148, 163, 184, 0.4) transparent;
    padding-right: 2px;
}
.kanban-scroll::-webkit-scrollbar        { width: 4px; }
.kanban-scroll::-webkit-scrollbar-track  { background: transparent; }
.kanban-scroll::-webkit-scrollbar-thumb  { background: rgba(148, 163, 184, 0.45); border-radius: 2px; }
.kanban-scroll::-webkit-scrollbar-thumb:hover { background: rgba(100, 116, 139, 0.7); }
</style>
