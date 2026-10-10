<script setup>
import { ref } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { confirmDelete } from '@/utils/alerts';
import AppLayout from '@/Layouts/AppLayout.vue';
import Pagination from '@/Components/Pagination.vue';

const { t } = useI18n();

const props = defineProps({
    leads:   Object,
    filters: Object,
    counts:  Object,
});

const q      = ref(props.filters.q ?? '');
const status = ref(props.filters.status ?? '');
const from   = ref(props.filters.from ?? '');
const to     = ref(props.filters.to ?? '');

function search() {
    router.get('/web-leads', { q: q.value, status: status.value, from: from.value, to: to.value },
        { preserveState: true, replace: true });
}

// Inline update form per row
const editing = ref(null);
const editForm = useForm({ status: '', staff_notes: '' });

function startEdit(lead) {
    editing.value = lead.id;
    editForm.status      = lead.status;
    editForm.staff_notes = lead.staff_notes ?? '';
}

function saveEdit(lead) {
    editForm.put(`/web-leads/${lead.id}`, {
        preserveScroll: true,
        onSuccess: () => { editing.value = null; },
    });
}

function cancelEdit() { editing.value = null; }

async function destroy(id) {
    if (await confirmDelete(t('webLeads.confirmDelete'))) {
        router.delete(`/web-leads/${id}`, { preserveScroll: true });
    }
}

const statusBadge = {
    new:       'badge-blue',
    contacted: 'badge-amber',
    closed:    'badge-green',
};
</script>

<template>
    <AppLayout>
        <template #header>
            <div class="page-header">
                <div>
                    <h1 class="page-title">{{ t('webLeads.title') }}</h1>
                    <p class="page-subtitle">
                        <span class="text-blue-600 font-semibold">{{ counts.new }}</span> {{ t('webLeads.statuses.new') }} ·
                        <span class="text-amber-600 font-semibold">{{ counts.contacted }}</span> {{ t('webLeads.statuses.contacted') }} ·
                        <span class="text-green-600 font-semibold">{{ counts.closed }}</span> {{ t('webLeads.statuses.closed') }}
                    </p>
                </div>
            </div>
        </template>

        <!-- Filters -->
        <div class="card mb-4 flex flex-wrap gap-2">
            <input v-model="q" @input="search" type="search"
                :placeholder="t('webLeads.searchPlaceholder')" class="input w-56" />
            <select v-model="status" @change="search" class="input w-40">
                <option value="">{{ t('webLeads.allStatuses') }}</option>
                <option value="new">{{ t('webLeads.statuses.new') }}</option>
                <option value="contacted">{{ t('webLeads.statuses.contacted') }}</option>
                <option value="closed">{{ t('webLeads.statuses.closed') }}</option>
            </select>
            <input v-model="from" @change="search" type="date" class="input w-36" />
            <span class="text-text-3 self-center text-sm">{{ t('common.to') }}</span>
            <input v-model="to" @change="search" type="date" class="input w-36" />
        </div>

        <div class="card p-0 overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-border">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-text-2">{{ t('webLeads.columns.date') }}</th>
                        <th class="px-4 py-3 text-left font-medium text-text-2">{{ t('webLeads.columns.name') }}</th>
                        <th class="px-4 py-3 text-left font-medium text-text-2">{{ t('webLeads.columns.phone') }}</th>
                        <th class="px-4 py-3 text-left font-medium text-text-2">{{ t('webLeads.columns.service') }}</th>
                        <th class="px-4 py-3 text-left font-medium text-text-2">{{ t('webLeads.columns.vehicle') }}</th>
                        <th class="px-4 py-3 text-left font-medium text-text-2">{{ t('webLeads.columns.status') }}</th>
                        <th class="px-4 py-3 text-right font-medium text-text-2">{{ t('webLeads.columns.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    <template v-if="leads.data.length === 0">
                        <tr>
                            <td colspan="7" class="px-4 py-10 text-center text-text-3">{{ t('webLeads.noResults') }}</td>
                        </tr>
                    </template>

                    <template v-for="lead in leads.data" :key="lead.id">
                        <!-- Main row -->
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3 text-text-2 text-xs font-mono whitespace-nowrap">
                                {{ new Date(lead.created_at).toLocaleDateString() }}
                            </td>
                            <td class="px-4 py-3 font-medium text-text">
                                {{ lead.name }}
                                <div v-if="lead.staff_notes" class="text-xs text-text-3 mt-0.5 max-w-xs truncate">
                                    📝 {{ lead.staff_notes }}
                                </div>
                            </td>
                            <td class="px-4 py-3 text-text-2 font-mono text-xs">{{ lead.phone }}</td>
                            <td class="px-4 py-3 text-text-2">{{ lead.service_type }}</td>
                            <td class="px-4 py-3 text-text-2 text-xs">
                                <div v-if="lead.vehicle">{{ lead.vehicle }}</div>
                                <div v-if="lead.tire_size" class="font-mono text-brand-600">{{ lead.tire_size }}</div>
                                <span v-if="!lead.vehicle && !lead.tire_size" class="text-text-3">—</span>
                            </td>
                            <td class="px-4 py-3">
                                <span :class="['badge', statusBadge[lead.status]]">
                                    {{ t(`webLeads.statuses.${lead.status}`) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-1">
                                    <button @click="startEdit(lead)" class="btn btn-ghost btn-sm">
                                        Edit
                                    </button>
                                    <button @click="destroy(lead.id)" class="btn btn-ghost btn-sm text-red-600">
                                        {{ t('common.delete') }}
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Inline edit row -->
                        <tr v-if="editing === lead.id" class="bg-slate-50">
                            <td colspan="7" class="px-4 py-3">
                                <div class="flex flex-wrap gap-3 items-end">
                                    <div>
                                        <label class="label text-xs">{{ t('webLeads.columns.status') }}</label>
                                        <select v-model="editForm.status" class="input w-36">
                                            <option value="new">{{ t('webLeads.statuses.new') }}</option>
                                            <option value="contacted">{{ t('webLeads.statuses.contacted') }}</option>
                                            <option value="closed">{{ t('webLeads.statuses.closed') }}</option>
                                        </select>
                                    </div>
                                    <div class="flex-1 min-w-48">
                                        <label class="label text-xs">{{ t('webLeads.staffNotes') }}</label>
                                        <input v-model="editForm.staff_notes" type="text" class="input" />
                                    </div>
                                    <div class="flex gap-2">
                                        <button @click="saveEdit(lead)" :disabled="editForm.processing"
                                            class="btn btn-primary">{{ t('webLeads.saveNotes') }}</button>
                                        <button @click="cancelEdit" class="btn btn-ghost btn-sm">{{ t('common.cancel') }}</button>
                                    </div>
                                </div>
                                <p v-if="editForm.errors.status" class="field-error mt-1">{{ editForm.errors.status }}</p>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            <Pagination :links="leads.links" />
        </div>
    </AppLayout>
</template>
