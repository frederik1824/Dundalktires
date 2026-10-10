<template>
  <AppLayout>
    <template #header>
      <div class="page-header">
        <div>
          <h1 class="page-title">{{ t('purchases.orders') }}</h1>
          <p class="page-subtitle">{{ t('purchases.subtitle') }}</p>
        </div>
        <div v-if="can('manage_purchases')" class="flex gap-2">
          <Link :href="route('purchases.create', { type: 'suggested' })" class="btn btn-ghost">
            {{ t('purchases.suggested') }}
          </Link>
          <Link :href="route('purchases.create')" class="btn btn-primary">
            <Icon name="plus" />
                                {{ t('purchases.newOrder') }}
          </Link>
        </div>
      </div>
    </template>

    <!-- Filters -->
    <div class="card mb-5 flex gap-3 flex-wrap items-end">
      <div class="flex-1 min-w-40">
        <label class="label text-xs">{{ t('common.search') }}</label>
        <input v-model="search" type="search" :placeholder="t('purchases.searchPlaceholder')" class="input" @input="debouncedSearch" />
      </div>
      <div class="min-w-36">
        <label class="label text-xs">{{ t('common.status') }}</label>
        <select v-model="status" class="input" @change="applyFilters">
          <option value="">{{ t('purchases.allStatuses') }}</option>
          <option value="draft">{{ t('purchases.statuses.draft') }}</option>
          <option value="sent">{{ t('purchases.statuses.sent') }}</option>
          <option value="partial">{{ t('purchases.statuses.partial') }}</option>
          <option value="received">{{ t('purchases.statuses.received') }}</option>
          <option value="cancelled">{{ t('purchases.statuses.cancelled') }}</option>
        </select>
      </div>
      <div class="min-w-36">
        <label class="label text-xs">{{ t('purchases.supplier') }}</label>
        <select v-model="supplierId" class="input" @change="applyFilters">
          <option value="">{{ t('purchases.allSuppliers') }}</option>
          <option v-for="s in suppliers" :key="s.id" :value="s.id">{{ s.name }}</option>
        </select>
      </div>
    </div>

    <!-- Table -->
    <div class="card p-0 overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-border text-text-3 text-xs uppercase tracking-wide">
          <tr>
            <th class="px-4 py-3 text-left">{{ t('purchases.columns.order') }}</th>
            <th class="px-4 py-3 text-left">{{ t('purchases.columns.supplier') }}</th>
            <th class="px-4 py-3 text-center">{{ t('purchases.columns.status') }}</th>
            <th class="px-4 py-3 text-center">{{ t('purchases.columns.items') }}</th>
            <th class="px-4 py-3 text-right">{{ t('purchases.columns.total') }}</th>
            <th class="px-4 py-3 text-left">{{ t('purchases.columns.created') }}</th>
            <th class="px-4 py-3 text-right">{{ t('purchases.columns.actions') }}</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-border">
          <tr v-for="o in orders.data" :key="o.id" class="hover:bg-gray-50 transition-colors">
            <td class="px-4 py-3">
              <div class="font-mono font-medium text-text">{{ o.number }}</div>
              <div v-if="o.type === 'suggested'" class="text-xs text-brand-600">{{ t('purchases.suggestedBadge') }}</div>
            </td>
            <td class="px-4 py-3 text-text-2">{{ o.supplier?.name || '—' }}</td>
            <td class="px-4 py-3 text-center">
              <span :class="['badge', statusBadge(o.status)]">{{ t('purchases.statuses.' + o.status) }}</span>
            </td>
            <td class="px-4 py-3 text-center text-text-2">{{ o.items?.length ?? 0 }}</td>
            <td class="px-4 py-3 text-right font-medium text-text">${{ orderTotal(o) }}</td>
            <td class="px-4 py-3 text-text-3 text-xs">{{ fmtDate(o.created_at) }}</td>
            <td class="px-4 py-3 text-right">
              <Link :href="route('purchases.show', o.id)" class="btn btn-ghost btn-sm">
                                <Icon name="view" />
                                {{ t('common.view') }}
                            </Link>
            </td>
          </tr>
          <tr v-if="orders.data.length === 0">
                        <td colspan="100%" class="px-4 py-16 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center mb-4">
                                    <Icon name="box" class="w-8 h-8" />
                                </div>
                                <h3 class="text-sm font-semibold text-slate-700 mb-1">Aún no hay registros</h3>
                                <p class="text-xs text-slate-500 mb-4">No se encontraron datos para esta sección.</p>
                                <Link href="/purchases/create" class="btn btn-primary shadow-sm"><Icon name="plus" /> Nueva Compra</Link>
                            </div>
                        </td></tr>
        </tbody>
      </table>

      <div v-if="orders.last_page > 1" class="px-4 py-3 border-t border-border flex justify-end gap-1">
        <Link
          v-for="link in orders.links"
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

import Icon from '@/Components/Icon.vue';
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import AppLayout from '@/Layouts/AppLayout.vue'
import { usePermissions } from '@/composables/usePermissions.js'

const { t } = useI18n()
const { can } = usePermissions()

const props = defineProps({
  orders:    Object,
  suppliers: Array,
  filters:   Object,
})

const search     = ref(props.filters?.search || '')
const status     = ref(props.filters?.status || '')
const supplierId = ref(props.filters?.supplier_id || '')

let timer = null
function debouncedSearch() {
  clearTimeout(timer)
  timer = setTimeout(applyFilters, 300)
}
function applyFilters() {
  router.get(route('purchases.index'), {
    search:      search.value || undefined,
    status:      status.value || undefined,
    supplier_id: supplierId.value || undefined,
  }, { preserveState: true, replace: true })
}

function statusBadge(s) {
  const map = { draft: 'badge-gray', sent: 'badge-blue', partial: 'badge-amber', received: 'badge-green', cancelled: 'badge-red' }
  return map[s] || 'badge-gray'
}

function orderTotal(o) {
  const total = (o.items || []).reduce((sum, i) => sum + i.qty_ordered * i.unit_cost, 0)
  return total.toFixed(2)
}

function fmtDate(d) {
  return new Date(d).toLocaleDateString()
}
</script>
