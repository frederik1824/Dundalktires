<template>
  <AppLayout :title="productType === 'tire' ? t('inventory.tires') : t('inventory.materials')">
    <div class="page-header">
      <div>
        <h1 class="page-title">{{ productType === 'tire' ? t('inventory.tires') : t('inventory.materials') }}</h1>
        <p class="page-subtitle">{{ products.total }} items</p>
      </div>
      <Link v-if="can('manage_inventory')" :href="route('products.create', { type: productType })" class="btn btn-primary">
        {{ productType === 'tire' ? t('inventory.addTire') : t('inventory.addItem') }}
      </Link>
    </div>

    <!-- Type tabs -->
    <div class="flex gap-1 mb-5 border-b border-border">
      <Link
        :href="route('products.index', { type: 'tire' })"
        :class="['px-4 py-2.5 text-sm font-medium border-b-2 transition-colors -mb-px', productType === 'tire' ? 'text-brand-600 border-brand-600' : 'text-text-2 border-transparent hover:text-text']"
      >
        <Icon name="plus" />
                                {{ t('nav.tires') }}
      </Link>
      <Link
        :href="route('products.index', { type: 'material' })"
        :class="['px-4 py-2.5 text-sm font-medium border-b-2 transition-colors -mb-px', productType !== 'tire' ? 'text-brand-600 border-brand-600' : 'text-text-2 border-transparent hover:text-text']"
      >
        {{ t('nav.materials') }}
      </Link>
    </div>

    <!-- Filters -->
    <div class="card mb-5 flex gap-3 flex-wrap items-end">
      <div class="flex-1 min-w-48">
        <label class="label text-xs">Search</label>
        <input
          v-model="search"
          type="search"
          :placeholder="productType === 'tire' ? 'Brand, size, SKU…' : 'Name, SKU…'"
          class="input"
          @input="debouncedSearch"
        />
      </div>
      <div class="min-w-36">
        <label class="label text-xs">Supplier</label>
        <select v-model="supplierId" class="input" @change="applyFilters">
          <option value="">All suppliers</option>
          <option v-for="s in suppliers" :key="s.id" :value="s.id">{{ s.name }}</option>
        </select>
      </div>
      <div class="min-w-36">
        <label class="label text-xs">Category</label>
        <select v-model="categoryId" class="input" @change="applyFilters">
          <option value="">All categories</option>
          <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
        </select>
      </div>
      <label class="flex items-center gap-2 text-sm text-text-2 cursor-pointer mb-0.5">
        <input v-model="lowStock" type="checkbox" class="rounded" @change="applyFilters" />
        {{ t('inventory.lowStock') }}
      </label>
    </div>

    <!-- Table -->
    <div class="card p-0 overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-border text-text-3 text-xs uppercase tracking-wide">
          <tr v-if="productType === 'tire'">
            <th class="px-4 py-3 text-left">Brand / Line</th>
            <th class="px-4 py-3 text-left">Size</th>
            <th class="px-4 py-3 text-left">Type</th>
            <th class="px-4 py-3 text-left">Supplier</th>
            <th class="px-4 py-3 text-right">Cost</th>
            <th class="px-4 py-3 text-right">Sale</th>
            <th class="px-4 py-3 text-center">Stock</th>
            <th class="px-4 py-3 text-right">Actions</th>
          </tr>
          <tr v-else>
            <th class="px-4 py-3 text-left">Name</th>
            <th class="px-4 py-3 text-left">SKU</th>
            <th class="px-4 py-3 text-left">Category</th>
            <th class="px-4 py-3 text-left">Supplier</th>
            <th class="px-4 py-3 text-right">Cost</th>
            <th class="px-4 py-3 text-right">Sale</th>
            <th class="px-4 py-3 text-center">Stock</th>
            <th class="px-4 py-3 text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-border">
          <template v-if="productType === 'tire'">
            <tr v-for="p in products.data" :key="p.id" class="hover:bg-gray-50 transition-colors">
              <td class="px-4 py-3">
                <div class="flex items-center gap-1.5">
                  <span class="font-medium text-text">{{ p.tire_brand }}</span>
                  <span v-if="p.is_featured" class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200" title="Destacado en el sitio web">
                    <svg class="w-2.5 h-2.5 fill-current text-amber-500" viewBox="0 0 20 20">
                      <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    Destacado
                  </span>
                </div>
                <div class="text-xs text-text-3">{{ p.tire_line }}</div>
              </td>
              <td class="px-4 py-3">
                <span class="inline-flex px-2 py-0.5 rounded bg-brand-50 text-brand-700 text-xs font-mono font-medium">
                  {{ tireSize(p) }}
                </span>
              </td>
              <td class="px-4 py-3 text-text-2 text-xs">{{ p.tire_type || '—' }}</td>
              <td class="px-4 py-3 text-text-2 text-xs">{{ p.supplier?.name || '—' }}</td>
              <td class="px-4 py-3 text-right text-text-2">${{ fmt(p.cost_price) }}</td>
              <td class="px-4 py-3 text-right font-medium text-text">${{ fmt(p.sale_price) }}</td>
              <td class="px-4 py-3 text-center">
                <StockBadge :qty="p.stock_qty" :min="p.stock_min" />
              </td>
              <td class="px-4 py-3 text-right">
                <div class="flex justify-end items-center gap-1">
                  <button v-if="can('manage_inventory')"
                    @click="toggleFeatured(p)"
                    type="button"
                    :title="p.is_featured ? 'Quitar de destacados en la web' : 'Destacar en la web'"
                    :class="[
                      'p-1.5 rounded transition-all flex items-center justify-center',
                      p.is_featured 
                        ? 'bg-amber-100 text-amber-600 hover:bg-amber-200 border border-amber-300 shadow-xs' 
                        : 'text-gray-300 hover:text-amber-500 hover:bg-gray-100 border border-transparent'
                    ]">
                    <svg v-if="p.is_featured" class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                      <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    <svg v-else class="w-4 h-4 fill-none stroke-current stroke-2" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                    </svg>
                  </button>
                  <Link :href="route('products.show', p.id)" class="btn-sm btn-ghost">View</Link>
                  <Link v-if="can('manage_inventory')" :href="route('products.edit', p.id)" class="btn-sm btn-ghost">
                    <Icon name="edit" />
                    Edit
                  </Link>
                </div>
              </td>
            </tr>
          </template>
          <template v-else>
            <tr v-for="p in products.data" :key="p.id" class="hover:bg-gray-50 transition-colors">
              <td class="px-4 py-3 font-medium text-text">{{ p.name }}</td>
              <td class="px-4 py-3 text-text-2 font-mono text-xs">{{ p.sku || '—' }}</td>
              <td class="px-4 py-3">
                <span v-if="p.category" class="inline-flex px-2 py-0.5 rounded text-xs font-medium text-white" :style="{backgroundColor: p.category.color}">
                  {{ p.category.name }}
                </span>
                <span v-else class="text-text-3">—</span>
              </td>
              <td class="px-4 py-3 text-text-2 text-xs">{{ p.supplier?.name || '—' }}</td>
              <td class="px-4 py-3 text-right text-text-2">${{ fmt(p.cost_price) }}</td>
              <td class="px-4 py-3 text-right font-medium text-text">${{ fmt(p.sale_price) }}</td>
              <td class="px-4 py-3 text-center">
                <StockBadge :qty="p.stock_qty" :min="p.stock_min" />
              </td>
              <td class="px-4 py-3 text-right">
                <div class="flex justify-end gap-1">
                  <Link :href="route('products.show', p.id)" class="btn-sm btn-ghost">View</Link>
                  <Link v-if="can('manage_inventory')" :href="route('products.edit', p.id)" class="btn-sm btn-ghost">
                                <Icon name="edit" />
                                Edit
                            </Link>
                </div>
              </td>
            </tr>
          </template>
          <tr v-if="products.data.length === 0">
                        <td colspan="100%" class="px-4 py-16 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center mb-4">
                                    <Icon name="box" class="w-8 h-8" />
                                </div>
                                <h3 class="text-sm font-semibold text-slate-700 mb-1">Aún no hay registros</h3>
                                <p class="text-xs text-slate-500 mb-4">No se encontraron datos para esta sección.</p>
                                <Link href="/products/create" class="btn btn-primary shadow-sm"><Icon name="plus" /> Nuevo Producto</Link>
                            </div>
                        </td></tr>
        </tbody>
      </table>

      <!-- Pagination -->
      <div v-if="products.last_page > 1" class="px-4 py-3 border-t border-border flex justify-end gap-1">
        <Link
          v-for="link in products.links"
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
import StockBadge from '@/Components/StockBadge.vue'
import { usePermissions } from '@/composables/usePermissions.js'

