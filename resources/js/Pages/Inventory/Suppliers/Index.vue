<template>
  <AppLayout>
    <template #header>
      <div class="page-header">
        <div>
          <h1 class="page-title">{{ t('nav.suppliers') }}</h1>
          <p class="page-subtitle">{{ t('inventory.suppliersSubtitle') }}</p>
        </div>
        <Link v-if="can('manage_inventory')" :href="route('suppliers.create')" class="btn btn-primary">
          <Icon name="plus" />
                                {{ t('inventory.newSupplier') }}
        </Link>
      </div>
    </template>

    <!-- Filters -->
    <div class="card mb-6 flex gap-3 items-center flex-wrap">
      <input
        v-model="search"
        type="search"
        :placeholder="t('inventory.suppliersSearch')"
        class="input flex-1 min-w-48"
        @input="debouncedSearch"
      />
      <label class="flex items-center gap-2 text-sm text-text-2 cursor-pointer">
        <input v-model="activeOnly" type="checkbox" class="rounded" @change="applyFilters" />
        {{ t('inventory.activeOnly') }}
      </label>
    </div>

    <!-- Table -->
    <div class="card p-0 overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-border text-text-2 text-xs uppercase tracking-wide">
          <tr>
            <th class="px-4 py-3 text-left">{{ t('inventory.columns.name') }}</th>
            <th class="px-4 py-3 text-left">{{ t('inventory.columns.contact') }}</th>
            <th class="px-4 py-3 text-left">{{ t('inventory.columns.phone') }}</th>
            <th class="px-4 py-3 text-left">{{ t('inventory.columns.email') }}</th>
            <th class="px-4 py-3 text-center">{{ t('inventory.columns.products') }}</th>
            <th class="px-4 py-3 text-center">{{ t('inventory.columns.status') }}</th>
            <th class="px-4 py-3 text-right">{{ t('inventory.columns.actions') }}</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-border">
          <tr v-for="s in suppliers.data" :key="s.id" class="hover:bg-gray-50 transition-colors">
            <td class="px-4 py-3 font-medium text-text">{{ s.name }}</td>
            <td class="px-4 py-3 text-text-2">{{ s.contact_name || '—' }}</td>
            <td class="px-4 py-3 text-text-2">{{ s.phone || '—' }}</td>
            <td class="px-4 py-3 text-text-2">{{ s.email || '—' }}</td>
            <td class="px-4 py-3 text-center">
              <span class="text-text-2">{{ s.products_count }}</span>
            </td>
            <td class="px-4 py-3 text-center">
              <span :class="['badge', s.is_active ? 'badge-green' : 'badge-gray']">
                {{ s.is_active ? t('common.active') : t('common.inactive') }}
              </span>
            </td>
            <td class="px-4 py-3 text-right">
              <div class="flex justify-end gap-2">
                <Link :href="route('suppliers.edit', s.id)" class="btn btn-ghost btn-sm">
                                <Icon name="edit" />
                                {{ t('common.edit') }}
                            </Link>
                <button v-if="can('manage_inventory')" @click="destroy(s)" class="btn btn-ghost btn-sm text-red-500 hover:bg-red-50 hover:border-red-200">
                                <Icon name="delete" />
                                {{ t('common.delete') }}
                            </button>
              </div>
            </td>
          </tr>
          <tr v-if="suppliers.data.length === 0">
                        <td colspan="100%" class="px-4 py-16 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center mb-4">
                                    <Icon name="box" class="w-8 h-8" />
                                </div>
                                <h3 class="text-sm font-semibold text-slate-700 mb-1">Aún no hay registros</h3>
                                <p class="text-xs text-slate-500 mb-4">No se encontraron datos para esta sección.</p>
                                <Link href="/suppliers/create" class="btn btn-primary shadow-sm"><Icon name="plus" /> Nuevo Proveedor</Link>
                            </div>
                        </td></tr>
        </tbody>
      </table>

      <!-- Pagination -->
      <div v-if="suppliers.last_page > 1" class="px-4 py-3 border-t border-border flex justify-end gap-1">
        <Link
          v-for="link in suppliers.links"
          :key="link.label"
          :href="link.url || '#'"
          :class="['px-3 py-1.5 rounded text-sm', link.active ? 'bg-brand-600 text-white' : 'text-text-2 hover:bg-gray-100', !link.url && 'opacity-40 pointer-events-none']"
          v-html="link.label"
          preserve-scroll
        />
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { confirmDelete } from '@/utils/alerts';


import Icon from '@/Components/Icon.vue';
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import AppLayout from '@/Layouts/AppLayout.vue'
import { usePermissions } from '@/composables/usePermissions.js'

const { t } = useI18n()
const { can } = usePermissions()

const props = defineProps({
  suppliers: Object,
  filters: Object,
})

const search     = ref(props.filters?.search || '')
const activeOnly = ref(props.filters?.active === 'true')

let timer = null
function debouncedSearch() {
  clearTimeout(timer)
  timer = setTimeout(applyFilters, 300)
}

function applyFilters() {
  router.get(route('suppliers.index'), {
    search: search.value || undefined,
    active: activeOnly.value ? 'true' : undefined,
  }, { preserveState: true, replace: true })
}

async function destroy(supplier) {
  if (await confirmDelete(t('inventory.confirmDeleteSupplier'))) {
    router.delete(route('suppliers.destroy', supplier.id))
  }
}
</script>
