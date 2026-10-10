<script setup>
import Icon from '@/Components/Icon.vue';
import { ref, computed } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';
import { confirmDelete, confirmAction } from '@/utils/alerts';

const { t } = useI18n();

const props = defineProps({
    order:                Object,
    statuses:             Array,
    technicians:          Array,
    disposalCostUnit:     { type: Number, default: 0 },
    availableConsumables: { type: Array, default: () => [] },
});

const statusColors = {
    pending:     'badge-amber',
    in_progress: 'badge-blue',
    completed:   'badge-green',
    cancelled:   'badge-gray',
};

async function changeStatus(status) {
    const needsConfirm = status === 'completed' || status === 'cancelled';
    if (needsConfirm) {
        const labels = { completed: 'completada', cancelled: 'cancelada' };
        const ok = await confirmAction(
            `¿Marcar orden como ${labels[status]}?`,
            status === 'completed' ? 'Se descontará el inventario de los productos usados.' : '',
            'Sí, cambiar estado'
        );
        if (!ok) return;
    }
    router.patch(`/work-orders/${props.order.id}/status`, { status }, { preserveScroll: true });
}

function createInvoice() {
    router.get('/invoices/create', { work_order_id: props.order.id });
}

// ── Consumables ──────────────────────────────────────────
const showConsumableForm = ref(false);
const selectedConsumableId = ref('');

const consumableForm = useForm({
    product_id:      null,
    name:            '',
    unit:            'unit',
    qty_used:        1,
    unit_cost:       '',
    notes:           '',
    save_to_catalog: false,
});

const selectedProduct = computed(() => {
    if (!selectedConsumableId.value || selectedConsumableId.value === 'custom') return null;
    return props.availableConsumables?.find(p => p.id === Number(selectedConsumableId.value)) || null;
});

function onSelectConsumable() {
    if (selectedConsumableId.value === 'custom') {
        consumableForm.product_id = null;
        consumableForm.name = '';
        consumableForm.unit = 'unit';
        consumableForm.unit_cost = '';
        consumableForm.qty_used = 1;
        consumableForm.save_to_catalog = false;
        return;
    }

    if (!selectedConsumableId.value) {
        consumableForm.product_id = null;
        consumableForm.name = '';
        consumableForm.unit = 'unit';
        consumableForm.unit_cost = '';
        consumableForm.qty_used = 1;
        consumableForm.save_to_catalog = false;
        return;
    }

    const prod = selectedProduct.value;
    if (prod) {
        consumableForm.product_id = prod.id;
        consumableForm.name = prod.name;
        consumableForm.unit = prod.unit || 'unit';
        consumableForm.unit_cost = Number(prod.cost_price || 0);
        consumableForm.qty_used = consumableForm.qty_used || 1;
        consumableForm.save_to_catalog = false;
    }
}

function resetConsumableForm() {
    consumableForm.reset();
    selectedConsumableId.value = '';
    consumableForm.qty_used = 1;
    consumableForm.save_to_catalog = false;
}

function submitConsumable() {
    consumableForm.post(route('work-orders.consumables.store', props.order.id), {
        preserveScroll: true,
        onSuccess: () => {
            resetConsumableForm();
            showConsumableForm.value = false;
        },
    });
}

async function removeConsumable(id) {
    if (!await confirmDelete('¿Eliminar este consumible?')) return;
    router.delete(route('work-orders.consumables.destroy', [props.order.id, id]), { preserveScroll: true });
}

// ── Tire Disposal ─────────────────────────────────────────
const showDisposalForm = ref(false);
const disposalForm = useForm({
    qty_total:       1,
    qty_client_kept: 0,
    notes:           '',
});

const disposalPreview = ref(0);
function updateDisposalPreview() {
    const total = parseInt(disposalForm.qty_total) || 0;
    const kept  = parseInt(disposalForm.qty_client_kept) || 0;
    disposalPreview.value = Math.max(0, total - kept);
}

function resetDisposalForm() {
    disposalForm.reset();
    disposalForm.qty_total       = 1;
    disposalForm.qty_client_kept = 0;
    disposalForm.notes           = '';
    disposalPreview.value        = 1;
}

