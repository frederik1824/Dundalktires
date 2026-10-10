<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const props = defineProps({
    customer: Object,
    vehicle: Object,
    vehicleTypes: Object,
    catalog: { type: Array, default: () => [] },
});

const isEdit = !!props.vehicle;

const form = useForm({
    make:      props.vehicle?.make      ?? '',
    model:     props.vehicle?.model     ?? '',
    year:      props.vehicle?.year      ?? new Date().getFullYear(),
    color:     props.vehicle?.color     ?? '',
    plate:     props.vehicle?.plate     ?? '',
    vin:       props.vehicle?.vin       ?? '',
    type:      props.vehicle?.type      ?? 'sedan',
    tire_size: props.vehicle?.tire_size ?? '',
    mileage:   props.vehicle?.mileage   ?? '',
    notes:     props.vehicle?.notes     ?? '',
});

const currentYear = new Date().getFullYear();

import { ref, computed } from 'vue';
const searchCatalog = ref('');
const showCatalog = ref(false);

const filteredCatalog = computed(() => {
    if (!searchCatalog.value) return [];
    const q = searchCatalog.value.toLowerCase();
    return props.catalog.filter(v => 
        (v.make + ' ' + v.model + ' ' + v.year).toLowerCase().includes(q)
    ).slice(0, 15);
});

function selectFromCatalog(v) {
    form.make = v.make;
    form.model = v.model;
    form.year = v.year;
    if (v.type) form.type = v.type;
    if (v.tire_size) form.tire_size = v.tire_size;
    
    showCatalog.value = false;
    searchCatalog.value = '';
}

function submit() {
    const base = `/customers/${props.customer.id}/vehicles`;
    if (isEdit) {
        form.put(`${base}/${props.vehicle.id}`);
    } else {
        form.post(base);
    }
}
</script>

