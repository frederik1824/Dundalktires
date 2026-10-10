<template>
  <AppLayout>
    <template #header>
      <div class="page-header">
        <div>
          <div class="flex items-center gap-2 text-sm mb-1">
            <Link :href="route('purchases.index')" class="text-slate-400 hover:text-slate-700">{{ t('nav.purchases') }}</Link>
            <span class="text-slate-300">/</span>
            <span class="text-slate-600">{{ orderType === 'suggested' ? t('purchases.suggested') : t('purchases.newOrder') }}</span>
          </div>
          <h1 class="page-title">{{ orderType === 'suggested' ? t('purchases.suggested') : t('purchases.orders') }}</h1>
        </div>
        <div class="text-right">
          <div class="text-xs text-text-3 uppercase tracking-wide">{{ t('purchases.columns.order') }}</div>
          <div class="font-mono font-bold text-text text-lg">{{ nextNumber }}</div>
        </div>
      </div>
    </template>

    <form @submit.prevent="submit" class="space-y-5">
      <!-- Header card -->
      <div class="card">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div>
            <label class="label">{{ t('purchases.supplier') }}</label>
            <select v-model="form.supplier_id" class="input">
              <option value="">{{ t('purchases.allSuppliers') }}</option>
              <option v-for="s in suppliers" :key="s.id" :value="s.id">{{ s.name }}</option>
            </select>
          </div>
          <div>
            <label class="label">{{ t('purchases.orderDate') }}</label>
            <input v-model="form.ordered_at" type="date" class="input" />
          </div>
          <div>
            <label class="label">{{ t('common.notes') }}</label>
            <input v-model="form.notes" type="text" class="input" placeholder="Internal notes…" />
          </div>
        </div>
      </div>

      <!-- Items table -->
      <div class="card p-0 overflow-x-auto">
        <div class="px-4 py-3 border-b border-border flex items-center justify-between">
          <h2 class="font-semibold text-text">{{ t('purchases.orderItems') }}</h2>
          <button type="button" @click="showProductSearch = true" class="btn btn-ghost text-xs">
            {{ t('inventory.addItem') }}
          </button>
        </div>

        <div class="overflow-x-auto"><table class="w-full text-sm">
          <thead class="bg-gray-50 text-xs text-text-3 uppercase">
            <tr>
              <th class="px-4 py-2 text-left">{{ t('purchases.product') }}</th>
              <th class="px-4 py-2 text-center w-20">{{ t('inventory.stock') }}</th>
              <th class="px-4 py-2 text-center w-24">{{ t('purchases.ordered') }}</th>
              <th class="px-4 py-2 text-right w-28">{{ t('purchases.unitCost') }}</th>
              <th class="px-4 py-2 text-right w-28">{{ t('workOrders.form.subtotal') }}</th>
              <th class="px-4 py-2 w-10"></th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border">
            <tr v-for="(item, idx) in form.items" :key="idx" class="hover:bg-gray-50">
              <td class="px-4 py-2.5">
                <div class="font-medium text-text">{{ item.product?.name || `Product #${item.product_id}` }}</div>
                <div v-if="item.product?.tire_size" class="text-xs font-mono text-brand-600">{{ item.product.tire_size }}</div>
              </td>
              <td class="px-4 py-2.5 text-center">
                <span class="text-xs" :class="item.product?.stock_qty <= item.product?.stock_min ? 'text-amber-600 font-medium' : 'text-text-3'">
                  {{ item.product?.stock_qty ?? '—' }}
                </span>
              </td>
              <td class="px-4 py-2.5">
                <input v-model.number="item.qty_ordered" type="number" min="1" class="input text-center py-1 text-sm" />
              </td>
              <td class="px-4 py-2.5">
                <div class="relative">
                  <span class="absolute inset-y-0 left-3 flex items-center text-text-3 text-xs">$</span>
                  <input v-model.number="item.unit_cost" type="number" step="0.01" min="0" class="input input-currency py-1 text-sm text-right" />
                </div>
              </td>
              <td class="px-4 py-2.5 text-right font-medium text-text">
                ${{ (item.qty_ordered * item.unit_cost).toFixed(2) }}
              </td>
              <td class="px-4 py-2.5 text-center">
                <button type="button" @click="removeItem(idx)" class="text-text-3 hover:text-red-500 transition-colors">✕</button>
              </td>
            </tr>
            <tr v-if="form.items.length === 0">
                        
                        <td colspan="100%" class="px-4 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-12 h-12 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center mb-3">
                                    <Icon name="box" class="w-6 h-6" />
                                </div>
                                <h3 class="text-sm font-semibold text-slate-700 mb-1">Sin productos</h3>
                                <p class="text-xs text-slate-500 mb-4">Usa el botón "Agregar producto" para empezar la orden de compra.</p>
                                <button type="button" @click="showProductSearch = true" class="btn btn-primary btn-sm"><Icon name="plus" /> Agregar</button>
                            </div>
                        </td>
