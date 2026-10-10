<script setup>
import { router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { confirmDelete } from '@/utils/alerts';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

const { t } = useI18n();

const props = defineProps({
    promotions: Array,
});

async function destroy(id) {
    if (await confirmDelete(t('promotions.confirmDelete'))) {
        router.delete(`/landing/promotions/${id}`, { preserveScroll: true });
    }
}
</script>

<template>
    <AppLayout>
        <template #header>
            <div class="page-header">
                <div>
                    <h1 class="page-title">{{ t('promotions.title') }}</h1>
                    <p class="page-subtitle">{{ t('promotions.subtitle') }}</p>
                </div>
                <Link href="/landing/promotions/create" class="btn btn-primary">
                    {{ t('promotions.newPromotion') }}
                </Link>
            </div>
        </template>

        <div class="card p-0 overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-border">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-text-2 w-8">{{ t('promotions.columns.order') }}</th>
                        <th class="px-4 py-3 text-left font-medium text-text-2">{{ t('promotions.columns.title') }}</th>
                        <th class="px-4 py-3 text-left font-medium text-text-2">{{ t('promotions.columns.badge') }}</th>
                        <th class="px-4 py-3 text-left font-medium text-text-2">{{ t('promotions.columns.dates') }}</th>
                        <th class="px-4 py-3 text-center font-medium text-text-2">{{ t('promotions.columns.active') }}</th>
                        <th class="px-4 py-3 text-right font-medium text-text-2">{{ t('promotions.columns.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    <tr v-if="promotions.length === 0">
                        <td colspan="6" class="px-4 py-10 text-center text-text-3">{{ t('promotions.noResults') }}</td>
                    </tr>
                    <tr v-for="promo in promotions" :key="promo.id" class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3 text-center text-text-3 text-xs font-mono">{{ promo.sort_order }}</td>
                        <td class="px-4 py-3">
                            <div class="font-medium text-text">{{ promo.title }}</div>
                            <div v-if="promo.description" class="text-xs text-text-3 mt-0.5 max-w-xs truncate">{{ promo.description }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <span v-if="promo.badge_text" class="badge badge-blue">{{ promo.badge_text }}</span>
                            <span v-else class="text-text-3">—</span>
                        </td>
                        <td class="px-4 py-3 text-xs text-text-2">
                            <span v-if="promo.starts_at || promo.ends_at">
                                {{ promo.starts_at ?? '∞' }} → {{ promo.ends_at ?? '∞' }}
                            </span>
                            <span v-else class="text-text-3">Always</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span :class="promo.is_active ? 'badge badge-green' : 'badge badge-gray'">
                                {{ promo.is_active ? t('common.yes') : t('common.no') }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex justify-end gap-1">
                                <Link :href="`/landing/promotions/${promo.id}/edit`" class="btn btn-ghost btn-sm">
                                    {{ t('common.edit') }}
                                </Link>
                                <button @click="destroy(promo.id)" class="btn btn-ghost btn-sm text-red-600">
                                    {{ t('common.delete') }}
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AppLayout>
</template>
