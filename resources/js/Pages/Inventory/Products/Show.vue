<template>
  <AppLayout>
    <template #header>
      <div class="page-header">
        <div>
          <div class="flex items-center gap-2 text-sm mb-1">
            <Link :href="route('products.index', { type: product.type })" class="text-slate-400 hover:text-slate-700">
              {{ product.type === 'tire' ? t('inventory.tires') : t('inventory.materials') }}
            </Link>
            <span class="text-slate-300">/</span>
            <span class="text-slate-600">{{ productName }}</span>
          </div>
          <h1 class="page-title">{{ productName }}</h1>
        </div>
        <div class="flex gap-2">
          <button v-if="can('manage_inventory')" @click="showAdjust = true" class="btn btn-ghost">{{ t('inventory.adjustStock') }}</button>
          <Link v-if="can('manage_inventory')" :href="route('products.edit', product.id)" class="btn btn-primary">
                                <Icon name="edit" />
                                {{ t('common.edit') }}
                            </Link>
        </div>
      </div>
    </template>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
      <!-- Info card -->
      <div class="card space-y-4">
        <!-- Tire size badge -->
        <div v-if="product.type === 'tire'" class="text-center">
          <span class="inline-block px-5 py-3 bg-brand-50 text-brand-700 rounded-xl text-2xl font-mono font-bold tracking-wide">
            {{ tireSize }}
          </span>
          <div class="mt-2 text-sm text-text-2">{{ product.tire_brand }} {{ product.tire_line }}</div>
          <div v-if="product.tire_type" class="mt-1">
            <span class="text-xs bg-gray-100 text-text-2 px-2 py-0.5 rounded-full">{{ product.tire_type }}</span>
          </div>
        </div>
        <div v-else>
          <p class="font-medium text-text text-lg">{{ product.name }}</p>
          <p v-if="product.sku" class="text-xs font-mono text-text-3 mt-0.5">SKU: {{ product.sku }}</p>
        </div>

        <hr class="border-border" />

        <dl class="space-y-2 text-sm">
          <div class="flex justify-between">
            <dt class="text-text-2">{{ t('inventory.costPrice') }}</dt>
            <dd class="font-medium">${{ fmt(product.cost_price) }}</dd>
          </div>
          <div class="flex justify-between">
            <dt class="text-text-2">{{ t('inventory.salePrice') }}</dt>
            <dd class="font-medium text-emerald-700">${{ fmt(product.sale_price) }}</dd>
          </div>
          <div class="flex justify-between">
            <dt class="text-text-2">{{ t('inventory.margin') }}</dt>
            <dd class="font-medium">{{ margin }}%</dd>
          </div>
        </dl>

        <hr class="border-border" />

        <div class="text-center">
          <div class="text-3xl font-bold" :class="stockColor">{{ product.stock_qty }}</div>
          <div class="text-xs text-text-3 mt-0.5">{{ t('inventory.unitsInStock') }}</div>
          <div class="text-xs text-text-3">{{ t('inventory.minStock') }} {{ product.stock_min }}</div>
          <StockBadge :qty="product.stock_qty" :min="product.stock_min" class="mt-2" />
        </div>

        <hr class="border-border" />

        <dl class="space-y-2 text-sm">
          <div v-if="product.supplier" class="flex justify-between">
            <dt class="text-text-2">{{ t('inventory.supplier') }}</dt>
            <dd>{{ product.supplier.name }}</dd>
          </div>
          <div v-if="product.category" class="flex justify-between">
            <dt class="text-text-2">{{ t('inventory.category') }}</dt>
            <dd>
              <span class="inline-flex px-2 py-0.5 rounded text-xs text-white" :style="{backgroundColor: product.category.color}">
                {{ product.category.name }}
              </span>
            </dd>
          </div>
          <div v-if="product.location" class="flex justify-between">
            <dt class="text-text-2">{{ t('inventory.location') }}</dt>
            <dd class="font-mono">{{ product.location }}</dd>
          </div>
          <div class="flex justify-between">
            <dt class="text-text-2">{{ t('common.status') }}</dt>
            <dd><span :class="product.is_active ? 'badge-green' : 'badge-gray'">{{ product.is_active ? t('common.active') : t('common.inactive') }}</span></dd>
          </div>
        </dl>
      </div>

      <!-- Movement history -->
      <div class="lg:col-span-2 card p-0 overflow-x-auto">
        <div class="px-4 py-3 border-b border-border">
          <h2 class="font-semibold text-text">{{ t('inventory.movementHistory') }}</h2>
        </div>
        <div class="overflow-x-auto"><table class="w-full text-sm">
          <thead class="bg-gray-50 text-xs text-text-3 uppercase">
            <tr>
              <th class="px-4 py-2 text-left">{{ t('inventory.movementColumns.date') }}</th>
              <th class="px-4 py-2 text-center">{{ t('inventory.movementColumns.type') }}</th>
              <th class="px-4 py-2 text-right">{{ t('inventory.movementColumns.change') }}</th>
              <th class="px-4 py-2 text-right">{{ t('inventory.movementColumns.after') }}</th>
              <th class="px-4 py-2 text-left">{{ t('inventory.movementColumns.by') }}</th>
              <th class="px-4 py-2 text-left">{{ t('inventory.movementColumns.notes') }}</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border">
            <tr v-for="m in movements.data" :key="m.id" class="hover:bg-gray-50">
              <td class="px-4 py-2.5 text-text-2 text-xs whitespace-nowrap">{{ fmtDate(m.created_at) }}</td>
              <td class="px-4 py-2.5 text-center">
                <span :class="movementBadge(m.type)">{{ m.type }}</span>
              </td>
              <td class="px-4 py-2.5 text-right font-mono font-medium" :class="m.qty_change > 0 ? 'text-emerald-600' : 'text-red-500'">
                {{ m.qty_change > 0 ? '+' : '' }}{{ m.qty_change }}
              </td>
              <td class="px-4 py-2.5 text-right font-mono text-text-2">{{ m.qty_after }}</td>
              <td class="px-4 py-2.5 text-text-2 text-xs">{{ m.user?.name || '—' }}</td>
              <td class="px-4 py-2.5 text-text-3 text-xs">{{ m.notes || '—' }}</td>
            </tr>
            <tr v-if="movements.data.length === 0">
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
        </table></div>
      </div>
    </div>

    <!-- Adjust stock modal -->
    <div v-if="showAdjust" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
      <div class="card w-full max-w-sm space-y-4">
        <h3 class="font-semibold text-text">{{ t('inventory.adjustStock') }}</h3>
        <p class="text-sm text-text-2">{{ t('inventory.currentStock') }} <strong>{{ product.stock_qty }}</strong></p>

        <div>
          <label class="label">{{ t('inventory.quantityChange') }} <span class="text-text-3 text-xs">{{ t('inventory.quantityChangeHint') }}</span></label>
          <input v-model.number="adjustQty" type="number" class="input" placeholder="e.g. 4 or -2" />
        </div>
        <div>
          <label class="label">{{ t('common.reason') }} <span class="text-red-500">*</span></label>
          <input v-model="adjustNotes" type="text" class="input" placeholder="Inventory count, damage, return…" />
        </div>

        <div class="flex justify-end gap-2 pt-1">
          <button @click="showAdjust = false" class="btn btn-ghost">{{ t('common.cancel') }}</button>
          <button @click="submitAdjust" :disabled="!adjustQty || !adjustNotes" class="btn btn-primary">{{ t('common.apply') }}</button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>

