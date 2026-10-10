<script setup>
import { ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';

const { t } = useI18n();

const props = defineProps({
    expense:        Object,
    categories:     Array,
    paymentMethods: Object,
});

const form = useForm({
    category_id:    props.expense?.category_id ?? null,
    description:    props.expense?.description ?? '',
    amount:         props.expense?.amount ?? '',
    payment_method: props.expense?.payment_method ?? 'cash',
    vendor:         props.expense?.vendor ?? '',
    reference:      props.expense?.reference ?? '',
    is_recurring:   props.expense?.is_recurring ?? false,
    recurrence:     props.expense?.recurrence ?? null,
    paid_at:        props.expense?.paid_at ?? new Date().toISOString().slice(0, 10),
    notes:          props.expense?.notes ?? '',
    evidence:       null,
});

const evidencePreview = ref(null);

function onFileChange(e) {
    const file = e.target.files[0];
    if (!file) return;
    form.evidence = file;
    if (file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = (ev) => { evidencePreview.value = ev.target.result; };
        reader.readAsDataURL(file);
    } else {
        evidencePreview.value = null;
    }
}

function submit() {
    if (props.expense) {
        form.put(`/expenses/${props.expense.id}`, { forceFormData: true });
    } else {
        form.post('/expenses', { forceFormData: true });
    }
}
</script>

<template>
    <AppLayout>
        <template #header>
            <div class="page-header">
                <h1 class="page-title">{{ expense ? t('expenses.form.editTitle') : t('expenses.form.newTitle') }}</h1>
            </div>
        </template>

        <form @submit.prevent="submit" class="max-w-2xl space-y-5">
            <div class="card space-y-4">
                <h2 class="section-title">{{ t('expenses.title') }}</h2>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="label">{{ t('expenses.form.paidAt') }} <span class="text-red-500">*</span></label>
                        <input v-model="form.paid_at" type="date" class="input w-full" />
                        <p v-if="form.errors.paid_at" class="field-error">{{ form.errors.paid_at }}</p>
                    </div>
                    <div>
                        <label class="label">{{ t('expenses.form.amount') }} <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-text-3">$</span>
                            <input v-model="form.amount" type="number" step="0.01" min="0.01" class="input input-currency w-full" placeholder="0.00" />
                        </div>
                        <p v-if="form.errors.amount" class="field-error">{{ form.errors.amount }}</p>
                    </div>
                </div>

                <div>
                    <label class="label">{{ t('expenses.form.description') }} <span class="text-red-500">*</span></label>
                    <input v-model="form.description" type="text" class="input w-full" placeholder="What was this expense for?" />
                    <p v-if="form.errors.description" class="field-error">{{ form.errors.description }}</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="label">{{ t('expenses.form.category') }}</label>
                        <select v-model="form.category_id" class="input w-full">
                            <option :value="null">— No Category —</option>
                            <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }} ({{ c.type }})</option>
                        </select>
                    </div>
                    <div>
                        <label class="label">{{ t('expenses.form.paymentMethod') }}</label>
                        <select v-model="form.payment_method" class="input w-full">
                            <option v-for="(label, key) in paymentMethods" :key="key" :value="key">{{ label }}</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="label">{{ t('expenses.form.vendor') }}</label>
                        <input v-model="form.vendor" type="text" class="input w-full" placeholder="Company or person paid" />
                    </div>
                    <div>
                        <label class="label">{{ t('expenses.form.reference') }}</label>
                        <input v-model="form.reference" type="text" class="input w-full" placeholder="Invoice or receipt #" />
                    </div>
                </div>
            </div>

            <!-- Recurring -->
            <div class="card space-y-3">
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="recurring" v-model="form.is_recurring" class="w-4 h-4 rounded text-brand-600" />
                    <label for="recurring" class="label cursor-pointer">{{ t('expenses.form.recurring') }}</label>
                </div>
                <div v-if="form.is_recurring">
                    <label class="label">{{ t('expenses.form.recurrence') }}</label>
                    <select v-model="form.recurrence" class="input w-48">
                        <option value="weekly">Weekly</option>
                        <option value="biweekly">Bi-weekly</option>
                        <option value="monthly">Monthly</option>
                        <option value="yearly">Yearly</option>
                    </select>
                </div>
            </div>

            <!-- Evidence -->
            <div class="card space-y-3">
                <h2 class="section-title">{{ t('expenses.form.evidence') }}</h2>
                <div v-if="expense?.evidence" class="text-sm text-text-2">
                    Current: <a :href="`/storage/${expense.evidence}`" target="_blank" class="text-brand-600 hover:underline">View file</a>
                </div>
                <input type="file" @change="onFileChange" accept="image/*,.pdf" class="text-sm text-text-2 file:btn file:btn-ghost file:btn-sm file:mr-2" />
                <img v-if="evidencePreview" :src="evidencePreview" class="max-h-40 rounded-lg border border-border object-contain" />
                <p v-if="form.errors.evidence" class="field-error">{{ form.errors.evidence }}</p>
            </div>

            <!-- Notes -->
            <div class="card">
                <label class="label">{{ t('expenses.form.notes') }}</label>
                <textarea v-model="form.notes" rows="2" class="input w-full" placeholder="Additional notes…" />
            </div>

            <div class="flex gap-3">
                <button type="submit" :disabled="form.processing" class="btn btn-primary">
                    {{ form.processing ? t('common.saving') : t('expenses.form.save') }}
                </button>
                <a href="/expenses" class="btn btn-ghost">{{ t('expenses.form.cancel') }}</a>
            </div>
        </form>
    </AppLayout>
</template>
