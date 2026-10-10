<script setup>
import { ref, watch, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';

const { t } = useI18n();

const props = defineProps({
    service:      Object,
    categories:   Array,
    vehicleTypes: Object,
});

const form = useForm({
    category_id:       props.service?.category_id ?? null,
    name:              props.service?.name ?? '',
    description:       props.service?.description ?? '',
    pricing_type:      props.service?.pricing_type ?? 'fixed',
    base_price:        props.service?.base_price ?? '0.00',
    cost_price:        props.service?.cost_price ?? '0.00',
    estimated_minutes: props.service?.estimated_minutes ?? null,
    is_active:         props.service?.is_active ?? true,
    sort_order:        props.service?.sort_order ?? 0,
    price_rules:       props.service?.price_rules ?? [],
});

const vehicleTypeKeys = computed(() => Object.keys(props.vehicleTypes));

// Ensure price_rules covers all vehicle types for by_vehicle_type
watch(() => form.pricing_type, (type) => {
    if (type === 'by_vehicle_type' && form.price_rules.length === 0) {
        form.price_rules = vehicleTypeKeys.value.map(vt => ({
            vehicle_type: vt,
            price: form.base_price,
        }));
    }
});

function submit() {
    if (props.service) {
        form.put(`/services/${props.service.id}`);
    } else {
        form.post('/services');
    }
}
</script>

<template>
    <AppLayout>
        <template #header>
            <div class="page-header">
                <h1 class="page-title">{{ service ? t('services.form.editTitle') : t('services.form.newTitle') }}</h1>
            </div>
        </template>

        <form @submit.prevent="submit" class="max-w-2xl space-y-5">
            <!-- Basic info -->
            <div class="card space-y-4">
                <h2 class="section-title">Service Details</h2>

                <div>
                    <label class="label">{{ t('services.form.category') }}</label>
                    <select v-model="form.category_id" class="input w-full">
                        <option :value="null">— No Category —</option>
                        <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
                    </select>
                </div>

                <div>
                    <label class="label">{{ t('services.form.name') }} <span class="text-red-500">*</span></label>
                    <input v-model="form.name" type="text" class="input w-full" placeholder="e.g. Tire Mounting" />
                    <p v-if="form.errors.name" class="field-error">{{ form.errors.name }}</p>
                </div>

                <div>
                    <label class="label">{{ t('services.form.description') }}</label>
                    <textarea v-model="form.description" rows="2" class="input w-full" placeholder="Short description…" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="label">{{ t('services.form.estMinutes') }}</label>
                        <input v-model="form.estimated_minutes" type="number" min="1" max="480" class="input w-full" placeholder="e.g. 30" />
                    </div>
                    <div>
                        <label class="label">Sort Order</label>
                        <input v-model="form.sort_order" type="number" min="0" class="input w-full" />
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" id="active" v-model="form.is_active" class="w-4 h-4 rounded border-border text-brand-600" />
                    <label for="active" class="text-sm text-text">{{ t('services.form.active') }}</label>
                </div>
            </div>

            <!-- Pricing -->
            <div class="card space-y-4">
                <h2 class="section-title">{{ t('services.columns.pricing') }}</h2>

                <div>
                    <label class="label">{{ t('services.form.pricingType') }} <span class="text-red-500">*</span></label>
                    <div class="flex gap-3">
                        <label v-for="type in ['fixed','by_vehicle_type','variable']" :key="type"
                            class="flex items-center gap-2 cursor-pointer px-3 py-2 rounded-lg border transition-colors"
                            :class="form.pricing_type === type ? 'border-brand-600 bg-brand-50 text-brand-700' : 'border-border text-text-2 hover:bg-slate-50'"
                        >
                            <input type="radio" v-model="form.pricing_type" :value="type" class="sr-only" />
                            <span class="text-sm font-medium capitalize">{{ t('services.pricing.' + type) }}</span>
                        </label>
                    </div>
                </div>

                <div v-if="form.pricing_type !== 'by_vehicle_type'">
                    <label class="label">{{ form.pricing_type === 'variable' ? 'Minimum Price' : t('services.form.basePrice') }} <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-text-3 text-sm">$</span>
                        <input v-model="form.base_price" type="number" step="0.01" min="0" class="input input-currency w-full" placeholder="0.00" />
                    </div>
                    <p v-if="form.pricing_type === 'variable'" class="text-xs text-text-3 mt-1">Technician will set the final price — this is just the minimum.</p>
                    <p v-if="form.errors.base_price" class="field-error">{{ form.errors.base_price }}</p>
                </div>

                <!-- Internal cost (for margin tracking) -->
                <div>
                    <label class="label">{{ t('services.form.costPrice') }}</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-text-3 text-sm">$</span>
                        <input v-model="form.cost_price" type="number" step="0.01" min="0" class="input input-currency w-full" placeholder="0.00" />
                    </div>
                    <p class="text-xs text-text-3 mt-1">{{ t('services.form.costPriceHint') }}</p>
                    <p v-if="form.errors.cost_price" class="field-error">{{ form.errors.cost_price }}</p>
                </div>

                <!-- Per vehicle type pricing -->
                <div v-if="form.pricing_type === 'by_vehicle_type'" class="space-y-2">
                    <label class="label">{{ t('services.form.vehiclePrices') }}</label>
                    <div v-for="rule in form.price_rules" :key="rule.vehicle_type" class="flex items-center gap-3">
                        <span class="text-sm text-text w-32 capitalize">{{ vehicleTypes[rule.vehicle_type] ?? rule.vehicle_type }}</span>
                        <div class="relative flex-1">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-text-3 text-sm">$</span>
                            <input v-model="rule.price" type="number" step="0.01" min="0" class="input input-currency w-full" />
                        </div>
                    </div>
                    <p class="text-xs text-text-3">Also set a Base Price as a fallback for unmatched types.</p>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-text-3 text-sm">$</span>
                        <input v-model="form.base_price" type="number" step="0.01" min="0" class="input input-currency w-48" placeholder="Fallback price" />
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex gap-3">
                <button type="submit" :disabled="form.processing" class="btn btn-primary">
                    {{ form.processing ? t('common.saving') : t('services.form.save') }}
                </button>
                <a href="/services" class="btn btn-ghost">{{ t('services.form.cancel') }}</a>
            </div>
        </form>
    </AppLayout>
</template>