import Icon from '@/Components/Icon.vue';
import { ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import AppLayout from '@/Layouts/AppLayout.vue'
import StockBadge from '@/Components/StockBadge.vue'
import { usePermissions } from '@/composables/usePermissions.js'
import { confirmAction } from '@/utils/alerts'

const { t } = useI18n()
const { can } = usePermissions()

const props = defineProps({
  product:   Object,
  movements: Object,
})

const showAdjust  = ref(false)
const adjustQty   = ref(0)
const adjustNotes = ref('')

const tireSize = computed(() => {
  if (!props.product.tire_width) return null
  return `${props.product.tire_width}/${props.product.tire_ratio}R${props.product.tire_diameter}`
})

const productName = computed(() => {
  if (props.product.type === 'tire') {
    return `${props.product.tire_brand} ${props.product.tire_line || ''} ${tireSize.value || ''}`.trim()
  }
  return props.product.name
})

const margin = computed(() => {
  const cost = Number(props.product.cost_price)
  const sale = Number(props.product.sale_price)
  if (!sale) return '0.0'
  return (((sale - cost) / sale) * 100).toFixed(1)
})

const stockColor = computed(() => {
  if (props.product.stock_qty <= 0) return 'text-red-500'
  if (props.product.stock_qty <= props.product.stock_min) return 'text-amber-500'
  return 'text-emerald-600'
})

function movementBadge(type) {
  const map = {
    purchase:   'badge-blue',
    sale:       'badge-gray',
    adjustment: 'badge-purple',
    return:     'badge-green',
    correction: 'badge-amber',
    transfer:   'badge-gray',
  }
  return map[type] || 'badge-gray'
}

function fmt(val) { return Number(val).toFixed(2) }
function fmtDate(d) {
  return new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit' })
}

async function submitAdjust() {
  const sign = adjustQty.value > 0 ? '+' : '';
  const ok = await confirmAction(
    `¿Aplicar ajuste de inventario (${sign}${adjustQty.value})?`,
    'El stock del producto se actualizará de forma permanente.',
    'Sí, aplicar ajuste'
  );
  if (!ok) return;
  router.post(route('products.adjust', props.product.id), {
    qty_change: adjustQty.value,
    notes:      adjustNotes.value,
  }, {
    onSuccess: () => {
      showAdjust.value  = false
      adjustQty.value   = 0
      adjustNotes.value = ''
    },
  });
}
</script>
