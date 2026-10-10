<template>
  <AppLayout>
    <template #header>
      <div class="page-header">
        <div>
          <div class="flex items-center gap-2 text-sm mb-1">
            <Link :href="route('purchases.index')" class="text-slate-400 hover:text-slate-700">{{ t('nav.purchases') }}</Link>
            <span class="text-slate-300">/</span>
            <span class="font-mono text-slate-600">{{ order.number }}</span>
          </div>
          <h1 class="page-title font-mono">{{ order.number }}</h1>
          <span :class="['badge mt-1', statusBadge(order.status)]">{{ t('purchases.statuses.' + order.status) }}</span>
        </div>
        <div class="flex gap-2 flex-wrap justify-end">
          <button v-if="order.status === 'draft'" @click="setStatus('sent')" class="btn btn-ghost text-xs">
            {{ t('purchases.markSent') }}
          </button>
          <button v-if="order.status === 'draft'" @click="confirmDelete" class="btn btn-ghost text-xs text-red-500 hover:bg-red-50 hover:border-red-200">
            <Icon name="delete" />
            {{ t('common.delete') }}
          </button>
          <button v-if="order.status === 'draft'" @click="setStatus('cancelled')" class="btn btn-ghost text-xs">
            {{ t('purchases.cancelOrder') }}
          </button>
          <button
            v-if="['draft','sent','partial'].includes(order.status)"
            @click="showReceive = true"
            class="btn btn-primary"
          >
            {{ t('purchases.receive') }}
          </button>
        </div>
      </div>
    </template>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
      <!-- Left: Info + items -->
      <div class="lg:col-span-2 space-y-5">
        <!-- Items table -->
        <div class="card p-0 overflow-x-auto">
          <div class="px-4 py-3 border-b border-border">
            <h2 class="font-semibold text-text">{{ t('purchases.orderItems') }}</h2>
          </div>
          <div class="overflow-x-auto"><table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs text-text-3 uppercase">
              <tr>
                <th class="px-4 py-2 text-left">{{ t('purchases.product') }}</th>
                <th class="px-4 py-2 text-center">{{ t('purchases.ordered') }}</th>
                <th class="px-4 py-2 text-center">{{ t('purchases.received') }}</th>
                <th class="px-4 py-2 text-right">{{ t('purchases.unitCost') }}</th>
                <th class="px-4 py-2 text-right">{{ t('workOrders.form.subtotal') }}</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-border">
              <tr v-for="item in order.items" :key="item.id" class="hover:bg-gray-50">
                <td class="px-4 py-3">
                  <div class="font-medium text-text">{{ productName(item.product) }}</div>
                  <div v-if="item.product?.type === 'tire'" class="text-xs font-mono text-brand-600">
                    {{ tireSize(item.product) }}
                  </div>
                  <div v-if="item.notes" class="text-xs text-text-3 mt-0.5">{{ item.notes }}</div>
                </td>
                <td class="px-4 py-3 text-center font-mono">{{ item.qty_ordered }}</td>
                <td class="px-4 py-3 text-center">
                  <span :class="item.qty_received >= item.qty_ordered ? 'text-emerald-600 font-medium' : item.qty_received > 0 ? 'text-amber-600 font-medium' : 'text-text-3'">
                    {{ item.qty_received }}
                  </span>
                  <span class="text-text-3 text-xs"> / {{ item.qty_ordered }}</span>
                </td>
                <td class="px-4 py-3 text-right">${{ fmt(item.unit_cost) }}</td>
                <td class="px-4 py-3 text-right font-medium">${{ (item.qty_ordered * item.unit_cost).toFixed(2) }}</td>
              </tr>
            </tbody>
            <tfoot class="bg-gray-50 border-t border-border">
              <tr>
                <td colspan="4" class="px-4 py-2.5 text-right text-sm font-medium text-text-2">{{ t('workOrders.form.total') }}:</td>
                <td class="px-4 py-2.5 text-right font-bold text-text">${{ orderTotal }}</td>
              </tr>
            </tfoot>
          </table></div>
        </div>

        <!-- Attachment -->
        <div class="card">
          <h2 class="section-title mb-3">{{ t('purchases.attachSection') }}</h2>
          <div v-if="order.attachment" class="flex items-center justify-between p-3 rounded-lg bg-gray-50 border border-border mb-3">
            <div class="flex items-center gap-2">
              <span class="text-2xl">📄</span>
              <span class="text-sm text-text-2">{{ t('purchases.attachedDoc') }}</span>
            </div>
            <a :href="`/storage/${order.attachment}`" target="_blank" class="btn-sm btn-ghost">
                                <Icon name="view" />
                                {{ t('common.view') }}
                            </a>
          </div>
          <form @submit.prevent="uploadDoc">
            <div class="flex gap-2 items-end">
              <div class="flex-1">
                <label class="label text-xs">{{ order.attachment ? t('purchases.replaceDoc') : t('purchases.attachInvoice') }}</label>
                <input ref="fileInput" type="file" accept=".pdf,.jpg,.jpeg,.png,.webp" class="input text-sm" @change="fileSelected = true" />
              </div>
              <button type="submit" :disabled="!fileSelected || uploading" class="btn btn-ghost text-sm shrink-0">
                {{ t('purchases.upload') }}
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Right: Summary -->
      <div class="space-y-4">
        <div class="card space-y-3">
          <h2 class="section-title">{{ t('purchases.orderInfo') }}</h2>
          <dl class="space-y-2 text-sm">
            <div class="flex justify-between">
              <dt class="text-text-2">{{ t('purchases.number') }}</dt>
              <dd class="font-mono font-medium">{{ order.number }}</dd>
            </div>
            <div class="flex justify-between">
              <dt class="text-text-2">{{ t('purchases.orderType') }}</dt>
              <dd>{{ order.type === 'suggested' ? '⚡ ' + t('purchases.suggestedBadge') : t('common.all') }}</dd>
            </div>
            <div class="flex justify-between">
              <dt class="text-text-2">{{ t('common.status') }}</dt>
              <dd><span :class="statusBadge(order.status)">{{ t('purchases.statuses.' + order.status) }}</span></dd>
            </div>
            <div v-if="order.supplier" class="flex justify-between">
              <dt class="text-text-2">{{ t('purchases.supplier') }}</dt>
              <dd class="font-medium text-right">{{ order.supplier.name }}</dd>
            </div>
            <div v-if="order.supplier?.phone" class="flex justify-between">
              <dt class="text-text-2">{{ t('purchases.phone') }}</dt>
              <dd class="text-text-2">{{ order.supplier.phone }}</dd>
            </div>
            <hr class="border-border" />
            <div class="flex justify-between">
              <dt class="text-text-2">{{ t('purchases.orderItemCount') }}</dt>
              <dd>{{ order.items?.length ?? 0 }}</dd>
            </div>
            <div class="flex justify-between">
              <dt class="text-text-2">{{ t('workOrders.form.total') }}</dt>
              <dd class="font-bold text-text">${{ orderTotal }}</dd>
            </div>
            <hr class="border-border" />
            <div class="flex justify-between">
              <dt class="text-text-2">{{ t('purchases.createdBy') }}</dt>
              <dd class="text-text-2">{{ order.created_by?.name }}</dd>
            </div>
            <div v-if="order.received_by" class="flex justify-between">
              <dt class="text-text-2">{{ t('purchases.receivedBy') }}</dt>
              <dd class="text-text-2">{{ order.received_by?.name }}</dd>
            </div>
            <div v-if="order.ordered_at" class="flex justify-between">
              <dt class="text-text-2">{{ t('purchases.orderedAt') }}</dt>
              <dd class="text-text-2 text-xs">{{ fmtDate(order.ordered_at) }}</dd>
            </div>
            <div v-if="order.received_at" class="flex justify-between">
              <dt class="text-text-2">{{ t('purchases.receivedAt') }}</dt>
              <dd class="text-text-2 text-xs">{{ fmtDate(order.received_at) }}</dd>
            </div>
          </dl>
          <div v-if="order.notes" class="text-sm text-text-2 bg-gray-50 rounded p-2">{{ order.notes }}</div>
        </div>
      </div>
    </div>

    <!-- Receive items modal -->
    <div v-if="showReceive" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
      <div class="card w-full max-w-2xl space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center">
          <h3 class="font-semibold text-text text-lg">{{ t('purchases.receive') }}</h3>
          <button @click="showReceive = false" class="text-text-3 hover:text-text">✕</button>
        </div>
        <p class="text-sm text-text-2">{{ t('purchases.receiveInstructions') }}</p>

        <div class="overflow-x-auto"><table class="w-full text-sm">
          <thead class="text-xs text-text-3 uppercase border-b border-border">
            <tr>
              <th class="pb-2 text-left">{{ t('purchases.product') }}</th>
              <th class="pb-2 text-center w-20">{{ t('purchases.ordered') }}</th>
              <th class="pb-2 text-center w-20">{{ t('purchases.prevReceived') }}</th>
              <th class="pb-2 text-center w-28">{{ t('purchases.receiving') }}</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border">
            <tr v-for="item in receiveForm" :key="item.id" class="hover:bg-gray-50">
              <td class="py-2.5 pr-3">
                <div class="font-medium text-text">{{ productName(order.items.find(i=>i.id===item.id)?.product) }}</div>
                <div v-if="order.items.find(i=>i.id===item.id)?.product?.type === 'tire'" class="text-xs font-mono text-brand-600">
                  {{ tireSize(order.items.find(i=>i.id===item.id)?.product) }}
                </div>
              </td>
              <td class="py-2.5 text-center text-text-2">{{ order.items.find(i=>i.id===item.id)?.qty_ordered }}</td>
              <td class="py-2.5 text-center text-text-3">{{ order.items.find(i=>i.id===item.id)?.qty_received }}</td>
              <td class="py-2.5">
                <input
                  v-model.number="item.qty_received"
                  type="number"
                  min="0"
                  :max="order.items.find(i=>i.id===item.id)?.qty_ordered"
                  class="input text-center py-1"
                />
              </td>
            </tr>
          </tbody>
        </table></div>

        <div class="flex justify-end gap-2 pt-2 border-t border-border">
          <button @click="showReceive = false" class="btn btn-ghost">{{ t('common.cancel') }}</button>
          <button @click="submitReceive" class="btn btn-primary">{{ t('purchases.confirm') }}</button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { confirmDelete as confirmDeleteAlert, confirmAction } from '@/utils/alerts';


