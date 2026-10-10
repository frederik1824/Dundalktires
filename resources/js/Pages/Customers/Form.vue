<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const props = defineProps({ customer: Object });

const isEdit = !!props.customer;

const form = useForm({
    name:    props.customer?.name    ?? '',
    phone:   props.customer?.phone   ?? '',
    email:   props.customer?.email   ?? '',
    address: props.customer?.address ?? '',
    notes:   props.customer?.notes   ?? '',
});

function submit() {
    if (isEdit) {
        form.put(`/customers/${props.customer.id}`);
    } else {
        form.post('/customers');
    }
}
</script>

<template>
    <AppLayout>
        <template #header>
            <div class="flex items-center gap-2 text-sm">
                <Link href="/customers" class="text-slate-400 hover:text-slate-700">{{ t('customers.title') }}</Link>
                <span class="text-slate-300">/</span>
                <span class="font-semibold text-slate-800">{{ isEdit ? t('customers.form.editLabel') : t('customers.form.newTitle') }}</span>
            </div>
        </template>

        <div class="max-w-lg">
            <form @submit.prevent="submit" class="card space-y-4">

                <div>
                    <label class="label">{{ t('customers.form.fullName') }} <span class="text-red-500">*</span></label>
                    <input v-model="form.name" type="text" required placeholder="John Smith"
                        class="input" />
                    <p v-if="form.errors.name" class="mt-1 text-xs text-red-600">{{ form.errors.name }}</p>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="label">{{ t('customers.form.phone') }}</label>
                        <input v-model="form.phone" type="tel" placeholder="(410) 555-0100"
                            class="input" />
                    </div>
                    <div>
                        <label class="label">{{ t('customers.form.email') }}</label>
                        <input v-model="form.email" type="email" placeholder="john@email.com"
                            class="input" />
                    </div>
                </div>

                <div>
                    <label class="label">{{ t('customers.form.address') }}</label>
                    <input v-model="form.address" type="text" placeholder="123 Main St, Dundalk MD"
                        class="input" />
                </div>

                <div>
                    <label class="label">{{ t('customers.form.notes') }}</label>
                    <textarea v-model="form.notes" rows="3" placeholder="Any relevant notes about this customer..."
                        class="w-full rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-brand-500 resize-none" />
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" :disabled="form.processing"
                        class="btn btn-primary">
                        {{ form.processing ? t('customers.form.saving') : (isEdit ? t('customers.form.saveChanges') : t('customers.form.create')) }}
                    </button>
                    <Link :href="isEdit ? `/customers/${customer.id}` : '/customers'"
                        class="text-sm text-slate-500 hover:text-slate-700">
                        {{ t('customers.form.cancel') }}
                    </Link>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
