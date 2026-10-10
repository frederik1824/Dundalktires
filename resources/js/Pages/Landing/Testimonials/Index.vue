<script setup>
import { router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { confirmDelete } from '@/utils/alerts';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

const { t } = useI18n();

defineProps({ testimonials: Array });

async function destroy(id) {
    if (await confirmDelete(t('testimonials.confirmDelete'))) {
        router.delete(`/landing/testimonials/${id}`, { preserveScroll: true });
    }
}

function stars(n) {
    return '★'.repeat(n) + '☆'.repeat(5 - n);
}
</script>

<template>
    <AppLayout>
        <template #header>
            <div class="page-header">
                <div>
                    <h1 class="page-title">{{ t('testimonials.title') }}</h1>
                    <p class="page-subtitle">{{ t('testimonials.subtitle') }}</p>
                </div>
                <Link href="/landing/testimonials/create" class="btn btn-primary">
                    {{ t('testimonials.newReview') }}
                </Link>
            </div>
        </template>

        <!-- Preview hint -->
        <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg text-sm text-blue-700">
            💡 Copia las mejores reseñas directamente desde
            <a href="https://www.google.com/maps/place/Dundalk+Used+and+New+Tires/@39.2457525,-76.5091015,17z"
               target="_blank" class="font-semibold underline hover:text-blue-900">
                Google Maps
            </a>
            y pégalas aquí con el nombre del cliente y su calificación.
        </div>

        <div class="card p-0 overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-border">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-text-2 w-8">{{ t('testimonials.columns.order') }}</th>
                        <th class="px-4 py-3 text-left font-medium text-text-2">{{ t('testimonials.columns.author') }}</th>
                        <th class="px-4 py-3 text-left font-medium text-text-2">{{ t('testimonials.columns.rating') }}</th>
                        <th class="px-4 py-3 text-left font-medium text-text-2">{{ t('testimonials.columns.excerpt') }}</th>
                        <th class="px-4 py-3 text-left font-medium text-text-2">{{ t('testimonials.columns.date') }}</th>
                        <th class="px-4 py-3 text-center font-medium text-text-2">{{ t('testimonials.columns.active') }}</th>
                        <th class="px-4 py-3 text-right font-medium text-text-2">{{ t('testimonials.columns.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    <tr v-if="testimonials.length === 0">
                        <td colspan="7" class="px-4 py-10 text-center text-text-3">{{ t('testimonials.noResults') }}</td>
                    </tr>
                    <tr v-for="t in testimonials" :key="t.id" class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3 text-center text-text-3 text-xs font-mono">{{ t.sort_order }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-brand-red flex items-center justify-center text-white text-xs font-bold shrink-0">
                                    {{ t.initials }}
                                </div>
                                <span class="font-medium text-text">{{ t.author_name }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-yellow-500 tracking-wider text-base">
                            {{ stars(t.rating) }}
                        </td>
                        <td class="px-4 py-3 text-text-2 text-xs max-w-xs">
                            <span class="line-clamp-2">{{ t.body }}</span>
                        </td>
                        <td class="px-4 py-3 text-text-3 text-xs">
                            {{ t.reviewed_at ? new Date(t.reviewed_at).toLocaleDateString('en-US', { month:'short', year:'numeric' }) : '—' }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span :class="t.is_active ? 'badge badge-green' : 'badge badge-gray'">
                                {{ t.is_active ? 'Yes' : 'No' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex justify-end gap-1">
                                <Link :href="`/landing/testimonials/${t.id}/edit`" class="btn btn-ghost btn-sm">
                                    Edit
                                </Link>
                                <button @click="destroy(t.id)" class="btn btn-ghost btn-sm text-red-600">
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AppLayout>
</template>
