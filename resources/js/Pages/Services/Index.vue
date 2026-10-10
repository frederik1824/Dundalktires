<script setup>
import { confirmDelete } from '@/utils/alerts';


import Icon from '@/Components/Icon.vue';
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { usePermissions } from '@/composables/usePermissions.js';

const { t } = useI18n();
const { can } = usePermissions();

const props = defineProps({
    services:   Object,
    categories: Array,
    filters:    Object,
});

const q        = ref(props.filters.q ?? '');
const category = ref(props.filters.category ?? '');

function search() {
    router.get('/services', { q: q.value, category: category.value }, { preserveState: true, replace: true });
}

function pricingLabel(type) {
    return t('services.pricing.' + type) ?? type;
}

function pricingBadge(type) {
    return {
        fixed:           'badge-blue',
        by_vehicle_type: 'badge-purple',
        variable:        'badge-amber',
    }[type] ?? 'badge-gray';
}

async function destroy(id) {
    if (await confirmDelete(t('services.confirmDelete'))) {
        router.delete(`/services/${id}`);
    }
}
</script>

<template>
    <AppLayout>
        <template #header>
            <div class="page-header">
                <div>
                    <h1 class="page-title">{{ t('services.title') }}</h1>
                    <p class="page-subtitle">{{ t('services.subtitle') }}</p>
                </div>
                <Link v-if="can('manage_services')" href="/services/create" class="btn btn-primary">
                                <Icon name="plus" />
                                {{ t('services.add') }}
                            </Link>
            </div>
        </template>

        <!-- Filters -->
        <div class="card mb-4 flex flex-wrap gap-2">
            <input v-model="q" @input="search" type="search" :placeholder="t('services.searchPlaceholder')" class="input w-56" />
            <select v-model="category" @change="search" class="input w-48">
                <option value="">{{ t('services.allCategories') }}</option>
                <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
            </select>
        </div>

        <!-- Table -->
        <div class="card p-0 overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-border">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-text-2">{{ t('services.columns.service') }}</th>
                        <th class="px-4 py-3 text-left font-medium text-text-2">{{ t('services.columns.category') }}</th>
                        <th class="px-4 py-3 text-left font-medium text-text-2">{{ t('services.columns.pricing') }}</th>
                        <th class="px-4 py-3 text-right font-medium text-text-2">{{ t('services.columns.basePrice') }}</th>
                        <th class="px-4 py-3 text-center font-medium text-text-2">{{ t('services.columns.estMin') }}</th>
                        <th class="px-4 py-3 text-center font-medium text-text-2">{{ t('services.columns.status') }}</th>
                        <th class="px-4 py-3 text-right font-medium text-text-2">{{ t('services.columns.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    <tr v-for="svc in services.data" :key="svc.id" class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3 font-medium text-text">{{ svc.name }}</td>
                        <td class="px-4 py-3 text-text-2">{{ svc.category?.name ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <span :class="['badge', pricingBadge(svc.pricing_type)]">
                                {{ pricingLabel(svc.pricing_type) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right font-mono text-text">${{ svc.base_price }}</td>
                        <td class="px-4 py-3 text-center text-text-2">{{ svc.estimated_minutes ?? '—' }}</td>
                        <td class="px-4 py-3 text-center">
                            <span :class="svc.is_active ? 'badge-green' : 'badge-gray'" class="badge">
                                {{ svc.is_active ? t('common.active') : t('common.inactive') }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <Link v-if="can('manage_services')" :href="`/services/${svc.id}/edit`" class="btn btn-ghost btn-sm">
                                <Icon name="edit" />
                                {{ t('common.edit') }}
                            </Link>
                                <button v-if="can('manage_services')" @click="destroy(svc.id)" class="btn btn-ghost btn-sm text-red-500 hover:text-red-700 hover:bg-red-50 border-red-200">
                                <Icon name="delete" />
                                {{ t('common.delete') }}
                            </button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="!services.data.length">
                        <td colspan="7" class="px-4 py-10 text-center text-text-3">{{ t('services.noResults') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <Pagination :links="services.links" class="mt-4" />
    </AppLayout>
</template>