function submitDisposal() {
    if (!disposalForm.qty_total || parseInt(disposalForm.qty_total) < 1) return;
    if (parseInt(disposalForm.qty_client_kept) > parseInt(disposalForm.qty_total)) return;

    disposalForm.post(route('work-orders.tire-disposal.store', props.order.id), {
        preserveScroll: true,
        onSuccess: () => {
            resetDisposalForm();
            showDisposalForm.value = false;
        },
        onError: () => {
            // Keep form open so errors are visible
        },
    });
}

async function removeDisposal(id) {
    if (!await confirmDelete('¿Eliminar este registro de desecho?')) return;
    router.delete(route('work-orders.tire-disposal.destroy', [props.order.id, id]), { preserveScroll: true });
}

const disposalStatusColors = { pending: 'badge-amber', partial: 'badge-blue', collected: 'badge-green' };
</script>

<template>
    <AppLayout>
        <template #header>
            <div class="page-header">
                <div>
                    <nav class="breadcrumb mb-1">
                        <Link href="/work-orders" class="breadcrumb-link">{{ t('workOrders.title') }}</Link>
                        <span class="breadcrumb-sep">/</span>
                        <span class="breadcrumb-current">{{ order.number }}</span>
                    </nav>
                    <div class="flex items-center gap-3">
                        <h1 class="page-title">{{ order.number }}</h1>
                        <span :class="['badge', statusColors[order.status]]">{{ t('workOrders.statuses.' + order.status) }}</span>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <Link :href="`/work-orders/${order.id}/edit`" class="btn btn-ghost">
                        <Icon name="edit" />
                        {{ t('common.edit') }}
                    </Link>
                    <button
                        v-if="order.status === 'completed' && !order.invoiced"
                        @click="createInvoice"
                        class="btn btn-primary"
                    >{{ t('workOrders.show.invoice') }}</button>
                </div>
            </div>
        </template>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            <!-- Left: details -->
            <div class="lg:col-span-2 space-y-5">

                <!-- Customer & vehicle -->
                <div class="card grid grid-cols-2 gap-4">
                    <div>
                        <p class="label">{{ t('workOrders.form.customer') }}</p>
                        <p class="font-medium text-text">
                            <Link v-if="order.customer" :href="`/customers/${order.customer.id}`" class="text-brand-600 hover:underline">
                                {{ order.customer.name }}
                            </Link>
                            <span v-else class="text-text-2">{{ t('common.walkIn') }}</span>
                        </p>
                        <p v-if="order.customer?.phone" class="text-sm text-text-2">{{ order.customer.phone }}</p>
                    </div>
                    <div v-if="order.vehicle">
                        <p class="label">{{ t('workOrders.form.vehicle') }}</p>
                        <p class="font-medium text-text">{{ order.vehicle.year }} {{ order.vehicle.make }} {{ order.vehicle.model }}</p>
                        <p v-if="order.vehicle.plate" class="text-sm text-text-2">{{ order.vehicle.plate }}</p>
                        <p v-if="order.vehicle.tire_size" class="text-xs text-text-3">{{ t('workOrders.form.vehicle') }}: {{ order.vehicle.tire_size }}</p>
                    </div>
                </div>

                <!-- Services -->
                <div class="card">
                    <h3 class="section-title mb-3">{{ t('workOrders.form.services') }}</h3>
                    <div class="overflow-x-auto" v-if="order.services.length">
                        <table class="w-full whitespace-nowrap text-sm">
                            <thead><tr class="border-b border-border">
                                <th class="pb-2 text-left text-text-2 font-medium">{{ t('services.columns.service') }}</th>
                                <th class="pb-2 text-center text-text-2 font-medium w-12">{{ t('workOrders.form.qty') }}</th>
                                <th class="pb-2 text-right text-text-2 font-medium w-24">{{ t('workOrders.form.price') }}</th>
                                <th class="pb-2 text-right text-text-2 font-medium w-20">{{ t('workOrders.form.total') }}</th>
                            </tr></thead>
                            <tbody class="divide-y divide-border">
                                <tr v-for="svc in order.services" :key="svc.id">
                                    <td class="py-2 text-text">{{ svc.name }}</td>
                                    <td class="py-2 text-center text-text-2">{{ svc.qty }}</td>
                                    <td class="py-2 text-right font-mono text-text-2">${{ svc.unit_price }}</td>
                                    <td class="py-2 text-right font-mono text-text">${{ (svc.qty * svc.unit_price).toFixed(2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <p v-else class="text-sm text-text-3 italic">{{ t('workOrders.noResults') }}</p>
                </div>

                <!-- Parts -->
                <div class="card">
                    <h3 class="section-title mb-3">{{ t('workOrders.form.products') }}</h3>
                    <div class="overflow-x-auto" v-if="order.products.length">
                        <table class="w-full whitespace-nowrap text-sm">
                            <thead><tr class="border-b border-border">
                                <th class="pb-2 text-left text-text-2 font-medium">{{ t('workOrders.show.part') }}</th>
                                <th class="pb-2 text-center text-text-2 font-medium w-12">{{ t('workOrders.form.qty') }}</th>
                                <th class="pb-2 text-right text-text-2 font-medium w-24">{{ t('workOrders.form.price') }}</th>
                                <th class="pb-2 text-right text-text-2 font-medium w-20">{{ t('workOrders.form.total') }}</th>
                            </tr></thead>
                            <tbody class="divide-y divide-border">
                                <tr v-for="prod in order.products" :key="prod.id">
                                    <td class="py-2 text-text">{{ prod.name }}</td>
                                    <td class="py-2 text-center text-text-2">{{ prod.qty }}</td>
                                    <td class="py-2 text-right font-mono text-text-2">${{ prod.unit_price }}</td>
                                    <td class="py-2 text-right font-mono text-text">${{ (prod.qty * prod.unit_price).toFixed(2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <p v-else class="text-sm text-text-3 italic">{{ t('workOrders.noResults') }}</p>
                </div>

                <!-- Consumables -->
                <div class="card">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <h3 class="section-title">{{ t('workOrders.show.consumables.title') }}</h3>
                            <Link href="/products?type=material" class="text-xs text-brand-600 hover:underline flex items-center gap-1 font-medium">
                                <span>Administrar catálogo</span>
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </Link>
                        </div>
                        <button @click="showConsumableForm = !showConsumableForm" class="btn btn-ghost btn-sm text-xs">
                            {{ t('workOrders.show.consumables.add') }}
                        </button>
                    </div>

                    <!-- Add form -->
                    <div v-if="showConsumableForm" class="mb-4 p-3.5 bg-slate-50 rounded-lg border border-border space-y-3">
                        <!-- Selector from Catalog -->
                        <div>
                            <label class="label font-semibold text-text">Seleccionar de catálogo</label>
                            <select v-model="selectedConsumableId" @change="onSelectConsumable" class="input w-full">
                                <option value="">— Seleccionar consumible registrado —</option>
                                <option v-for="c in availableConsumables" :key="c.id" :value="c.id">
                                    {{ c.name }} · Costo: ${{ Number(c.cost_price).toFixed(2) }} ({{ c.unit || 'unit' }}) · Stock: {{ c.stock_qty }}
                                </option>
                                <option value="custom">+ Registrar consumible manual / nuevo</option>
                            </select>
                        </div>

                        <!-- Stock info badge if selected -->
                        <div v-if="selectedProduct" class="flex items-center justify-between text-xs px-3 py-1.5 bg-blue-50 text-blue-800 rounded border border-blue-200">
                            <span>Stock disponible en taller: <strong>{{ selectedProduct.stock_qty }} {{ selectedProduct.unit || 'unidades' }}</strong></span>
                            <span>Costo unitario registrado: <strong>${{ Number(selectedProduct.cost_price).toFixed(2) }}</strong></span>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            <div class="col-span-2">
                                <label class="label">{{ t('workOrders.show.consumables.name') }} <span class="text-red-500">*</span></label>
                                <input v-model="consumableForm.name" type="text" class="input w-full"
                                    :placeholder="t('workOrders.show.consumables.customName')" required />
                                <p v-if="consumableForm.errors.name" class="text-red-500 text-xs mt-1">{{ consumableForm.errors.name }}</p>
                            </div>
                            <div>
                                <label class="label">{{ t('workOrders.show.consumables.unit') }}</label>
                                <input v-model="consumableForm.unit" type="text" class="input w-full" placeholder="unit, lt, oz…" />
                            </div>
                            <div>
                                <label class="label">{{ t('workOrders.show.consumables.qty') }} <span class="text-red-500">*</span></label>
                                <input v-model="consumableForm.qty_used" type="number" step="0.001" min="0.001" class="input w-full" required />
                                <p v-if="consumableForm.errors.qty_used" class="text-red-500 text-xs mt-1">{{ consumableForm.errors.qty_used }}</p>
                            </div>
                            <div>
                                <label class="label">{{ t('workOrders.show.consumables.unitCost') }} <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-text-3 text-sm pointer-events-none">$</span>
                                    <input v-model="consumableForm.unit_cost" type="number" step="0.01" min="0" class="input input-currency w-full" required />
                                </div>
                                <p v-if="consumableForm.errors.unit_cost" class="text-red-500 text-xs mt-1">{{ consumableForm.errors.unit_cost }}</p>
                            </div>
                            <div class="col-span-2 sm:col-span-3">
                                <label class="label">{{ t('workOrders.show.consumables.notes') }}</label>
                                <input v-model="consumableForm.notes" type="text" class="input w-full" placeholder="Notas adicionales..." />
                            </div>
                        </div>

                        <!-- Checkbox to save new consumable to catalog if it's custom -->
                        <div v-if="!consumableForm.product_id" class="flex items-center gap-2 pt-1">
                            <input v-model="consumableForm.save_to_catalog" type="checkbox" id="save_to_catalog" class="rounded text-brand-600 focus:ring-brand-500 cursor-pointer" />
                            <label for="save_to_catalog" class="text-xs text-text-2 cursor-pointer select-none">
                                Guardar este insumo en el catálogo para poder seleccionarlo en futuras órdenes
                            </label>
                        </div>

                        <div class="flex items-center justify-between pt-1">
                            <div v-if="consumableForm.qty_used && consumableForm.unit_cost" class="text-xs text-text-2">
                                Costo total: <strong class="font-mono text-sm">${{ (Number(consumableForm.qty_used) * Number(consumableForm.unit_cost)).toFixed(2) }}</strong>
                            </div>
                            <div class="flex gap-2 ml-auto">
                                <button @click="submitConsumable" :disabled="consumableForm.processing" class="btn btn-primary btn-sm text-xs">
                                    {{ consumableForm.processing ? '…' : t('workOrders.show.consumables.addBtn') }}
                                </button>
                                <button @click="resetConsumableForm(); showConsumableForm = false" class="btn btn-ghost btn-sm text-xs">{{ t('workOrders.form.cancel') }}</button>
                            </div>
                        </div>
                    </div>

                    <!-- List -->
                    <div class="overflow-x-auto" v-if="order.consumables?.length">
                        <table class="w-full whitespace-nowrap text-sm">
                            <thead><tr class="border-b border-border">
                                <th class="pb-2 text-left text-text-2 font-medium">{{ t('workOrders.show.consumables.name') }}</th>
                                <th class="pb-2 text-center text-text-2 font-medium w-16">{{ t('workOrders.show.consumables.qty') }}</th>
                                <th class="pb-2 text-center text-text-2 font-medium w-16">{{ t('workOrders.show.consumables.unit') }}</th>
                                <th class="pb-2 text-right text-text-2 font-medium w-24">{{ t('workOrders.show.consumables.unitCost') }}</th>
                                <th class="pb-2 text-right text-text-2 font-medium w-20">{{ t('workOrders.show.consumables.total') }}</th>
                                <th class="pb-2 w-8"></th>
                            </tr></thead>
                            <tbody class="divide-y divide-border">
                                <tr v-for="c in order.consumables" :key="c.id">
                                    <td class="py-2 text-text">
                                        <div class="flex items-center gap-1.5">
                                            <span>{{ c.name }}</span>
                                            <span v-if="c.product_id" class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-600">Catálogo</span>
                                        </div>
                                        <span v-if="c.notes" class="block text-xs text-text-3">{{ c.notes }}</span>
                                    </td>
                                    <td class="py-2 text-center font-mono text-text-2">{{ c.qty_used }}</td>
                                    <td class="py-2 text-center text-text-3 text-xs">{{ c.unit }}</td>
                                    <td class="py-2 text-right font-mono text-text-2">${{ Number(c.unit_cost).toFixed(2) }}</td>
                                    <td class="py-2 text-right font-mono text-text">${{ Number(c.total_cost).toFixed(2) }}</td>
                                    <td class="py-2 text-right">
                                        <button @click="removeConsumable(c.id)" class="text-red-400 hover:text-red-600 text-xs p-1">✕</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <p v-else-if="!showConsumableForm" class="text-sm text-text-3 italic">{{ t('workOrders.show.consumables.empty') }}</p>
                </div>

                <!-- Tire Disposal -->
                <div class="card">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="section-title">{{ t('workOrders.show.disposal.title') }}</h3>
                        <button @click="showDisposalForm = !showDisposalForm" class="btn btn-ghost btn-sm text-xs">
                            {{ t('workOrders.show.disposal.add') }}
                        </button>
                    </div>

                    <!-- Add form -->
                    <div v-if="showDisposalForm" class="mb-4 p-3 bg-slate-50 rounded-lg border border-border space-y-3">
                        <div v-if="disposalCostUnit > 0" class="text-xs text-text-3">
                            {{ t('workOrders.show.disposal.costUnit') }}: <strong class="font-mono">${{ disposalCostUnit.toFixed(2) }}</strong>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="label">{{ t('workOrders.show.disposal.qtyTotal') }}</label>
                                <input v-model="disposalForm.qty_total" @input="updateDisposalPreview" type="number" min="1" class="input w-full" />
                                <p v-if="disposalForm.errors.qty_total" class="text-red-500 text-xs mt-1">{{ disposalForm.errors.qty_total }}</p>
                            </div>
                            <div>
                                <label class="label">{{ t('workOrders.show.disposal.kept') }}</label>
                                <input v-model="disposalForm.qty_client_kept" @input="updateDisposalPreview" type="number" min="0" :max="disposalForm.qty_total" class="input w-full" />
                                <p v-if="disposalForm.errors.qty_client_kept" class="text-red-500 text-xs mt-1">{{ disposalForm.errors.qty_client_kept }}</p>
                                <p v-if="parseInt(disposalForm.qty_client_kept) > parseInt(disposalForm.qty_total)" class="text-red-500 text-xs mt-1">No puede ser mayor que el total.</p>
                            </div>
                            <div class="col-span-2">
                                <label class="label">{{ t('workOrders.show.disposal.notes') }}</label>
                                <input v-model="disposalForm.notes" type="text" class="input w-full" />
                            </div>
                        </div>
                        <div class="text-sm text-text-2 bg-white rounded border border-border px-3 py-2">
                            {{ t('workOrders.show.disposal.forDisposal') }}: <strong class="font-mono">{{ disposalPreview }}</strong>
                            <span v-if="disposalCostUnit > 0" class="ml-2 text-text-3">
                                · {{ t('workOrders.show.disposal.totalCost') }}: <strong class="font-mono">${{ (disposalPreview * disposalCostUnit).toFixed(2) }}</strong>
                            </span>
                            <span v-if="disposalPreview === 0" class="ml-2 text-amber-600 text-xs font-medium">(Cliente se lleva todas las llantas — no se generará registro)</span>
                        </div>
                        <div class="flex gap-2">
                            <button @click="submitDisposal" :disabled="disposalForm.processing || parseInt(disposalForm.qty_client_kept) > parseInt(disposalForm.qty_total)" class="btn btn-primary btn-sm text-xs disabled:opacity-50">
                                {{ disposalForm.processing ? '…' : t('workOrders.show.disposal.addBtn') }}
                            </button>
                            <button @click="resetDisposalForm(); showDisposalForm = false" class="btn btn-ghost btn-sm text-xs">{{ t('workOrders.form.cancel') }}</button>
                        </div>
                    </div>

                    <!-- List -->
                    <div class="overflow-x-auto" v-if="(order.tire_disposals ?? order.tireDisposals)?.length">
                        <table class="w-full whitespace-nowrap text-sm">
                            <thead><tr class="border-b border-border">
                                <th class="pb-2 text-center text-text-2 font-medium w-16">{{ t('workOrders.show.disposal.qtyTotal') }}</th>
                                <th class="pb-2 text-center text-text-2 font-medium w-16">{{ t('workOrders.show.disposal.kept') }}</th>
                                <th class="pb-2 text-center text-text-2 font-medium w-16">{{ t('workOrders.show.disposal.forDisposal') }}</th>
                                <th class="pb-2 text-right text-text-2 font-medium w-24">{{ t('workOrders.show.disposal.totalCost') }}</th>
                                <th class="pb-2 text-left text-text-2 font-medium">{{ t('workOrders.show.disposal.status') }}</th>
                                <th class="pb-2 w-8"></th>
                            </tr></thead>
                            <tbody class="divide-y divide-border">
                                <tr v-for="d in (order.tire_disposals ?? order.tireDisposals)" :key="d.id">
                                    <td class="py-2 text-center font-mono text-text-2">{{ d.qty_total }}</td>
                                    <td class="py-2 text-center font-mono text-text-2">{{ d.qty_client_kept }}</td>
                                    <td class="py-2 text-center font-mono text-text font-semibold">{{ d.qty_for_disposal }}</td>
                                    <td class="py-2 text-right font-mono text-text">${{ (d.qty_for_disposal * d.cost_per_unit).toFixed(2) }}</td>
                                    <td class="py-2">
                                        <span :class="['badge', disposalStatusColors[d.status] ?? 'badge-gray']">
                                            {{ t('workOrders.show.disposal.statuses.' + d.status) }}
                                        </span>
                                    </td>
                                    <td class="py-2 text-right">
                                        <button @click="removeDisposal(d.id)" class="text-red-400 hover:text-red-600 text-xs p-1">✕</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <p v-else-if="!showDisposalForm" class="text-sm text-text-3 italic">{{ t('workOrders.show.disposal.empty') }}</p>
                </div>

                <!-- Notes -->
                <div v-if="order.notes || order.internal_notes" class="card grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div v-if="order.notes">
                        <p class="label">{{ t('workOrders.form.notes') }}</p>
                        <p class="text-sm text-text whitespace-pre-line">{{ order.notes }}</p>
                    </div>
                    <div v-if="order.internal_notes">
                        <p class="label">{{ t('workOrders.form.internalNotes') }}</p>
                        <p class="text-sm text-text whitespace-pre-line">{{ order.internal_notes }}</p>
                    </div>
                </div>
            </div>

            <!-- Right: sidebar -->
            <div class="space-y-5">
                <!-- Totals -->
                <div class="card space-y-2">
                    <h3 class="section-title">{{ t('workOrders.show.summary') }}</h3>
                    <div class="flex justify-between text-sm">
                        <span class="text-text-2">{{ t('workOrders.form.subtotal') }}</span>
                        <span class="font-mono">${{ order.subtotal }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-text-2">{{ t('workOrders.form.tax') }}</span>
                        <span class="font-mono">${{ order.tax }}</span>
                    </div>
                    <div class="flex justify-between font-bold text-text border-t border-border pt-2">
                        <span>{{ t('workOrders.form.total') }}</span>
                        <span class="font-mono text-lg">${{ order.total }}</span>
                    </div>
                    <div v-if="order.invoiced" class="flex items-center gap-1 text-green-600 text-xs pt-1">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        {{ t('workOrders.show.invoiced') }}
                    </div>
                </div>

                <!-- Operational costs / margin -->
                <div v-if="order.margin_amount !== null" class="card space-y-2">
                    <h3 class="section-title">{{ t('workOrders.show.costs.title') }}</h3>
                    <div v-if="order.cost_services > 0" class="flex justify-between text-xs">
                        <span class="text-text-3">{{ t('workOrders.show.costs.services') }}</span>
                        <span class="font-mono text-text-2">${{ Number(order.cost_services).toFixed(2) }}</span>
                    </div>
                    <div v-if="order.cost_products > 0" class="flex justify-between text-xs">
                        <span class="text-text-3">{{ t('workOrders.show.costs.products') }}</span>
                        <span class="font-mono text-text-2">${{ Number(order.cost_products).toFixed(2) }}</span>
                    </div>
                    <div v-if="order.cost_consumables > 0" class="flex justify-between text-xs">
                        <span class="text-text-3">{{ t('workOrders.show.costs.consumables') }}</span>
                        <span class="font-mono text-text-2">${{ Number(order.cost_consumables).toFixed(2) }}</span>
                    </div>
                    <div v-if="order.cost_disposal > 0" class="flex justify-between text-xs">
                        <span class="text-text-3">{{ t('workOrders.show.costs.disposal') }}</span>
                        <span class="font-mono text-text-2">${{ Number(order.cost_disposal).toFixed(2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm border-t border-border pt-2">
                        <span class="text-text-2 font-medium">{{ t('workOrders.show.costs.totalCost') }}</span>
                        <span class="font-mono font-semibold text-text">
                            ${{ (Number(order.cost_services) + Number(order.cost_products) + Number(order.cost_consumables) + Number(order.cost_disposal)).toFixed(2) }}
                        </span>
                    </div>
                    <div class="flex justify-between text-sm pt-1">
                        <span class="text-text-2">{{ t('workOrders.show.costs.marginAmt') }}</span>
                        <span :class="['font-mono font-bold', Number(order.margin_amount) >= 0 ? 'text-green-600' : 'text-red-500']">
                            ${{ Number(order.margin_amount).toFixed(2) }}
                        </span>
                    </div>
                    <div v-if="order.margin_pct !== null" class="flex justify-between text-xs">
                        <span class="text-text-3">{{ t('workOrders.show.costs.marginPct') }}</span>
                        <span :class="['font-mono', Number(order.margin_pct) >= 0 ? 'text-green-600' : 'text-red-500']">
                            {{ Number(order.margin_pct).toFixed(1) }}%
                        </span>
                    </div>
                </div>

                <!-- Status -->
                <div class="card space-y-2">
                    <h3 class="section-title">{{ t('common.status') }}</h3>
                    <div class="flex flex-col gap-1">
                        <button
                            v-for="s in statuses" :key="s"
                            @click="changeStatus(s)"
                            :disabled="order.status === s || (order.invoiced && (s === 'pending' || s === 'cancelled'))"
                            :title="order.invoiced && (s === 'pending' || s === 'cancelled') ? 'No disponible: la orden ya tiene una factura emitida' : undefined"
                            :class="[
                                'w-full text-left px-3 py-2 rounded-lg text-sm transition-colors capitalize',
                                order.status === s
                                    ? 'bg-brand-50 text-brand-700 font-semibold cursor-default'
                                    : (order.invoiced && (s === 'pending' || s === 'cancelled'))
                                        ? 'text-text-3 cursor-not-allowed opacity-40'
                                        : 'text-text-2 hover:bg-slate-50 border border-transparent hover:border-border'
                            ]"
                        >{{ t('workOrders.statuses.' + s) }}</button>
                    </div>
                </div>

                <!-- Assignment -->
                <div class="card space-y-1">
                    <h3 class="section-title">{{ t('workOrders.form.assignedTo') }}</h3>
                    <p class="text-sm text-text">{{ order.assignedTo?.name ?? order.assigned_to?.name ?? t('workOrders.show.unassigned') }}</p>
                    <p v-if="order.promised_at" class="text-xs text-text-2">
                        {{ t('workOrders.form.promisedAt') }}: {{ new Date(order.promised_at).toLocaleString() }}
                    </p>
                    <p v-if="order.started_at" class="text-xs text-text-2">
                        {{ t('workOrders.show.started') }}: {{ new Date(order.started_at).toLocaleString() }}
                    </p>
                    <p v-if="order.completed_at" class="text-xs text-text-2">
                        {{ t('workOrders.show.completed') }}: {{ new Date(order.completed_at).toLocaleString() }}
                    </p>
                </div>

                <!-- Linked invoices -->
                <div class="card" v-if="order.invoices?.length">
                    <h3 class="section-title mb-2">{{ t('workOrders.show.invoices') }}</h3>
                    <div v-for="inv in order.invoices" :key="inv.id" class="flex items-center justify-between text-sm border-b border-border py-1 last:border-0">
                        <Link :href="`/invoices/${inv.id}`" class="text-brand-600 hover:underline font-mono">{{ inv.number }}</Link>
                        <span :class="['badge', { paid: 'badge-green', partial: 'badge-amber', draft: 'badge-gray', void: 'badge-red' }[inv.status] ?? 'badge-gray']">{{ t('invoices.statuses.' + inv.status) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