const { t } = useI18n()
const { can } = usePermissions()

const props = defineProps({
  products:    Object,
  suppliers:   Array,
  categories:  Array,
  filters:     Object,
  productType: String,
})

const search     = ref(props.filters?.search || '')
const supplierId = ref(props.filters?.supplier_id || '')
const categoryId = ref(props.filters?.category_id || '')
const lowStock   = ref(props.filters?.low_stock === 'true')

let timer = null
function debouncedSearch() {
  clearTimeout(timer)
  timer = setTimeout(applyFilters, 300)
}

function applyFilters() {
  router.get(route('products.index'), {
    type:        props.productType,
    search:      search.value || undefined,
    supplier_id: supplierId.value || undefined,
    category_id: categoryId.value || undefined,
    low_stock:   lowStock.value ? 'true' : undefined,
  }, { preserveState: true, replace: true })
}

function tireSize(p) {
  if (!p.tire_width) return '—'
  return `${p.tire_width}/${p.tire_ratio}R${p.tire_diameter}`
}

function fmt(val) {
  return Number(val).toFixed(2)
}

function toggleFeatured(p) {
  p.is_featured = !p.is_featured
  router.post(route('products.toggle-featured', p.id), {}, {
    preserveScroll: true,
    onError: () => {
      p.is_featured = !p.is_featured
    },
  })
}
</script>