</tr>
          </tbody>
          <tfoot v-if="form.items.length > 0" class="bg-gray-50 border-t border-border">
            <tr>
              <td colspan="4" class="px-4 py-2.5 text-right text-sm font-medium text-text-2">{{ t('workOrders.form.total') }}:</td>
              <td class="px-4 py-2.5 text-right font-bold text-text">${{ orderTotal }}</td>
              <td></td>
            </tr>
          </tfoot>
        </table></div>
      </div>

      <div v-if="form.errors.items" class="text-sm text-red-500">{{ form.errors.items }}</div>

      <div class="flex justify-end gap-3">
        <Link :href="route('purchases.index')" class="btn btn-ghost">{{ t('common.cancel') }}</Link>
        <button type="submit" :disabled="form.processing || form.items.length === 0" class="btn btn-primary">
          {{ form.processing ? t('common.saving') : t('purchases.newOrder') }}
        </button>
      </div>
    </form>

    <!-- Product search modal -->
    <transition name="modal">
    <div v-if="showProductSearch" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
      <div class="card w-full max-w-lg space-y-3">
        <div class="flex justify-between items-center">
          <h3 class="font-semibold text-text">{{ t('purchases.addProduct') }}</h3>
          <button @click="showProductSearch = false" class="text-text-3 hover:text-text">✕</button>
        </div>
        <input
          v-model="productQuery"
          type="text"
          class="input"
          :placeholder="t('purchases.searchProducts')"
          @input="searchProducts"
          autofocus
        />
        <div class="max-h-64 overflow-y-auto divide-y divide-border">
          <button
            v-for="p in productResults"
            :key="p.id"
            type="button"
            class="w-full text-left px-3 py-2.5 hover:bg-brand-50 transition-colors flex justify-between items-center"
            @click="addProduct(p)"
          >
            <div>
              <div class="text-sm font-medium text-text">{{ p.name }}</div>
              <div v-if="p.size" class="text-xs font-mono text-brand-600">{{ p.size }}</div>
            </div>
            <div class="text-right">
              <div class="text-xs text-text-2">{{ t('inventory.stock') }}: {{ p.stock }}</div>
              <div class="text-xs text-text-3">${{ p.price }}</div>
            </div>
          </button>
          <div v-if="productResults.length === 0 && productQuery.length > 1" class="px-3 py-4 text-center text-text-3 text-sm">
            {{ t('purchases.noProductsFound') }}
          </div>
        </div>
      </div>
    </div>
    </transition>
  </AppLayout>
</template>

<script setup>
import Icon from '@/Components/Icon.vue';

import { ref, computed } from 'vue'
import { Link, useForm, router } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import AppLayout from '@/Layouts/AppLayout.vue'

const { t } = useI18n()

const props = defineProps({
  orderType:      { type: String, default: 'regular' },
  suggestedItems: { type: Array, default: () => [] },
  suppliers:      Array,
  nextNumber:     String,
})

const showProductSearch = ref(false)
const productQuery      = ref('')
const productResults    = ref([])

const form = useForm({
  type:        props.orderType,
  supplier_id: '',
  ordered_at:  new Date().toISOString().split('T')[0],
  notes:       '',
  items:       props.suggestedItems.map(i => ({
    product_id:  i.product_id,
    product:     i.product,
    qty_ordered: i.qty_ordered,
    unit_cost:   parseFloat(i.unit_cost),
    notes:       '',
  })),
})

const orderTotal = computed(() =>
  form.items.reduce((s, i) => s + i.qty_ordered * i.unit_cost, 0).toFixed(2)
)

let searchTimer = null
async function searchProducts() {
  clearTimeout(searchTimer)
  if (productQuery.value.length < 2) { productResults.value = []; return }
  searchTimer = setTimeout(async () => {
    const res = await fetch(route('products.search') + '?q=' + encodeURIComponent(productQuery.value))
    productResults.value = await res.json()
  }, 250)
}

function addProduct(p) {
  const existing = form.items.find(i => i.product_id === p.id)
  if (existing) { existing.qty_ordered++; showProductSearch.value = false; return }

  form.items.push({
    product_id:  p.id,
    product:     { id: p.id, name: p.name, tire_size: p.size, stock_qty: p.stock, stock_min: 0 },
    qty_ordered: 1,
    unit_cost:   parseFloat(p.price) || 0,
    notes:       '',
  })
  showProductSearch.value = false
  productQuery.value = ''
  productResults.value = []
}

function removeItem(idx) { form.items.splice(idx, 1) }

function submit() {
  form.post(route('purchases.store'))
}
</script>
