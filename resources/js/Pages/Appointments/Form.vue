<script setup>
import { ref, watch, computed } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    appointment: Object,
    customers:   Array,
    employees:   Array,
    prefill:     Object,
});

const isEdit = computed(() => !!props.appointment);

const form = useForm({
    customer_id:      props.appointment?.customer_id  ?? props.prefill?.customer_id ?? null,
    vehicle_id:       props.appointment?.vehicle_id   ?? props.prefill?.vehicle_id  ?? null,
    assigned_to:      props.appointment?.assigned_to  ?? null,
    status:           props.appointment?.status       ?? 'scheduled',
    service_type:     props.appointment?.service_type ?? 'other',
    scheduled_at:     props.appointment?.scheduled_at
        ? props.appointment.scheduled_at.replace(' ', 'T').slice(0, 16)
        : '',
    duration_minutes: props.appointment?.duration_minutes ?? 60,
    notes:            props.appointment?.notes        ?? '',
});

// Vehicles are embedded in the customers array (each customer has .vehicles)
const vehicles = computed(() => {
    if (!form.customer_id) return [];
    const c = props.customers.find(c => c.id === parseInt(form.customer_id));
    return c?.vehicles ?? [];
});

watch(() => form.customer_id, () => {
    form.vehicle_id = null;
});

function submit() {
    if (isEdit.value) {
        form.put(`/appointments/${props.appointment.id}`);
    } else {
        form.post('/appointments');
    }
}

const serviceTypes = [
    { value: 'tire_change', label: 'Tire Change' },
    { value: 'rotation',    label: 'Rotation' },
    { value: 'alignment',   label: 'Alignment' },
    { value: 'inspection',  label: 'Inspection' },
    { value: 'repair',      label: 'Repair' },
    { value: 'other',       label: 'Other' },
];
</script>

<template>
    <AppLayout>
        <template #header>
            <div class="page-header">
                <div>
                    <h1 class="page-title">{{ isEdit ? `Edit Appointment ${appointment.number}` : 'New Appointment' }}</h1>
                    <p class="page-subtitle">{{ isEdit ? 'Update appointment details' : 'Schedule a customer appointment' }}</p>
                </div>
                <Link href="/appointments" class="btn btn-ghost">← Back</Link>
            </div>
        </template>

        <form @submit.prevent="submit" class="max-w-2xl space-y-5">

            <!-- Customer & Vehicle -->
            <div class="card space-y-4">
                <h2 class="section-title">Customer</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="label">Customer</label>
                        <select v-model="form.customer_id" class="input w-full">
                            <option :value="null">— Walk-in / no customer —</option>
                            <option v-for="c in customers" :key="c.id" :value="c.id">
                                {{ c.name }}<span v-if="c.phone"> · {{ c.phone }}</span>
                            </option>
                        </select>
                    </div>
                    <div>
                        <label class="label">Vehicle</label>
                        <select v-model="form.vehicle_id" :disabled="!form.customer_id" class="input w-full disabled:opacity-50">
                            <option :value="null">— No vehicle —</option>
                            <option v-for="v in vehicles" :key="v.id" :value="v.id">
                                {{ v.year }} {{ v.make }} {{ v.model }}
                                <span v-if="v.plate"> · {{ v.plate }}</span>
                            </option>
                        </select>
                        <p v-if="form.errors.vehicle_id" class="field-error">{{ form.errors.vehicle_id }}</p>
                    </div>
                </div>
            </div>

            <!-- Service Details -->
            <div class="card space-y-4">
                <h2 class="section-title">Service Details</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="label">Service Type <span class="text-red-500">*</span></label>
                        <select v-model="form.service_type" class="input w-full">
                            <option v-for="st in serviceTypes" :key="st.value" :value="st.value">{{ st.label }}</option>
                        </select>
                        <p v-if="form.errors.service_type" class="field-error">{{ form.errors.service_type }}</p>
                    </div>
                    <div>
                        <label class="label">Assigned To</label>
                        <select v-model="form.assigned_to" class="input w-full">
                            <option :value="null">— Unassigned —</option>
                            <option v-for="e in employees" :key="e.id" :value="e.id">{{ e.name }}</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="label">Date & Time <span class="text-red-500">*</span></label>
                        <input v-model="form.scheduled_at" type="datetime-local" class="input w-full" />
                        <p v-if="form.errors.scheduled_at" class="field-error">{{ form.errors.scheduled_at }}</p>
                    </div>
                    <div>
                        <label class="label">Duration (minutes) <span class="text-red-500">*</span></label>
                        <input v-model.number="form.duration_minutes" type="number" min="15" max="480" step="15" class="input w-full" />
                        <p v-if="form.errors.duration_minutes" class="field-error">{{ form.errors.duration_minutes }}</p>
                    </div>
                </div>
                <div v-if="isEdit">
                    <label class="label">Status</label>
                    <select v-model="form.status" class="input w-full md:w-48">
                        <option value="scheduled">Scheduled</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="in_progress">In Progress</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div>
                    <label class="label">Notes</label>
                    <textarea v-model="form.notes" rows="3" class="input w-full" placeholder="Special requests, reminders…"></textarea>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit" :disabled="form.processing" class="btn btn-primary">
                    {{ form.processing ? 'Saving…' : (isEdit ? 'Update Appointment' : 'Schedule Appointment') }}
                </button>
                <Link href="/appointments" class="btn btn-ghost">Cancel</Link>
            </div>
        </form>
    </AppLayout>
</template>
