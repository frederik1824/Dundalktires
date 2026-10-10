<script setup>
import { confirmAction } from '@/utils/alerts';

import Icon from '@/Components/Icon.vue';
import { ref, computed } from 'vue';
import { Link, useForm, router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';
import { usePermissions } from '@/composables/usePermissions.js';

const { t } = useI18n();
const { can } = usePermissions();

const props = defineProps({
    invoice:        Object,
    paymentMethods: Object,
    cashOpen:       { type: Boolean, default: false },
});

const totalRefunded = computed(() =>
    (props.invoice.credit_notes ?? []).reduce((s, cn) => s + parseFloat(cn.amount), 0)
);

const statusColors = { paid: 'badge-green', partial: 'badge-amber', draft: 'badge-gray', void: 'badge-red' };

const payForm = useForm({
    method:    'cash',
    amount:    '',
    reference: '',
    notes:     '',
});

const showPayModal = ref(false);

function addPayment() {
    payForm.post(`/invoices/${props.invoice.id}/payment`, {
        onSuccess: () => { showPayModal.value = false; payForm.reset(); },
    });
}

async function voidInvoice() {
    if (await confirmAction(t('invoices.pos.voidBtn') + '?', '', t('common.yes'))) {
        router.patch(`/invoices/${props.invoice.id}/void`);
    }
}

const balance = (parseFloat(props.invoice.total) - parseFloat(props.invoice.amount_paid)).toFixed(2);
</script>

<template>
    <AppLayout>
        <template #header>
            <div class="page-header">
                <div>
                    <nav class="breadcrumb mb-1">
                        <Link href="/invoices" class="breadcrumb-link">{{ t('invoices.title') }}</Link>
                        <span class="breadcrumb-sep">/</span>
                        <span class="breadcrumb-current">{{ invoice.number }}</span>
                    </nav>
                    <div class="flex items-center gap-3">
                        <h1 class="page-title">{{ invoice.number }}</h1>
                        <span :class="['badge', statusColors[invoice.status] ?? 'badge-gray']">{{ t('invoices.statuses.' + invoice.status) }}</span>
                    </div>
                </div>
                <div class="flex gap-2 flex-wrap">
                    <!-- Thermal receipt print buttons -->
                    <div class="flex rounded-lg overflow-hidden border border-border">
                        <a :href="`/invoices/${invoice.id}/receipt?width=80`" target="_blank"
                            class="flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-text-2 hover:bg-slate-50 transition-colors border-r border-border">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                            </svg>
                            80mm
                        </a>
                        <a :href="`/invoices/${invoice.id}/receipt?width=58`" target="_blank"
                            class="flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-text-2 hover:bg-slate-50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                            </svg>
                            58mm
                        </a>
                    </div>
                    <a :href="`/invoices/${invoice.id}/pdf`" target="_blank" class="btn btn-ghost">
                        <Icon name="download" class="w-4 h-4 mr-1" />PDF
                    </a>
                    <Link v-if="can('manage_sales') && parseFloat(invoice.amount_paid) > totalRefunded"
                        :href="`/invoices/${invoice.id}/credit-note`"
                        class="btn btn-ghost text-amber-600 hover:text-amber-700 hover:bg-amber-50 border-amber-200">
                        {{ t('invoices.pos.creditNotesSection') }}
                    </Link>
                    <button v-if="invoice.status !== 'void' && parseFloat(balance) > 0"
                        @click="showPayModal = true" class="btn btn-primary"
                        :disabled="!cashOpen"
                        :title="!cashOpen ? 'La caja no está abierta para hoy' : undefined">
                        {{ t('invoices.pos.addPaymentBtn') }}
                    </button>
                    <button v-if="invoice.status !== 'void'"
                        @click="voidInvoice" class="btn btn-ghost text-red-500 hover:text-red-700 hover:bg-red-50 border-red-200">{{ t('invoices.pos.voidBtn') }}</button>
                </div>
            </div>
        </template>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            <div class="lg:col-span-2 space-y-5">
                <!-- Customer info -->
                <div class="card grid grid-cols-2 gap-4">
                    <div>
                        <p class="label">{{ t('invoices.pos.customer') }}</p>
                        <p class="font-medium text-text">{{ invoice.customer?.name ?? t('common.walkIn') }}</p>
                        <p v-if="invoice.customer?.phone" class="text-sm text-text-2">{{ invoice.customer.phone }}</p>
                    </div>
                    <div v-if="invoice.work_order">
                        <p class="label">{{ t('invoices.pos.workOrderLabel') }}</p>
                        <Link :href="`/work-orders/${invoice.work_order.id}`" class="font-mono text-brand-600 hover:underline">
                            {{ invoice.work_order.number }}
                        </Link>
                        <p v-if="invoice.work_order.vehicle" class="text-xs text-text-2 mt-0.5">
                            {{ invoice.work_order.vehicle.year }} {{ invoice.work_order.vehicle.make }} {{ invoice.work_order.vehicle.model }}
                        </p>
                    </div>
                </div>

                <!-- Line items -->
                <div class="card">
                    <h3 class="section-title mb-3">{{ t('invoices.pos.itemsSection') }}</h3>
                    <div class="overflow-x-auto"><table class="w-full text-sm">
                        <thead><tr class="border-b border-border">
                            <th class="pb-2 text-left text-text-2 font-medium">{{ t('invoices.pos.item') }}</th>
                            <th class="pb-2 text-center text-text-2 font-medium w-12">{{ t('invoices.pos.qty') }}</th>
                            <th class="pb-2 text-right text-text-2 font-medium w-24">{{ t('invoices.pos.price') }}</th>
                            <th class="pb-2 text-right text-text-2 font-medium w-24">{{ t('invoices.pos.total') }}</th>
                        </tr></thead>
                        <tbody class="divide-y divide-border">
                            <tr v-for="item in invoice.items" :key="item.id">
                                <td class="py-2 text-text">
                                    {{ item.name }}
                                    <span class="ml-1 badge" :class="{ service: 'badge-blue', product: 'badge-purple', other: 'badge-gray' }[item.type] ?? 'badge-gray'">{{ item.type }}</span>
                                </td>
                                <td class="py-2 text-center text-text-2">{{ item.qty }}</td>
                                <td class="py-2 text-right font-mono text-text-2">${{ item.unit_price }}</td>
                                <td class="py-2 text-right font-mono">${{ item.total }}</td>
                            </tr>
                        </tbody>
                    </table></div>
                </div>

                <!-- Payments -->
                <div class="card">
                    <h3 class="section-title mb-3">{{ t('invoices.pos.paymentsSection') }}</h3>
                    <div class="overflow-x-auto" v-if="invoice.payments.length"><table class="w-full text-sm whitespace-nowrap">
                        <thead><tr class="border-b border-border">
                            <th class="pb-2 pr-4 text-left text-text-2 font-medium">{{ t('invoices.pos.method') }}</th>
                            <th class="pb-2 pr-4 text-left text-text-2 font-medium">{{ t('invoices.pos.reference') }}</th>
                            <th class="pb-2 pr-4 text-left text-text-2 font-medium">{{ t('invoices.pos.receivedBy') }}</th>
                            <th class="pb-2 pr-4 text-left text-text-2 font-medium">{{ t('invoices.columns.date') }}</th>
                            <th class="pb-2 text-right text-text-2 font-medium">{{ t('invoices.pos.modal.amount') }}</th>
                        </tr></thead>
                        <tbody class="divide-y divide-border">
                            <tr v-for="pay in invoice.payments" :key="pay.id">
                                <td class="py-2 pr-4 text-text capitalize">{{ paymentMethods[pay.method] ?? pay.method }}</td>
                                <td class="py-2 pr-4 text-text-2">{{ pay.reference ?? '—' }}</td>
                                <td class="py-2 pr-4 text-text-2">{{ pay.received_by?.name ?? '—' }}</td>
                                <td class="py-2 pr-4 text-text-2">{{ new Date(pay.paid_at).toLocaleDateString() }}</td>
                                <td class="py-2 text-right font-mono font-medium">${{ pay.amount }}</td>
                            </tr>
                        </tbody>
                    </table></div>
                    <p v-else class="text-sm text-text-3 italic">{{ t('invoices.pos.noPayments') }}</p>
                </div>
            </div>

            <!-- Right sidebar -->
            <div class="space-y-5">
                <div class="card space-y-2">
                    <h3 class="section-title">{{ t('invoices.pos.summarySection') }}</h3>
                    <div class="flex justify-between text-sm"><span class="text-text-2">{{ t('invoices.pos.subtotal') }}</span><span class="font-mono">${{ invoice.subtotal }}</span></div>
                    <div v-if="parseFloat(invoice.tax)" class="flex justify-between text-sm"><span class="text-text-2">{{ t('invoices.pos.taxLabel') }}</span><span class="font-mono">${{ invoice.tax }}</span></div>
                    <div v-if="parseFloat(invoice.discount)" class="flex justify-between text-sm"><span class="text-text-2">{{ t('invoices.pos.discountLabel') }}</span><span class="font-mono text-green-600">-${{ invoice.discount }}</span></div>
                    <div class="flex justify-between font-bold text-text border-t border-border pt-2 text-base">
                        <span>{{ t('invoices.pos.total') }}</span><span class="font-mono">${{ invoice.total }}</span>
                    </div>
                    <div class="flex justify-between text-sm"><span class="text-text-2">{{ t('invoices.pos.paidLabel') }}</span><span class="font-mono text-green-600">${{ invoice.amount_paid }}</span></div>
                    <div :class="['flex justify-between text-sm font-semibold', parseFloat(balance) > 0 ? 'text-amber-600' : 'text-green-600']">
                        <span>{{ t('invoices.pos.balanceDueLabel') }}</span><span class="font-mono">${{ balance }}</span>
                    </div>
                </div>

                <div class="card text-xs text-text-2 space-y-1">
                    <p>{{ t('invoices.columns.date') }}: {{ new Date(invoice.created_at).toLocaleDateString() }}</p>
                    <p v-if="invoice.notes">{{ t('common.notes') }}: {{ invoice.notes }}</p>
                </div>

                <!-- Credit Notes -->
                <div v-if="invoice.credit_notes?.length" class="card">
                    <h3 class="section-title mb-3">{{ t('invoices.pos.creditNotesSection') }}</h3>
                    <div v-for="cn in invoice.credit_notes" :key="cn.id"
                        class="flex justify-between items-start py-2 border-b last:border-0 text-sm"
                        style="border-color:var(--color-border);">
                        <div>
                            <p class="font-mono text-xs text-brand-600">{{ cn.number }}</p>
                            <p class="text-text-2 text-xs capitalize">{{ cn.reason.replace('_', ' ') }} · {{ cn.refund_method }}</p>
                            <p v-if="cn.notes" class="text-text-3 text-xs mt-0.5">{{ cn.notes }}</p>
                        </div>
                        <span class="font-mono font-semibold text-amber-600">-${{ parseFloat(cn.amount).toFixed(2) }}</span>
                    </div>
                    <div class="flex justify-between font-semibold text-sm pt-2 mt-1" style="border-top:1px solid var(--color-border);">
                        <span>{{ t('invoices.pos.totalRefunded') }}</span>
                        <span class="font-mono text-amber-600">-${{ totalRefunded.toFixed(2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Modal -->
        <div v-if="showPayModal" class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-sm p-6 space-y-4">
                <h3 class="font-semibold text-text text-lg">{{ t('invoices.pos.modal.title') }}</h3>
                <div class="text-sm text-text-2 bg-slate-50 rounded-lg px-3 py-2">
                    {{ t('invoices.pos.modal.balanceDue') }} <span class="font-mono font-semibold text-text">${{ balance }}</span>
                </div>
                <div>
                    <label class="label">{{ t('invoices.pos.modal.method') }}</label>
                    <select v-model="payForm.method" class="input w-full">
                        <option v-for="(label, key) in paymentMethods" :key="key" :value="key">{{ label }}</option>
                    </select>
                </div>
                <div>
                    <label class="label">{{ t('invoices.pos.modal.amount') }}</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-text-3">$</span>
                        <input v-model="payForm.amount" type="number" step="0.01" min="0.01" class="input input-currency w-full" :placeholder="balance" />
                    </div>
                    <p v-if="payForm.errors.amount" class="field-error">{{ payForm.errors.amount }}</p>
                </div>
                <div v-if="payForm.method !== 'cash'">
                    <label class="label">{{ t('invoices.pos.modal.reference') }}</label>
                    <input v-model="payForm.reference" type="text" class="input w-full"
                        :placeholder="payForm.method === 'card' ? t('invoices.pos.modal.lastFour') : t('invoices.pos.modal.refConfirm')" />
                </div>
                <div class="flex gap-2 pt-2">
                    <button type="button" @click="addPayment" :disabled="payForm.processing || !cashOpen" class="btn btn-primary flex-1">
                        {{ payForm.processing ? t('invoices.pos.modal.saving') : t('invoices.pos.modal.save') }}
                    </button>
                    <button type="button" @click="showPayModal = false" class="btn btn-ghost">{{ t('invoices.pos.modal.cancel') }}</button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
