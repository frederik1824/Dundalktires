<script setup>

import Icon from '@/Components/Icon.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const props = defineProps({
    customers: Object,
    filters: Object,
});

const search = ref(props.filters?.q ?? '');

let timeout;
watch(search, (val) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        router.get('/customers', { q: val }, { preserveState: true, replace: true });
    }, 300);
});
</script>

<template>
    <AppLayout>
        <template #header>
            <h1 class="text-sm font-semibold text-slate-800">{{ t('customers.title') }}</h1>
        </template>
        <template #header-actions>
            <Link
                href="/customers/create"
                class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-brand-700 transition"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                {{ t('customers.newCustomer') }}
            </Link>
        </template>

        <!-- Search -->
        <div class="mb-4">
            <div class="relative max-w-sm">
                <svg class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0A7 7 0 104.65 4.65a7 7 0 0012 12z"/>
                </svg>
                <input
                    v-model="search"
                    type="text"
                    :placeholder="t('customers.searchPlaceholder')"
                    class="w-full rounded-lg pl-9 pr-3 py-2 text-sm placeholder-slate-400 outline-none focus:ring-2 focus:ring-brand-500"
                    style="border: 1px solid var(--color-border); background: var(--color-surface);"
                />
            </div>
        </div>

        <!-- Table -->
        <div class="rounded-xl overflow-x-auto shadow-sm" style="background:var(--color-surface);border:1px solid var(--color-border);">
            <table class="w-full text-sm">
                <thead>
                    <tr style="border-bottom:1px solid var(--color-border);">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ t('customers.name') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ t('customers.phone') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider hidden md:table-cell">{{ t('customers.email') }}</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ t('customers.vehicles') }}</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="c in customers.data"
                        :key="c.id"
                        class="hover:bg-slate-50 transition-colors"
                        style="border-bottom:1px solid var(--color-border);"
                    >
                        <td class="px-4 py-3">
                            <Link :href="`/customers/${c.id}`" class="font-medium text-brand-700 hover:underline">
                                {{ c.name }}
                            </Link>
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ c.phone ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-500 hidden md:table-cell">{{ c.email ?? '—' }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-semibold"
                                :class="c.vehicles_count > 0 ? 'bg-brand-50 text-brand-700' : 'bg-slate-100 text-slate-400'">
                                {{ c.vehicles_count }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <Link :href="`/customers/${c.id}/edit`" class="btn btn-ghost btn-sm mr-2">
                                <Icon name="edit" />
                                {{ t('common.edit') }}
                            </Link>
                        </td>
                    </tr>
                    <tr v-if="customers.data.length === 0">
                        <td colspan="5" class="px-4 py-16 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center mb-4">
                                    <Icon name="user" class="w-8 h-8" />
                                </div>
                                <h3 class="text-sm font-semibold text-slate-700 mb-1">{{ t('customers.noResults') }}</h3>
                                <p class="text-xs text-slate-500 mb-4">Empieza agregando tu primer cliente a la base de datos.</p>
                                <Link href="/customers/create" class="btn btn-primary shadow-sm">
                                    <Icon name="plus" /> {{ t('customers.addFirst') }}
                                </Link>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Pagination -->
            <div v-if="customers.last_page > 1" class="flex items-center justify-between px-4 py-3" style="border-top:1px solid var(--color-border);">
                <p class="text-xs text-slate-500">
                    {{ t('customers.showing') }} {{ customers.from }}–{{ customers.to }} {{ t('customers.of') }} {{ customers.total }}
                </p>
                <div class="flex gap-1">
                    <Link
                        v-for="link in customers.links"
                        :key="link.label"
                        :href="link.url ?? '#'"
                        v-html="link.label"
                        :class="[
                            'px-2.5 py-1 rounded text-xs',
                            link.active ? 'bg-brand-600 text-white font-semibold' : 'text-slate-500 hover:bg-slate-100',
                            !link.url ? 'opacity-40 pointer-events-none' : ''
                        ]"
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