<template>
    <AppLayout>
        <template #header>
            <div class="flex items-center gap-2 text-sm">
                <Link href="/customers" class="text-slate-400 hover:text-slate-700">{{ t('nav.customers') }}</Link>
                <span class="text-slate-300">/</span>
                <Link :href="`/customers/${customer.id}`" class="text-slate-400 hover:text-slate-700">{{ customer.name }}</Link>
                <span class="text-slate-300">/</span>
                <span class="font-semibold text-slate-800">{{ isEdit ? t('vehicles.editVehicle') : t('vehicles.addVehicle') }}</span>
            </div>
        </template>

        <div class="max-w-lg">
            <form @submit.prevent="submit" class="card space-y-4">

                
                <!-- Buscador de catálogo -->
                <div class="mb-4 relative" v-if="catalog.length > 0">
                    <label class="label text-brand-700 flex items-center gap-1.5 mb-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        Autocompletar desde el historial
                    </label>
                    <input 
                        v-model="searchCatalog" 
                        @focus="showCatalog = true" 
                        type="text" 
                        placeholder="Ej. Toyota Corolla 2018..." 
                        class="input bg-brand-50/30 border-brand-200 focus:border-brand-500 w-full focus:ring-brand-500/20"
                    />
                    
                    <div v-if="showCatalog && searchCatalog" class="absolute z-20 w-full mt-1 bg-white border border-slate-200 rounded-lg shadow-xl overflow-hidden">
                        <button v-if="filteredCatalog.length === 0" type="button" class="w-full text-left px-4 py-3 text-sm text-slate-500 italic cursor-default">
                            No se encontraron coincidencias en el historial.
                        </button>
                        <button 
                            v-for="(v, i) in filteredCatalog" :key="i"
                            type="button"
                            @click="selectFromCatalog(v)"
                            class="w-full text-left px-4 py-2 hover:bg-brand-50 focus:bg-brand-50 text-sm border-b border-slate-100 last:border-0 transition-colors"
                        >
                            <span class="font-bold text-slate-800">{{ v.make }} {{ v.model }}</span> 
                            <span class="text-slate-500 ml-1">({{ v.year }})</span>
                            <span v-if="v.tire_size" class="inline-block mt-1 sm:mt-0 sm:ml-2 text-[10px] font-bold uppercase px-2 py-0.5 rounded bg-slate-100 text-slate-600">Llantas: {{ v.tire_size }}</span>
                        </button>
                    </div>
                </div>

                <!-- Fondo para cerrar el dropdown al hacer click afuera -->
                <div v-if="showCatalog" @click="showCatalog = false" class="fixed inset-0 z-10"></div>

                <div class="relative z-0">
                <!-- Make / Model -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="label">{{ t('vehicles.make') }} <span class="text-red-500">*</span></label>
                        <input v-model="form.make" type="text" required placeholder="Toyota, Honda, Ford..."
                            class="input" />
                        <p v-if="form.errors.make" class="mt-1 text-xs text-red-600">{{ form.errors.make }}</p>
                    </div>
                    <div>
                        <label class="label">{{ t('vehicles.model') }} <span class="text-red-500">*</span></label>
                        <input v-model="form.model" type="text" required placeholder="Camry, Civic, F-150..."
                            class="input" />
                        <p v-if="form.errors.model" class="mt-1 text-xs text-red-600">{{ form.errors.model }}</p>
                    </div>
                </div>

                <!-- Year / Color -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="label">{{ t('vehicles.year') }} <span class="text-red-500">*</span></label>
                        <input v-model="form.year" type="number" required :min="1950" :max="currentYear + 2"
                            class="input" />
                        <p v-if="form.errors.year" class="mt-1 text-xs text-red-600">{{ form.errors.year }}</p>
                    </div>
                    <div>
                        <label class="label">{{ t('vehicles.color') }}</label>
                        <input v-model="form.color" type="text" placeholder="Silver, Black..."
                            class="input" />
                    </div>
                </div>

                <!-- Type -->
                <div>
                    <label class="label">{{ t('vehicles.type') }} <span class="text-red-500">*</span></label>
                    <div class="flex flex-wrap gap-2">
                        <button
                            v-for="(label, key) in vehicleTypes"
                            :key="key"
                            type="button"
                            @click="form.type = key"
                            :class="[
                                'px-3 py-1.5 rounded-lg text-sm border transition',
                                form.type === key
                                    ? 'bg-brand-600 text-white border-brand-600'
                                    : 'text-slate-600 border-slate-200 hover:border-brand-400'
                            ]"
                        >{{ label }}</button>
                    </div>
                    <p v-if="form.errors.type" class="mt-1 text-xs text-red-600">{{ form.errors.type }}</p>
                </div>

                <!-- Plate / VIN -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="label">{{ t('vehicles.plate') }}</label>
                        <input v-model="form.plate" type="text" placeholder="1ABC234"
                            class="input" />
                    </div>
                    <div>
                        <label class="label">VIN <span class="text-xs text-slate-400">(optional)</span></label>
                        <input v-model="form.vin" type="text" maxlength="17" placeholder="17 characters"
                            class="w-full rounded-lg px-3 py-2 text-sm font-mono outline-none focus:ring-2 focus:ring-brand-500" />
                        <p v-if="form.errors.vin" class="mt-1 text-xs text-red-600">{{ form.errors.vin }}</p>
                    </div>
                </div>

                <!-- Tire size / Mileage -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="label">{{ t('vehicles.tireSize') }}</label>
                        <input v-model="form.tire_size" type="text" placeholder="215/55R17"
                            class="input" />
                        <p class="mt-0.5 text-xs text-slate-400">Used to pre-fill tire searches</p>
                    </div>
                    <div>
                        <label class="label">{{ t('vehicles.mileage') }}</label>
                        <input v-model="form.mileage" type="number" min="0" placeholder="45000"
                            class="input" />
                    </div>
                </div>

                <!-- Notes -->
                <div>
                    <label class="label">{{ t('vehicles.notes') }}</label>
                    <textarea v-model="form.notes" rows="2" placeholder="Known issues, customer preferences..."
                        class="w-full rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-brand-500 resize-none" />
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" :disabled="form.processing"
                        class="btn btn-primary">
                        {{ form.processing ? t('common.saving') : (isEdit ? t('vehicles.saveChanges') : t('vehicles.create')) }}
                    </button>
                    <Link :href="`/customers/${customer.id}`" class="text-sm text-slate-500 hover:text-slate-700">
                        {{ t('vehicles.cancel') }}
                    </Link>
                </div>
            </div>
            </form>
        </div>
    </AppLayout>
</template>
