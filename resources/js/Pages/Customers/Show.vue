<script setup>
import { confirmDelete } from '@/utils/alerts';


import Icon from '@/Components/Icon.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';
import { usePermissions } from '@/composables/usePermissions.js';

const { t } = useI18n();
const { can } = usePermissions();

const props = defineProps({
    customer:   Object,
    workOrders: Array,
    invoices:   Array,
});

const activeTab = ref('vehicles');

const statusColors = {
    paid: 'badge-green', partial: 'badge-amber', draft: 'badge-gray', void: 'badge-red',
    open: 'badge-blue', in_progress: 'badge-amber', completed: 'badge-green', cancelled: 'badge-red',
};

const vehicleTypeLabel = {
    sedan: 'Sedan', suv: 'SUV', pickup: 'Pickup / Truck',
    van: 'Van', truck: 'Commercial Truck', other: 'Other',
};

async function deleteVehicle(vehicle) {
    if (await confirmDelete(`Remove ${vehicle.year} ${vehicle.make} ${vehicle.model}?`)) {
        router.delete(`/customers/${props.customer.id}/vehicles/${vehicle.id}`);
    }
}
</script>

<template>
    <AppLayout>
        <template #header>
            <div class="flex items-center gap-2 text-sm">
                <Link href="/customers" class="text-slate-400 hover:text-slate-700">{{ t('customers.title') }}</Link>
                <span class="text-slate-300">/</span>
                <span class="font-semibold text-slate-800">{{ customer.name }}</span>
            </div>
        </template>
        <template #header-actions>
            <Link v-if="can('manage_customers')" :href="`/customers/${customer.id}/edit`"
                class="inline-flex items-center gap-1.5 rounded-lg border px-3 py-1.5 text-sm font-medium text-slate-700 hover:bg-slate-50 transition"
                style="border-color:var(--color-border);">
                <Icon name="edit" />
                {{ t('common.edit') }}
            </Link>
        </template>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

            <!-- Customer info -->
            <div class="rounded-xl p-5 shadow-sm" style="background:var(--color-surface);border:1px solid var(--color-border);">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-brand-600 flex items-center justify-center text-white font-bold text-sm shrink-0">
                        {{ customer.name.charAt(0).toUpperCase() }}
                    </div>
                    <div>
                        <h2 class="font-semibold text-slate-900">{{ customer.name }}</h2>
                        <p class="text-xs text-slate-400">Customer #{{ customer.id }}</p>
                    </div>
                </div>

                <dl class="space-y-2.5">
                    <div v-if="customer.phone">
                        <dt class="text-xs text-slate-400 uppercase tracking-wide font-medium">{{ t('customers.phone') }}</dt>
                        <dd class="text-sm text-slate-700 mt-0.5">{{ customer.phone }}</dd>
                    </div>
                    <div v-if="customer.email">
                        <dt class="text-xs text-slate-400 uppercase tracking-wide font-medium">{{ t('customers.email') }}</dt>
                        <dd class="text-sm text-slate-700 mt-0.5">{{ customer.email }}</dd>
                    </div>
                    <div v-if="customer.address">
                        <dt class="text-xs text-slate-400 uppercase tracking-wide font-medium">{{ t('customers.address') }}</dt>
                        <dd class="text-sm text-slate-700 mt-0.5">{{ customer.address }}</dd>
                    </div>
                    <div v-if="customer.notes">
                        <dt class="text-xs text-slate-400 uppercase tracking-wide font-medium">{{ t('common.notes') }}</dt>
                        <dd class="text-sm text-slate-600 mt-0.5">{{ customer.notes }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Right panel: tabs -->
            <div class="lg:col-span-2">
                <!-- Tab bar -->
                <div class="flex gap-1 border-b mb-4" style="border-color:var(--color-border);">
                    <button v-for="tab in [
                        { key:'vehicles',    label:`Vehicles (${customer.vehicles.length})` },
                        { key:'workOrders',  label:`Work Orders (${workOrders.length})` },
                        { key:'invoices',    label:`Invoices (${invoices.length})` },
                    ]" :key="tab.key"
                        @click="activeTab = tab.key"
                        :class="['px-4 py-2 text-sm font-medium border-b-2 -mb-px transition-colors',
                            activeTab === tab.key
                                ? 'border-brand-600 text-brand-600'
                                : 'border-transparent text-slate-500 hover:text-slate-700']"
                    >{{ tab.label }}</button>
                </div>

                <!-- VEHICLES tab -->
                <div v-show="activeTab === 'vehicles'">
                    <div class="flex justify-end mb-3">
                        <Link v-if="can('manage_customers')"
                            :href="`/customers/${customer.id}/vehicles/create`"
                            class="inline-flex items-center gap-1 rounded-lg bg-brand-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-brand-700 transition"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Add Vehicle
                        </Link>
                    </div>

                    <div v-if="customer.vehicles.length === 0"
                        class="rounded-xl p-8 text-center shadow-sm"
                        style="background:var(--color-surface);border:1px solid var(--color-border);">
                        <div class="text-3xl mb-2">🚗</div>
                        <p class="text-sm text-slate-500 mb-3">No vehicles registered yet.</p>
                        <Link :href="`/customers/${customer.id}/vehicles/create`" class="text-sm text-brand-600 hover:underline">
                            Add first vehicle →
                        </Link>
                    </div>

                    <div v-else class="space-y-3">
                        <div v-for="v in customer.vehicles" :key="v.id"
                            class="rounded-xl p-4 shadow-sm"
                            style="background:var(--color-surface);border:1px solid var(--color-border);">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-lg bg-slate-100 flex items-center justify-center text-lg shrink-0">
                                        {{ v.type === 'sedan' ? '🚗' : v.type === 'suv' ? '🚙' : v.type === 'pickup' ? '🛻' : v.type === 'van' ? '🚐' : '🚛' }}
                                    </div>
                                    <div>
                                        <Link :href="`/customers/${customer.id}/vehicles/${v.id}`"
                                            class="font-semibold text-sm hover:text-brand-600 hover:underline"
                                            style="color:var(--color-text)">
                                            {{ v.year }} {{ v.make }} {{ v.model }}
                                        </Link>
                                        <p class="text-xs text-slate-400 mt-0.5">
                                            {{ vehicleTypeLabel[v.type] }}<span v-if="v.color"> · {{ v.color }}</span>
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <Link v-if="can('manage_customers')" :href="`/customers/${customer.id}/vehicles/${v.id}/edit`" class="btn btn-ghost btn-sm">
                                        <Icon name="edit" />{{ t('common.edit') }}
                                    </Link>
                                    <button v-if="can('manage_customers')" @click="deleteVehicle(v)" class="btn btn-ghost btn-sm text-red-500 hover:bg-red-50 hover:border-red-200">
                                        <Icon name="delete" />{{ t('common.remove') }}
                                    </button>
                                </div>
                            </div>
                            <div class="mt-3 flex flex-wrap gap-2">
                                <span v-if="v.plate" class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium bg-slate-100 text-slate-600">🪪 {{ v.plate }}</span>
                                <span v-if="v.tire_size" class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium bg-brand-50 text-brand-700">🔵 {{ v.tire_size }}</span>
                                <span v-if="v.mileage" class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium bg-slate-100 text-slate-500">{{ v.mileage.toLocaleString() }} mi</span>
                                <span v-if="v.vin" class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-mono text-slate-400">VIN: {{ v.vin }}</span>
                            </div>
                            <p v-if="v.notes" class="mt-2 text-xs text-slate-400 italic">{{ v.notes }}</p>
                        </div>
                    </div>
                </div>

                <!-- WORK ORDERS tab -->
                <div v-show="activeTab === 'workOrders'">
                    <div v-if="workOrders.length === 0" class="rounded-xl p-8 text-center shadow-sm" style="background:var(--color-surface);border:1px solid var(--color-border);">
                        <p class="text-sm text-slate-500">No work orders yet.</p>
                    </div>
                    <div v-else class="card overflow-x-auto p-0">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b" style="border-color:var(--color-border);">
                                    <th class="text-left px-4 py-2.5 text-xs text-text-2 font-medium">Number</th>
                                    <th class="text-left px-4 py-2.5 text-xs text-text-2 font-medium">Vehicle</th>
                                    <th class="text-left px-4 py-2.5 text-xs text-text-2 font-medium">Status</th>
                                    <th class="text-right px-4 py-2.5 text-xs text-text-2 font-medium">Total</th>
                                    <th class="text-left px-4 py-2.5 text-xs text-text-2 font-medium">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="wo in workOrders" :key="wo.id" class="border-b hover:bg-slate-50 transition-colors" style="border-color:var(--color-border);">
                                    <td class="px-4 py-2.5">
                                        <Link :href="`/work-orders/${wo.id}`" class="font-mono text-brand-600 hover:underline text-xs">{{ wo.number }}</Link>
                                    </td>
                                    <td class="px-4 py-2.5 text-text-2 text-xs">
                                        {{ wo.vehicle ? `${wo.vehicle.year} ${wo.vehicle.make} ${wo.vehicle.model}` : '—' }}
                                    </td>
                                    <td class="px-4 py-2.5">
                                        <span :class="['badge text-xs', statusColors[wo.status] ?? 'badge-gray']">{{ wo.status }}</span>
                                    </td>
                                    <td class="px-4 py-2.5 text-right font-medium">${{ parseFloat(wo.total ?? 0).toFixed(2) }}</td>
                                    <td class="px-4 py-2.5 text-text-2 text-xs">{{ new Date(wo.created_at).toLocaleDateString() }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- INVOICES tab -->
                <div v-show="activeTab === 'invoices'">
                    <div v-if="invoices.length === 0" class="rounded-xl p-8 text-center shadow-sm" style="background:var(--color-surface);border:1px solid var(--color-border);">
                        <p class="text-sm text-slate-500">No invoices yet.</p>
                    </div>
                    <div v-else class="card overflow-x-auto p-0">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b" style="border-color:var(--color-border);">
                                    <th class="text-left px-4 py-2.5 text-xs text-text-2 font-medium">Invoice</th>
                                    <th class="text-left px-4 py-2.5 text-xs text-text-2 font-medium">Status</th>
                                    <th class="text-right px-4 py-2.5 text-xs text-text-2 font-medium">Total</th>
                                    <th class="text-right px-4 py-2.5 text-xs text-text-2 font-medium">Paid</th>
                                    <th class="text-left px-4 py-2.5 text-xs text-text-2 font-medium">Date</th>
                                    <th class="px-4 py-2.5"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="inv in invoices" :key="inv.id" class="border-b hover:bg-slate-50 transition-colors" style="border-color:var(--color-border);">
                                    <td class="px-4 py-2.5">
                                        <Link :href="`/invoices/${inv.id}`" class="font-mono text-brand-600 hover:underline text-xs">{{ inv.number }}</Link>
                                    </td>
                                    <td class="px-4 py-2.5">
                                        <span :class="['badge text-xs', statusColors[inv.status] ?? 'badge-gray']">{{ inv.status }}</span>
                                    </td>
                                    <td class="px-4 py-2.5 text-right font-medium">${{ parseFloat(inv.total ?? 0).toFixed(2) }}</td>
                                    <td class="px-4 py-2.5 text-right text-text-2">${{ parseFloat(inv.amount_paid ?? 0).toFixed(2) }}</td>
                                    <td class="px-4 py-2.5 text-text-2 text-xs">{{ new Date(inv.created_at).toLocaleDateString() }}</td>
                                    <td class="px-4 py-2.5">
                                        <a :href="`/invoices/${inv.id}/pdf`" target="_blank" class="text-xs text-slate-400 hover:text-brand-600">PDF</a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
