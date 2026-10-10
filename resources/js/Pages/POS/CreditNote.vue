<script setup>
import { useForm } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    invoice:         Object,
    maxRefund:       Number,
    alreadyRefunded: Number,
    refundMethods:   Object,
});

const form = useForm({
    reason:        'return',
    notes:         '',
    amount:        props.maxRefund > 0 ? props.maxRefund.toFixed(2) : '',
    refund_method: 'cash',
});

function submit() {
    form.post(`/invoices/${props.invoice.id}/credit-note`);
}
</script>

<template>
    <AppLayout>
        <template #header>
            <div class="page-header">
                <div>
                    <nav class="breadcrumb mb-1">
                        <Link href="/invoices" class="breadcrumb-link">Invoices</Link>
                        <span class="breadcrumb-sep">/</span>
                        <Link :href="`/invoices/${invoice.id}`" class="breadcrumb-link">{{ invoice.number }}</Link>
                        <span class="breadcrumb-sep">/</span>
                        <span class="breadcrumb-current">Credit Note</span>
                    </nav>
                    <h1 class="page-title">Issue Credit Note</h1>
                </div>
            </div>
        </template>

        <div class="max-w-2xl space-y-5">

            <!-- Invoice summary -->
            <div class="card">
                <h2 class="text-sm font-semibold mb-3" style="color:var(--color-text)">Invoice Summary</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                    <div>
                        <p class="label">Invoice</p>
                        <p class="font-mono font-medium">{{ invoice.number }}</p>
                    </div>
                    <div>
                        <p class="label">Customer</p>
                        <p>{{ invoice.customer?.name ?? 'Walk-in' }}</p>
                    </div>
                    <div>
                        <p class="label">Total Billed</p>
                        <p class="font-medium">${{ parseFloat(invoice.total).toFixed(2) }}</p>
                    </div>
                    <div>
                        <p class="label">Total Paid</p>
                        <p class="font-medium text-green-600">${{ parseFloat(invoice.amount_paid).toFixed(2) }}</p>
                    </div>
                </div>
                <div v-if="alreadyRefunded > 0" class="mt-3 text-sm text-amber-600 bg-amber-50 rounded-lg px-3 py-2">
                    Previously refunded: ${{ alreadyRefunded.toFixed(2) }} ·
                    Max refundable: ${{ maxRefund.toFixed(2) }}
                </div>
            </div>

            <!-- Credit note form -->
            <div class="card">
                <h2 class="text-sm font-semibold mb-4" style="color:var(--color-text)">Credit Note Details</h2>

                <div v-if="maxRefund <= 0" class="text-sm text-red-600 bg-red-50 rounded-lg px-4 py-3">
                    This invoice has been fully refunded. No further credit notes can be issued.
                </div>

                <form v-else @submit.prevent="submit" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-group">
                            <label class="label">Reason *</label>
                            <select v-model="form.reason" class="input" required>
                                <option value="return">Return / Exchange</option>
                                <option value="error">Billing Error</option>
                                <option value="discount">Retroactive Discount</option>
                                <option value="other">Other</option>
                            </select>
                            <p v-if="form.errors.reason" class="form-error">{{ form.errors.reason }}</p>
                        </div>
                        <div class="form-group">
                            <label class="label">Refund Method *</label>
                            <select v-model="form.refund_method" class="input" required>
                                <option v-for="(label, key) in refundMethods" :key="key" :value="key">{{ label }}</option>
                            </select>
                            <p v-if="form.errors.refund_method" class="form-error">{{ form.errors.refund_method }}</p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="label">Refund Amount * <span class="text-text-3">(max ${{ maxRefund.toFixed(2) }})</span></label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-text-3">$</span>
                            <input v-model="form.amount" type="number" step="0.01" min="0.01" :max="maxRefund"
                                class="input input-currency" required />
                        </div>
                        <p v-if="form.errors.amount" class="form-error">{{ form.errors.amount }}</p>
                    </div>

                    <div class="form-group">
                        <label class="label">Notes</label>
                        <textarea v-model="form.notes" class="input" rows="3" placeholder="Describe the reason for the refund…"></textarea>
                        <p v-if="form.errors.notes" class="form-error">{{ form.errors.notes }}</p>
                    </div>

                    <div class="flex justify-end gap-3">
                        <Link :href="`/invoices/${invoice.id}`" class="btn btn-ghost">Cancel</Link>
                        <button type="submit" class="btn btn-primary" :disabled="form.processing">
                            {{ form.processing ? 'Issuing…' : 'Issue Credit Note' }}
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </AppLayout>
</template>