import Icon from '@/Components/Icon.vue';
import { ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import AppLayout from '@/Layouts/AppLayout.vue'

const { t } = useI18n()

const props = defineProps({ order: Object })

const showReceive = ref(false)
const fileInput   = ref(null)
const fileSelected = ref(false)
const uploading    = ref(false)

// Pre-fill receive form with current received quantities
const receiveForm = ref(
  (props.order.items || []).map(i => ({ id: i.id, qty_received: i.qty_ordered }))
)

const orderTotal = computed(() =>
  (props.order.items || []).reduce((s, i) => s + i.qty_ordered * i.unit_cost, 0).toFixed(2)
)

function statusBadge(s) {
  const map = { draft: 'badge-gray', sent: 'badge-blue', partial: 'badge-amber', received: 'badge-green', cancelled: 'badge-red' }
  return map[s] || 'badge-gray'
}
function productName(p) { if (!p) return '—'; return p.type === 'tire' ? `${p.tire_brand} ${p.tire_line || ''}`.trim() : p.name }
function tireSize(p)    { if (!p?.tire_width) return null; return `${p.tire_width}/${p.tire_ratio}R${p.tire_diameter}` }
function fmt(v)         { return Number(v).toFixed(2) }
function fmtDate(d)     { return new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) }

async function setStatus(status) {
  const msgs = {
    cancelled: ['¿Cancelar esta orden de compra?', 'Esta acción no se puede deshacer fácilmente.', 'Sí, cancelar'],
    received:  ['¿Marcar como recibida?', 'Se actualizará el inventario con los productos indicados.', 'Sí, confirmar'],
    sent:      ['¿Marcar orden como enviada?', '', 'Sí, enviada'],
  };
  const [title, text, btn] = msgs[status] ?? [`¿Cambiar estado a ${status}?`, '', 'Sí, continuar'];
  if (!await confirmAction(title, text, btn)) return;
  router.patch(route('purchases.status', props.order.id), { status });
}

async function confirmDelete() {
  if (await confirmDeleteAlert(`${t('common.delete')} ${props.order.number}?`)) {
    router.delete(route('purchases.destroy', props.order.id))
  }
}

async function submitReceive() {
  const ok = await confirmAction(
    '¿Confirmar recepción de mercancía?',
    'Se actualizará el stock de los productos recibidos.',
    'Sí, confirmar recepción'
  );
  if (!ok) return;
  router.post(route('purchases.receive', props.order.id), {
    items: receiveForm.value,
  }, {
    onSuccess: () => { showReceive.value = false },
  });
}

function uploadDoc() {
  if (!fileInput.value?.files[0]) return
  uploading.value = true
  const data = new FormData()
  data.append('attachment', fileInput.value.files[0])
  router.post(route('purchases.attachment', props.order.id), data, {
    forceFormData: true,
    onFinish: () => { uploading.value = false; fileSelected.value = false },
  })
}
</script>
