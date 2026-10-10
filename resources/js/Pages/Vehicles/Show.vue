<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { usePermissions } from '@/composables/usePermissions.js';

const { can } = usePermissions();

const props = defineProps({
    customer:       Object,
    vehicle:        Object,
    workOrders:     Array,
    serviceHistory: Array,
});

const activeTab = ref('history');

const statusColors = {
    open: 'badge-blue', in_progress: 'badge-amber',
    completed: 'badge-green', cancelled: 'badge-red',
};

const typeEmoji = {
    sedan: '🚗', suv: '🚙', pickup: '🛻', van: '🚐', truck: '🚛', other: '🚘',
};

const totalSpent = props.workOrders.reduce((s, wo) => s + parseFloat(wo.total ?? 0), 0);
</script>

<template>
    <AppLayout>
        <template #header>
            <div class="page-header">
                <div>
                    <nav class="breadcrumb mb-1">
                        <Link href="/customers" class="breadcrumb-link">Customers</Link>
                        <span class="breadcrumb-sep">/</span>
                        <Link :href="`/customers/${customer.id}`" class="breadcrumb-link">{{ customer.name }}</Link>
                        <span class="breadcrumb-sep">/</span>
                        <span class="breadcrumb-current">{{ vehicle.year }} {{ vehicle.make }} {{ vehicle.model }}</span>
                    </nav>
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">{{ typeEmoji[vehicle.type] ?? '🚘' }}</span>
                        <h1 class="page-title">{{ vehicle.year }} {{ vehicle.make }} {{ vehicle.model }}</h1>
                    </div>
                </div>
                <div class="flex gap-2">
                    <Link v-if="can('manage_customers')"
                        :href="`/customers/${customer.id}/vehicles/${vehicle.id}/edit`"
                        class="btn btn-ghost">Edit Vehicle</Link>
                </div>
            </div>
        </template>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-5">

            <!-- Vehicle info card -->
            <div class="space-y-4">
                <div class="card">
                    <h2 class="text-xs font-semibold uppercase tracking-wide text-text-3 mb-3">Details</h2>
                    <dl class="space-y-2.5 text-sm">
                        <div v-if="vehicle.plate">
                            <dt class="label">Plate</dt>
                            <dd class="font-mono font-medium">{{ vehicle.plate }}</dd>
                        </div>
                        <div v-if="vehicle.color">
                            <dt class="label">Color</dt>
                            <dd>{{ vehicle.color }}</dd>
                        </div>
                        <div v-if="vehicle.tire_size">
                            <dt class="label">Tire Size</dt>
                            <dd class="font-mono text-brand-600">{{ vehicle.tire_size }}</dd>
                        </div>
                        <div v-if="vehicle.mileage">
                            <dt class="label">Mileage</dt>
                            <dd>{{ vehicle.mileage.toLocaleString() }} mi</dd>
                        </div>
                        <div v-if="vehicle.vin">
                            <dt class="label">VIN</dt>
                            <dd class="font-mono text-xs break-all text-text-2">{{ vehicle.vin }}</dd>
                        </div>
                        <div v-if="vehicle.notes">
                            <dt class="label">Notes</dt>
                            <dd class="text-text-2 italic">{{ vehicle.notes }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Stats -->
                <div class="card space-y-3">
                    <h2 class="text-xs font-semibold uppercase tracking-wide text-text-3">Summary</h2>
                    <div>
                        <p class="text-2xl font-bold font-mono text-brand-600">{{ workOrders.length }}</p>
                        <p class="text-xs text-text-3">Work Orders</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold font-mono text-green-600">${{ totalSpent.toFixed(2) }}</p>
                        <p class="text-xs text-text-3">Total Billed</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold font-mono text-text">{{ serviceHistory.length }}</p>
                        <p class="text-xs text-text-3">Service Line Items</p>
                    </div>
                </div>
            </div>

            <!-- Main panel -->
            <div class="lg:col-span-3">
                <!-- Tabs -->
                <div class="flex gap-1 border-b mb-4" style="border-color:var(--color-border);">
                    <button v-for="tab in [
                        { key:'history',    label:`Service History (${serviceHistory.length})` },
                        { key:'workOrders', label:`Work Orders (${workOrders.length})` },
                    ]" :key="tab.key"
                        @click="activeTab = tab.key"
                        :class="['px-4 py-2 text-sm font-medium border-b-2 -mb-px transition-colors',
                            activeTab === tab.key
                                ? 'border-brand-600 text-brand-600'
                                : 'border-transparent text-slate-500 hover:text-slate-700']"
                    >{{ tab.label }}</button>
                </div>

                <!-- SERVICE HISTORY tab -->
                <div v-show="activeTab === 'history'">
                    <div v-if="serviceHistory.length === 0"
                        class="rounded-xl p-10 text-center"
                        style="background:var(--color-surface);border:1px solid var(--color-border);">
                        <p class="text-sm text-text-3">No service history yet.</p>
                    </div>
                    <div v-else class="card overflow-x-auto p-0">
                        <table class="w-full text-sm">
                            <thead>
                                <tr style="background:var(--color-surface-2, #f8fafc);border-bottom:1px solid var(--color-border);">
                                    <th class="text-left px-4 py-3 text-xs text-text-3 font-medium">Date</th>
                                    <th class="text-left px-4 py-3 text-xs text-text-3 font-medium">Item</th>
                                    <th class="text-left px-4 py-3 text-xs text-text-3 font-medium">Type</th>
                                    <th class="text-right px-4 py-3 text-xs text-text-3 font-medium">Qty</th>
                                    <th class="text-right px-4 py-3 text-xs text-text-3 font-medium">Price</th>
                                    <th class="text-left px-4 py-3 text-xs text-text-3 font-medium">Work Order</th>
                                    <th class="text-left px-4 py-3 text-xs text-text-3 font-medium">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, i) in serviceHistory" :key="i"
                                    class="border-b hover:bg-slate-50 transition-colors"
                                    style="border-color:var(--color-border);">
                                    <td class="px-4 py-2.5 text-text-2 text-xs">{{ item.date }}</td>
                                    <td class="px-4 py-2.5 font-medium text-text">{{ item.name }}</td>
                                    <td class="px-4 py-2.5">
                                        <span :class="['badge text-xs', item.type === 'service' ? 'badge-blue' : 'badge-gray']">
                                            {{ item.type }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2.5 text-right text-text-2">{{ item.qty }}</td>
                                    <td class="px-4 py-2.5 text-right font-mono">${{ item.price.toFixed(2) }}</td>
                                    <td class="px-4 py-2.5">
                                        <Link :href="`/work-orders/${item.wo_id}`"
                                            class="font-mono text-xs text-brand-600 hover:underline">
                                            {{ item.wo_number }}
                                        </Link>
                                    </td>
                                    <td class="px-4 py-2.5">
                                        <span :class="['badge text-xs', statusColors[item.status] ?? 'badge-gray']">
                                            {{ item.status.replace('_', ' ') }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- WORK ORDERS tab -->
                <div v-show="activeTab === 'workOrders'">
                    <div v-if="workOrders.length === 0"
                        class="rounded-xl p-10 text-center"
                        style="background:var(--color-surface);border:1px solid var(--color-border);">
                        <p class="text-sm text-text-3">No work orders yet.</p>
                    </div>
                    <div v-else class="space-y-3">
                        <div v-for="wo in workOrders" :key="wo.id"
                            class="card hover:shadow-md transition-shadow">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <Link :href="`/work-orders/${wo.id}`"
                                            class="font-mono font-semibold text-brand-600 hover:underline">
                                            {{ wo.number }}
                                        </Link>
                                        <span :class="['badge text-xs', statusColors[wo.status] ?? 'badge-gray']">
                                            {{ wo.status.replace('_', ' ') }}
                                        </span>
                                        <span v-if="wo.invoice_num" class="text-xs text-text-3">
                                            → <Link :href="`/invoices/${wo.invoice_id}`"
                                                class="font-mono text-text-2 hover:text-brand-600">
                                                {{ wo.invoice_num }}
                                            </Link>
                                        </span>
                                    </div>
                                    <p class="text-xs text-text-3">
                                        {{ new Date(wo.created_at).toLocaleDateString() }}
                                        <span v-if="wo.assigned_to"> · {{ wo.assigned_to }}</span>
                                    </p>
                                    <div class="mt-2 flex flex-wrap gap-1">
                                        <span v-for="s in wo.services" :key="s"
                                            class="inline-flex items-center rounded px-2 py-0.5 text-xs bg-blue-50 text-blue-700">
                                            {{ s }}
                                        </span>
                                        <span v-for="p in wo.products" :key="p"
                                            class="inline-flex items-center rounded px-2 py-0.5 text-xs bg-slate-100 text-slate-600">
                                            {{ p }}
                                        </span>
                                    </div>
                                </div>
                                <div class="text-right shrink-0">
                                    <p class="font-mono font-bold text-text">${{ parseFloat(wo.total ?? 0).toFixed(2) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
