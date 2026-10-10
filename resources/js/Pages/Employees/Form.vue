<script setup>
import { useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';

const { t } = useI18n();

const props = defineProps({
    employee: Object,
    roles:    Array,
});

const form = useForm({
    name:                  props.employee?.name ?? '',
    email:                 props.employee?.email ?? '',
    role:                  props.employee?.role ?? '',
    password:              '',
    password_confirmation: '',
    phone:                 props.employee?.phone ?? '',
});

function submit() {
    if (props.employee) {
        form.put(`/employees/${props.employee.id}`);
    } else {
        form.post('/employees');
    }
}
</script>

<template>
    <AppLayout>
        <template #header>
            <div class="page-header">
                <h1 class="page-title">{{ employee ? t('employees.form.editTitle') : t('employees.form.addTitle') }}</h1>
            </div>
        </template>

        <form @submit.prevent="submit" class="max-w-lg space-y-5">
            <div class="card space-y-4">
                <h2 class="section-title">{{ t('employees.form.personalInfo') }}</h2>

                <div>
                    <label class="label">{{ t('employees.form.fullName') }} <span class="text-red-500">*</span></label>
                    <input v-model="form.name" type="text" class="input w-full" placeholder="John Doe" />
                    <p v-if="form.errors.name" class="field-error">{{ form.errors.name }}</p>
                </div>

                <div>
                    <label class="label">{{ t('employees.form.email') }} <span class="text-red-500">*</span></label>
                    <input v-model="form.email" type="email" class="input w-full" placeholder="employee@dundalk.com" />
                    <p v-if="form.errors.email" class="field-error">{{ form.errors.email }}</p>
                </div>

                <div>
                    <label class="label">{{ t('employees.form.phone') }}</label>
                    <input v-model="form.phone" type="tel" class="input w-full" placeholder="+1 (555) 000-0000" />
                </div>

                <div>
                    <label class="label">{{ t('employees.form.role') }}</label>
                    <select v-model="form.role" class="input w-full">
                        <option value="">{{ t('employees.form.noRole') }}</option>
                        <option v-for="r in roles" :key="r" :value="r" class="capitalize">{{ r }}</option>
                    </select>
                    <p v-if="form.errors.role" class="field-error">{{ form.errors.role }}</p>
                </div>
            </div>

            <div class="card space-y-4">
                <h2 class="section-title">{{ employee ? t('employees.form.changePassword') : t('employees.form.password') }}</h2>
                <p v-if="employee" class="text-xs text-text-3">{{ t('employees.form.keepHint') }}</p>

                <div>
                    <label class="label">{{ t('employees.form.password') }} {{ !employee ? '*' : '' }}</label>
                    <input v-model="form.password" type="password" class="input w-full" :placeholder="t('employees.form.minChars')" />
                    <p v-if="form.errors.password" class="field-error">{{ form.errors.password }}</p>
                </div>

                <div>
                    <label class="label">{{ t('employees.form.confirmPwd') }}</label>
                    <input v-model="form.password_confirmation" type="password" class="input w-full" :placeholder="t('employees.form.repeatPwd')" />
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit" :disabled="form.processing" class="btn btn-primary">
                    {{ form.processing ? t('employees.form.saving') : (employee ? t('employees.form.saveChanges') : t('employees.form.create')) }}
                </button>
                <a href="/employees" class="btn btn-ghost">{{ t('employees.form.cancel') }}</a>
            </div>
        </form>
    </AppLayout>
</template>
