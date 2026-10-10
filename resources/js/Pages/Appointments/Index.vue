<script setup>
import { confirmDelete } from '@/utils/alerts';

import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { usePermissions } from '@/composables/usePermissions.js';

const { can } = usePermissions();

const props = defineProps({
    appointments: Object,
    employees:    Array,
    filters:      Object,
});

const status   = ref(props.filters.status ?? '');
const from     = ref(props.filters.from ?? '');
const to       = ref(props.filters.to ?? '');
const q        = ref(props.filters.q ?? '');

function search() {
    router.get('/appointments', {
        status: status.value,
        from: from.value,
        to: to.value,
        q: q.value,
    }, { preserveState: true, replace: true });
}

async function destroy(id) {
    if (await confirmDelete('Remove this appointment?')) {
        router.delete(`/appointments/${id}`);
    }
}

const statusColors = {
    scheduled:   'badge-gray',
    confirmed:   'badge-blue',
    in_progress: 'badge-amber',
    completed:   'badge-green',
    cancelled:   'badge-red',
};

const serviceLabels = {
    tire_change: 'Tire Change',
    rotation:    'Rotation',
    alignment:   'Alignment',
    inspection:  'Inspection',
    repair:      'Repair',
    other:       'Other',
};

function formatDate(dt) {
    if (!dt) return '—';
    return new Date(dt).toLocaleString('en-US', { month: 'short', day: 'numeric', hour: 'numeric', minute: '2-digit' });
}
</script>

<template>
    <AppLayout>
        <template #header>
            <div class="page-header">
                <div>
                    <h1 class="page-title">Appointments</h1>
                    <p class="page-subtitle">{{ appointments.total }} appointments</p>
                </div>
                <Link v-if="can('manage_work_orders')" href="/appointments/create" class="btn btn-primary">
                    + New Appointment
                </Link>
            </div>
        </template>

        <!-- Filters -->
        <div class="card mb-4 flex flex-wrap gap-3 items-end">
            <div>
                <label class="label">Search</label>
                <input v-model="q" @input="search" type="search" placeholder="Name or #APT…" class="input w-52" />
            </div>
            <div>
                <label class="label">Status</label>
                <select v-model="status" @change="search" class="input w-40">
                    <option value="">All statuses</option>
                    <option value="scheduled">Scheduled</option>
                    <option value="confirmed">Confirmed</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
            <div>
                <label class="label">From</label>
                <input v-model="from" @change="search" type="date" class="input" />
            </div>
            <div>
                <label class="label">To</label>
                <input v-model="to" @change="search" type="date" class="input" />
            </div>
        </div>

        <div class="card p-0 overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-border">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-text-2">#</th>
                        <th class="px-4 py-3 text-left font-medium text-text-2">Customer</th>
                        <th class="px-4 py-3 text-left font-medium text-text-2">Service</th>
                        <th class="px-4 py-3 text-left font-medium text-text-2">Scheduled</th>
                        <th class="px-4 py-3 text-left font-medium text-text-2">Assigned</th>
                        <th class="px-4 py-3 text-left font-medium text-text-2">Status</th>
                        <th class="px-4 py-3 text-right font-medium text-text-2">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    <tr v-for="apt in appointments.data" :key="apt.id" class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3 font-mono text-xs text-brand-700 font-semibold">{{ apt.number }}</td>
                        <td class="px-4 py-3">
                            <p class="font-medium text-text">{{ apt.customer?.name ?? 'Walk-in' }}</p>
                            <p v-if="apt.vehicle" class="text-xs text-text-3">
                                {{ apt.vehicle.year }} {{ apt.vehicle.make }} {{ apt.vehicle.model }}
                                <span v-if="apt.vehicle.plate"> · {{ apt.vehicle.plate }}</span>
                            </p>
                        </td>
                        <td class="px-4 py-3 text-text-2">{{ serviceLabels[apt.service_type] ?? apt.service_type }}</td>
                        <td class="px-4 py-3 text-text-2 text-xs">
                            {{ formatDate(apt.scheduled_at) }}
                            <span class="text-text-3 block">{{ apt.duration_minutes }} min</span>
                        </td>
                        <td class="px-4 py-3 text-text-2 text-xs">{{ apt.assigned_to?.name ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <span :class="['badge', statusColors[apt.status] ?? 'badge-gray']">{{ apt.status.replace('_', ' ') }}</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <Link v-if="can('manage_work_orders')" :href="`/appointments/${apt.id}/edit`"
                                    class="btn btn-ghost btn-sm">Edit</Link>
                                <button v-if="can('manage_work_orders')" @click="destroy(apt.id)"
                                    class="btn btn-ghost btn-sm text-red-500 hover:text-red-700 hover:bg-red-50">
                                    Remove
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="!appointments.data.length">
                        <td colspan="7" class="px-4 py-10 text-center text-text-3">No appointments found.</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <Pagination :links="appointments.links" class="mt-4" />
    </AppLayout>
</template>
