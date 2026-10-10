<template>
  <AppLayout>
    <template #header>
      <div class="flex items-center gap-2 text-sm">
        <Link :href="route('products.index', { type: productType })" class="text-slate-400 hover:text-slate-700">
          {{ productType === 'tire' ? t('inventory.tires') : t('inventory.materials') }}
        </Link>
        <span class="text-slate-300">/</span>
        <span class="font-semibold text-slate-800">
          {{ isEdit ? (productType === 'tire' ? t('inventory.editTire') : t('inventory.editItem')) : (productType === 'tire' ? t('inventory.newTire') : t('inventory.newItem')) }}
        </span>
      </div>
    </template>

    <form @submit.prevent="submit" class="max-w-3xl space-y-5">
      <!-- TIRE FIELDS -->
      <template v-if="productType === 'tire'">
        <div class="card space-y-5">
          <h2 class="section-title">{{ t('inventory.tireDetails') }}</h2>

          <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div>
              <label class="label">{{ t('inventory.width') }} <span class="text-red-500">*</span></label>
              <input v-model="form.tire_width" type="text" class="input font-mono" placeholder="215" required />
            </div>
            <div>
              <label class="label">{{ t('inventory.ratio') }} <span class="text-red-500">*</span></label>
              <input v-model="form.tire_ratio" type="text" class="input font-mono" placeholder="55" required />
            </div>
            <div>
              <label class="label">{{ t('inventory.diameter') }} <span class="text-red-500">*</span></label>
              <input v-model="form.tire_diameter" type="text" class="input font-mono" placeholder="17" required />
            </div>
            <div class="flex items-end">
              <span v-if="tireSizePreview" class="px-3 py-2 bg-brand-50 text-brand-700 rounded text-sm font-mono font-medium w-full text-center">
                {{ tireSizePreview }}
              </span>
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="label">{{ t('inventory.brand') }} <span class="text-red-500">*</span></label>
              <input v-model="form.tire_brand" type="text" class="input" placeholder="Nexen, Michelin, Goodyear…" required />
            </div>
            <div>
              <label class="label">{{ t('inventory.line') }}</label>
              <input v-model="form.tire_line" type="text" class="input" placeholder="N-Priz AH8, Primacy 4…" />
            </div>
            <div>
              <label class="label">{{ t('inventory.tireType') }}</label>
              <select v-model="form.tire_type" class="input">
                <option value="">Select…</option>
                <option>All-Season</option>
                <option>Summer</option>
                <option>Winter</option>
                <option>All-Terrain</option>
                <option>Performance</option>
                <option>Highway</option>
                <option>Mud-Terrain</option>
              </select>
            </div>
            <div>
              <label class="label">{{ t('inventory.nameLabel') }}</label>
              <input v-model="form.name" type="text" class="input" placeholder="Auto-generated if empty" />
              <p class="text-xs text-text-3 mt-1">{{ t('inventory.autoNameHint') }}</p>
            </div>
          </div>
        </div>
      </template>

      <!-- MATERIAL FIELDS -->
      <template v-else>
        <div class="card space-y-4">
          <h2 class="section-title">{{ t('inventory.itemDetails') }}</h2>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
              <label class="label">{{ t('inventory.columns.name') }} <span class="text-red-500">*</span></label>
              <input v-model="form.name" type="text" class="input" required />
              <p v-if="form.errors.name" class="field-error">{{ form.errors.name }}</p>
            </div>
            <div>
              <label class="label">{{ t('inventory.skuPartNumber') }}</label>
              <input v-model="form.sku" type="text" class="input font-mono" />
            </div>
            <div>
              <label class="label">{{ t('inventory.barcode') }}</label>
              <input v-model="form.barcode" type="text" class="input font-mono" />
            </div>
            <div>
              <label class="label">{{ t('inventory.unit') }}</label>
              <select v-model="form.unit" class="input">
                <option value="unit">Unit</option>
                <option value="pair">Pair</option>
                <option value="set">Set</option>
                <option value="box">Box</option>
                <option value="liter">Liter</option>
                <option value="kg">Kg</option>
                <option value="oz">Oz</option>
              </select>
            </div>
            <div>
              <label class="label">{{ t('inventory.category') }}</label>
              <select v-model="form.category_id" class="input">
                <option value="">{{ t('common.none') }}</option>
                <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
              </select>
            </div>
          </div>
        </div>
      </template>

      <!-- Common fields -->
      <div class="card space-y-4">
        <h2 class="section-title">{{ t('inventory.pricingStock') }}</h2>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
          <div>
            <label class="label">{{ t('inventory.costPrice') }} <span class="text-red-500">*</span></label>
            <div class="relative">
              <span class="absolute inset-y-0 left-3 flex items-center text-text-3">$</span>
              <input v-model="form.cost_price" type="number" step="0.01" min="0" class="input input-currency" required />
            </div>
          </div>
          <div>
            <label class="label">{{ t('inventory.salePrice') }} <span class="text-red-500">*</span></label>
            <div class="relative">
              <span class="absolute inset-y-0 left-3 flex items-center text-text-3">$</span>
              <input v-model="form.sale_price" type="number" step="0.01" min="0" class="input input-currency" required />
            </div>
          </div>
          <div>
            <label class="label">{{ t('inventory.initStock') }}</label>
            <input v-model="form.stock_qty" type="number" min="0" class="input" />
          </div>
          <div>
            <label class="label">{{ t('inventory.minAlert') }}</label>
            <input v-model="form.stock_min" type="number" min="0" class="input" />
          </div>
        </div>
      </div>

      <div class="card space-y-4">
        <h2 class="section-title">{{ t('inventory.additionalInfo') }}</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="label">{{ t('inventory.supplier') }}</label>
            <select v-model="form.supplier_id" class="input">
              <option value="">{{ t('common.none') }}</option>
              <option v-for="s in suppliers" :key="s.id" :value="s.id">{{ s.name }}</option>
            </select>
          </div>
          <div v-if="productType === 'tire'">
            <label class="label">{{ t('inventory.category') }}</label>
            <select v-model="form.category_id" class="input">
              <option value="">{{ t('common.none') }}</option>
              <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
            </select>
          </div>
          <div>
            <label class="label">{{ t('inventory.location') }}</label>
            <input v-model="form.location" type="text" class="input" placeholder="A1, Bay 3…" />
          </div>
          <div class="flex items-end">
            <label class="flex items-center gap-2 cursor-pointer mb-0.5">
              <input v-model="form.is_active" type="checkbox" class="rounded" />
              <span class="text-sm text-text">{{ t('inventory.activeProduct') }}</span>
            </label>
          </div>
          <div class="sm:col-span-2">
            <label class="label">{{ t('common.notes') }}</label>
            <textarea v-model="form.notes" rows="2" class="input"></textarea>
          </div>
        </div>
      </div>

      <div class="flex justify-end gap-3">
        <Link :href="route('products.index', { type: productType })" class="btn btn-ghost">{{ t('common.cancel') }}</Link>
        <button type="submit" :disabled="form.processing" class="btn btn-primary">
          {{ form.processing ? t('common.saving') : (isEdit ? t('common.save') : t('common.create')) }}
        </button>
      </div>
    </form>
  </AppLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import AppLayout from '@/Layouts/AppLayout.vue'

