<script setup>
import { confirmDelete, confirmAction } from '@/utils/alerts';


import Icon from '@/Components/Icon.vue';
import { ref } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import { usePermissions } from '@/composables/usePermissions.js';

const { t } = useI18n();
const { can } = usePermissions();

const props = defineProps({
    employees: Object,
    filters:   Object,
});

const q = ref(props.filters.q ?? '');

function search() {
    router.get('/employees', { q: q.value }, { preserveState: true, replace: true });
}

async function destroy(id) {
    if (await confirmDelete(t('employees.confirmRemove'))) {
        router.delete(`/employees/${id}`);
    }
}

function roleColor(role) {
    const colors = { admin: 'badge-red', manager: 'badge-purple', technician: 'badge-blue', cashier: 'badge-green' };
    return colors[role?.toLowerCase()] ?? 'badge-gray';
}

// Password reset modal
const resetTarget = ref(null);
const resetForm = useForm({ password: '', password_confirmation: '' });

function openReset(emp) { resetTarget.value = emp; resetForm.reset(); }
function closeReset() { resetTarget.value = null; }
async function submitReset() {
    const ok = await confirmAction(
        `¿Restablecer contraseña de ${resetTarget.value.name}?`,
        'La contraseña actual quedará inválida de inmediato.',
        'Sí, restablecer'
    );
    if (!ok) return;
    resetForm.post(`/employees/${resetTarget.value.id}/reset-password`, {
        onSuccess: () => closeReset(),
    });
}
</script>

<template>
    <AppLayout>
        <template #header>
            <div class="page-header">
                <div>
                    <h1 class="page-title">{{ t('employees.title') }}</h1>
                    <p class="page-subtitle">{{ employees.total }} {{ t('employees.teamMembers') }}</p>
                </div>
                <Link v-if="can('manage_employees')" href="/employees/create" class="btn btn-primary">
                                <Icon name="plus" />
                                {{ t('employees.addEmployee') }}
                            </Link>
            </div>
        </template>

        <div class="card mb-4">
            <input v-model="q" @input="search" type="search" :placeholder="t('employees.searchPlaceholder')" class="input w-72" />
        </div>

        <div class="card p-0 overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-border">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-text-2">{{ t('employees.columns.employee') }}</th>
                        <th class="px-4 py-3 text-left font-medium text-text-2">{{ t('employees.columns.email') }}</th>
                        <th class="px-4 py-3 text-left font-medium text-text-2">{{ t('employees.columns.role') }}</th>
                        <th class="px-4 py-3 text-left font-medium text-text-2">{{ t('employees.columns.joined') }}</th>
                        <th class="px-4 py-3 text-right font-medium text-text-2">{{ t('employees.columns.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    <tr v-for="emp in employees.data" :key="emp.id" class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-brand-600 flex items-center justify-center text-white text-xs font-bold shrink-0">
                                    {{ emp.name.charAt(0).toUpperCase() }}
                                </div>
                                <span class="font-medium text-text">{{ emp.name }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-text-2">{{ emp.email }}</td>
                        <td class="px-4 py-3">
                            <span v-if="emp.roles?.length"
                                :class="['badge', roleColor(emp.roles[0].name)]">
                                {{ emp.roles[0].name }}
                            </span>
                            <span v-else class="text-text-3 text-xs">{{ t('employees.noRole') }}</span>
                        </td>
                        <td class="px-4 py-3 text-text-2 text-xs">{{ new Date(emp.created_at).toLocaleDateString() }}</td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <Link v-if="can('manage_employees')" :href="`/employees/${emp.id}/edit`" class="btn btn-ghost btn-sm">
                                    <Icon name="edit" />{{ t('common.edit') }}
                                </Link>
                                <button v-if="can('manage_employees')" @click="openReset(emp)"
                                    class="btn btn-ghost btn-sm text-amber-600 hover:text-amber-700 hover:bg-amber-50">
                                    🔑 Reset
                                </button>
                                <button v-if="can('manage_employees')" @click="destroy(emp.id)" class="btn btn-ghost btn-sm text-red-500 hover:text-red-700 hover:bg-red-50 border-red-200">
                                    <Icon name="delete" />{{ t('common.remove') }}
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="!employees.data.length">
                        <td colspan="5" class="px-4 py-10 text-center text-text-3">{{ t('employees.noResults') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <Pagination :links="employees.links" class="mt-4" />

        <!-- Reset password modal -->
        <div v-if="resetTarget" class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-sm p-6 space-y-4">
                <h3 class="font-semibold text-text text-lg">Reset Password</h3>
                <p class="text-sm text-text-2">Setting new password for <strong>{{ resetTarget.name }}</strong>.</p>
                <div>
                    <label class="label">New Password <span class="text-red-500">*</span></label>
                    <input v-model="resetForm.password" type="password" class="input w-full" placeholder="Min. 8 characters" autocomplete="new-password" />
                    <p v-if="resetForm.errors.password" class="field-error">{{ resetForm.errors.password }}</p>
                </div>
                <div>
                    <label class="label">Confirm Password</label>
                    <input v-model="resetForm.password_confirmation" type="password" class="input w-full" autocomplete="new-password" />
                </div>
                <div class="flex gap-2 pt-1">
                    <button @click="submitReset" :disabled="resetForm.processing" class="btn btn-primary flex-1">
                        {{ resetForm.processing ? 'Saving…' : 'Reset Password' }}
                    </button>
                    <button @click="closeReset" class="btn btn-ghost">Cancel</button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
