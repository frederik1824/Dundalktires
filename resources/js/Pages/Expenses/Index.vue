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
    expenses:   Object,
    categories: Array,
    filters:    Object,
    totals:     Object,
});

const q        = ref(props.filters.q ?? '');
const category = ref(props.filters.category ?? '');
const type     = ref(props.filters.type ?? '');
const from     = ref(props.filters.from ?? '');
const to       = ref(props.filters.to ?? '');

function search() {
    router.get('/expenses', { q: q.value, category: category.value, type: type.value, from: from.value, to: to.value },
        { preserveState: true, replace: true });
}

async function destroy(id) {
    if (await confirmDelete(t('expenses.confirmDelete'))) router.delete(`/expenses/${id}`);
}

const methodIcons = { cash: '💵', card: '💳', zelle: '⚡', cashapp: '💚', transfer: '🏦', check: '📋', other: '📦' };
</script>

<template>
    <AppLayout>
        <template #header>
            <div class="page-header">
                <div>
                    <h1 class="page-title">{{ t('expenses.title') }}</h1>
                    <p class="page-subtitle">
                        {{ totals?.count ?? 0 }} {{ t('expenses.records') }} ·
                        <span class="font-mono font-semibold text-red-600">${{ parseFloat(totals?.total ?? 0).toFixed(2) }}</span> {{ t('expenses.total') }}
                    </p>
                </div>
                <Link v-if="can('manage_expenses')" href="/expenses/create" class="btn btn-primary">
                                <Icon name="plus" />
                                {{ t('expenses.newExpense') }}
                            </Link>
            </div>
        </template>

        <!-- Filters -->
        <div class="card mb-4 flex flex-wrap gap-2">
            <input v-model="q" @input="search" type="search" :placeholder="t('expenses.searchPlaceholder')" class="input w-48" />
            <select v-model="category" @change="search" class="input w-44">
                <option value="">{{ t('expenses.allCategories') }}</option>
                <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
            </select>
            <select v-model="type" @change="search" class="input w-32">
                <option value="">{{ t('expenses.allTypes') }}</option>
                <option value="fixed">{{ t('expenses.types.fixed') }}</option>
                <option value="variable">{{ t('expenses.types.variable') }}</option>
            </select>
            <input v-model="from" @change="search" type="date" class="input w-36" />
            <span class="text-text-3 self-center text-sm">{{ t('common.to') }}</span>
            <input v-model="to" @change="search" type="date" class="input w-36" />
        </div>

        <div class="card p-0 overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-border">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-text-2">{{ t('expenses.columns.date') }}</th>
                        <th class="px-4 py-3 text-left font-medium text-text-2">{{ t('expenses.columns.description') }}</th>
                        <th class="px-4 py-3 text-left font-medium text-text-2">{{ t('expenses.columns.category') }}</th>
                        <th class="px-4 py-3 text-left font-medium text-text-2">{{ t('expenses.columns.vendor') }}</th>
                        <th class="px-4 py-3 text-left font-medium text-text-2">{{ t('expenses.columns.method') }}</th>
                        <th class="px-4 py-3 text-center font-medium text-text-2">{{ t('expenses.columns.recurring') }}</th>
                        <th class="px-4 py-3 text-right font-medium text-text-2">{{ t('expenses.columns.amount') }}</th>
                        <th class="px-4 py-3 text-right font-medium text-text-2">{{ t('expenses.columns.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    <tr v-for="exp in expenses.data" :key="exp.id" class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3 text-text-2 text-xs font-mono">{{ exp.paid_at }}</td>
                        <td class="px-4 py-3 text-text font-medium">
                            {{ exp.description }}
                            <a v-if="exp.evidence" :href="`/storage/${exp.evidence}`" target="_blank"
                                class="ml-1 text-brand-600 text-xs hover:underline">📎</a>
                        </td>
                        <td class="px-4 py-3">
                            <span v-if="exp.category" class="badge" :style="`background-color: ${exp.category.color}22; color: ${exp.category.color}; border: 1px solid ${exp.category.color}44`">
                                {{ exp.category.name }}
                            </span>
                            <span v-else class="text-text-3">—</span>
                        </td>
                        <td class="px-4 py-3 text-text-2">{{ exp.vendor ?? '—' }}</td>
                        <td class="px-4 py-3 text-text-2">{{ methodIcons[exp.payment_method] }} {{ exp.payment_method }}</td>
                        <td class="px-4 py-3 text-center">
                            <span v-if="exp.is_recurring" class="badge badge-blue">{{ exp.recurrence }}</span>
                            <span v-else class="text-text-3">—</span>
                        </td>
                        <td class="px-4 py-3 text-right font-mono font-semibold text-red-600">${{ exp.amount }}</td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <Link :href="`/expenses/${exp.id}/edit`" class="btn btn-ghost btn-sm">
                                <Icon name="edit" />
                                {{ t('common.edit') }}
                            </Link>
                                <button v-if="can('manage_expenses')" @click="destroy(exp.id)" class="btn btn-ghost btn-sm text-red-500 hover:text-red-700 hover:bg-red-50 border-red-200">
                                <Icon name="delete" />
                                {{ t('common.delete') }}
                            </button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="!expenses.data.length">
                        <td colspan="8" class="px-4 py-10 text-center text-text-3">{{ t('expenses.noResults') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <Pagination :links="expenses.links" class="mt-4" />
    </AppLayout>
</template>
