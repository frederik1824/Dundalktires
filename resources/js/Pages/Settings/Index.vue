<script setup>
import { useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';

const { t } = useI18n();

const props = defineProps({
    settings: Array,
});

// Build form with a `settings` object matching controller expectation
const form = useForm({
    settings: Object.fromEntries(props.settings.map(s => [s.key, s.value ?? ''])),
});

function submit() {
    form.put('/settings');
}

const groups = [
    {
        title: 'business',
        keys: ['business_name', 'business_address', 'business_phone', 'business_email'],
    },
    {
        title: 'numbering',
        keys: ['invoice_prefix', 'purchase_prefix', 'work_order_prefix'],
    },
    {
        title: 'financial',
        keys: ['tax_rate', 'currency_symbol', 'low_stock_alert'],
    },
    {
        title: 'operations',
        keys: ['tire_disposal_cost'],
    },
];

function settingByKey(key) {
    return props.settings.find(s => s.key === key);
}
</script>

<template>
    <AppLayout>
        <template #header>
            <div class="page-header">
                <div>
                    <h1 class="page-title">{{ t('settings.title') }}</h1>
                    <p class="page-subtitle">{{ t('settings.subtitle') }}</p>
                </div>
                <button @click="submit" :disabled="form.processing" class="btn btn-primary">
                    {{ form.processing ? t('settings.saving') : t('settings.save') }}
                </button>
            </div>
        </template>

        <div v-if="$page.props.flash?.success" class="mb-4 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm">
            {{ $page.props.flash.success }}
        </div>

        <form @submit.prevent="submit" class="max-w-2xl space-y-6">
            <div v-for="group in groups" :key="group.title" class="card space-y-4">
                <h2 class="section-title">{{ t('settings.groups.' + group.title) }}</h2>

                <div v-for="key in group.keys" :key="key">
                    <template v-if="settingByKey(key)">
                        <label class="label">{{ settingByKey(key).label }}</label>
                        <div :class="{'relative': key === 'tire_disposal_cost'}">
                            <span v-if="key === 'tire_disposal_cost'" class="absolute left-3 top-1/2 -translate-y-1/2 text-text-3 text-sm pointer-events-none">$</span>
                            <input
                                v-model="form.settings[key]"
                                :type="settingByKey(key).type === 'float' || settingByKey(key).type === 'integer' ? 'number' : 'text'"
                                :step="settingByKey(key).type === 'float' ? '0.01' : settingByKey(key).type === 'integer' ? '1' : undefined"
                                :min="settingByKey(key).type !== 'string' ? '0' : undefined"
                                :class="['input w-full', key === 'tire_disposal_cost' ? 'input-currency' : '']"
                                :placeholder="settingByKey(key).label"
                            />
                        </div>
                        <p v-if="key === 'tire_disposal_cost'" class="text-xs text-text-3 mt-1">
                            Costo operativo por defecto aplicado a las órdenes de trabajo cuando el cliente deja sus llantas usadas para desecho.
                        </p>
                    </template>
                </div>
            </div>

            <button type="submit" :disabled="form.processing" class="btn btn-primary">
                {{ form.processing ? t('settings.saving') : t('settings.save') }}
            </button>
        </form>
    </AppLayout>
</template>