const { t } = useI18n()

const props = defineProps({
  product:     { type: Object, default: null },
  productType: { type: String, default: 'tire' },
  suppliers:   Array,
  categories:  Array,
})

const isEdit = computed(() => !!props.product)

const form = useForm({
  type:          props.product?.type || props.productType,
  name:          props.product?.name || '',
  sku:           props.product?.sku || '',
  barcode:       props.product?.barcode || '',
  unit:          props.product?.unit || 'unit',
  description:   props.product?.description || '',
  supplier_id:   props.product?.supplier_id || '',
  category_id:   props.product?.category_id || '',
  tire_brand:    props.product?.tire_brand || '',
  tire_line:     props.product?.tire_line || '',
  tire_width:    props.product?.tire_width || '',
  tire_ratio:    props.product?.tire_ratio || '',
  tire_diameter: props.product?.tire_diameter || '',
  tire_type:     props.product?.tire_type || '',
  cost_price:    props.product?.cost_price || '0.00',
  sale_price:    props.product?.sale_price || '0.00',
  stock_qty:     props.product?.stock_qty ?? 0,
  stock_min:     props.product?.stock_min ?? 0,
  location:      props.product?.location || '',
  notes:         props.product?.notes || '',
  is_active:     props.product?.is_active ?? true,
})

const tireSizePreview = computed(() => {
  if (!form.tire_width || !form.tire_ratio || !form.tire_diameter) return null
  return `${form.tire_width}/${form.tire_ratio}R${form.tire_diameter}`
})

function submit() {
  // Auto-fill name for tires
  if (props.productType === 'tire' && !form.name) {
    form.name = `${form.tire_brand} ${form.tire_line || ''} ${tireSizePreview.value || ''}`.trim()
  }
  if (isEdit.value) {
    form.put(route('products.update', props.product.id))
  } else {
    form.post(route('products.store'))
  }
}
</script>
