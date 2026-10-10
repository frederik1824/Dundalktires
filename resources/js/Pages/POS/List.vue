<script setup>

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
    invoices: Object,
    filters:  Object,
});

const q      = ref(props.filters.q ?? '');
const status = ref(props.filters.status ?? '');

function search() {
    router.get('/invoices', { q: q.value, status: status.value }, { preserveState: true, replace: true });
}

const statusColors = { paid: 'badge-green', partial: 'badge-amber', draft: 'badge-gray', void: 'badge-red' };
</script>

<template>
    <AppLayout>
        <template #header>
            <div class="page-header">
                <div>
                    <h1 class="page-title">{{ t('invoices.title') }}</h1>
                    <p class="page-subtitle">{{ t('invoices.subtitle') }}</p>
                </div>
                <Link v-if="can('manage_sales')" href="/pos" class="btn btn-primary">
                                <Icon name="plus" />
                                {{ t('invoices.newInvoice') }}
                            </Link>
            </div>
        </template>

        <div class="card mb-4 flex flex-wrap gap-2">
            <input v-model="q" @input="search" type="search" :placeholder="t('invoices.searchPlaceholder')" class="input w-56" />
            <select v-model="status" @change="search" class="input w-36">
                <option value="">{{ t('invoices.allStatus') }}</option>
                <option value="draft">{{ t('invoices.statuses.draft') }}</option>
                <option value="paid">{{ t('invoices.statuses.paid') }}</option>
                <option value="partial">{{ t('invoices.statuses.partial') }}</option>
                <option value="void">{{ t('invoices.statuses.void') }}</option>
            </select>
        </div>

        <div class="card p-0 overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-border">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-text-2">{{ t('invoices.columns.number') }}</th>
                        <th class="px-4 py-3 text-left font-medium text-text-2">{{ t('invoices.columns.customer') }}</th>
                        <th class="px-4 py-3 text-center font-medium text-text-2">{{ t('invoices.columns.items') }}</th>
                        <th class="px-4 py-3 text-left font-medium text-text-2">{{ t('invoices.columns.status') }}</th>
                        <th class="px-4 py-3 text-right font-medium text-text-2">{{ t('invoices.columns.total') }}</th>
                        <th class="px-4 py-3 text-right font-medium text-text-2">{{ t('invoices.columns.paid') }}</th>
                        <th class="px-4 py-3 text-right font-medium text-text-2">{{ t('invoices.columns.date') }}</th>
                        <th class="px-4 py-3 text-right font-medium text-text-2">{{ t('invoices.columns.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    <tr v-for="inv in invoices.data" :key="inv.id" class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3 font-mono font-semibold text-brand-700">{{ inv.number }}</td>
                        <td class="px-4 py-3 text-text">{{ inv.customer?.name ?? t('common.walkIn') }}</td>
                        <td class="px-4 py-3 text-center text-text-2">{{ inv.items_count }}</td>
                        <td class="px-4 py-3">
                            <span :class="['badge', statusColors[inv.status] ?? 'badge-gray']">{{ t('invoices.statuses.' + inv.status) }}</span>
                        </td>
                        <td class="px-4 py-3 text-right font-mono">${{ inv.total }}</td>
                        <td class="px-4 py-3 text-right font-mono text-green-600">${{ inv.amount_paid }}</td>
                        <td class="px-4 py-3 text-right text-text-2 text-xs">{{ new Date(inv.created_at).toLocaleDateString() }}</td>
                        <td class="px-4 py-3 text-right">
                            <Link :href="`/invoices/${inv.id}`" class="btn btn-ghost btn-sm">
                                <Icon name="view" />
                                {{ t('common.view') }}
                            </Link>
                        </td>
                    </tr>
                    <tr v-if="!invoices.data.length">
                        <td colspan="8" class="px-4 py-10 text-center text-text-3">{{ t('invoices.noResults') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <Pagination :links="invoices.links" class="mt-4" />
    </AppLayout>
</template>
