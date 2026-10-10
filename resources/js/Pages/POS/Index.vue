<script setup>
import { ref, computed, watch } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';

const { t } = useI18n();
const props = defineProps({
    customers:      Array,
    paymentMethods: Object,
    prefillOrder:   Object,
    cashOpen:       { type: Boolean, default: false },
});

const form = useForm({
    customer_id:   null,
    work_order_id: null,
    notes:         '',
    tax:           0,
    discount:      0,
    items:         [],
    payments:      [],
});

// Pre-fill from work order if provided
if (props.prefillOrder) {
    form.customer_id   = props.prefillOrder.customer_id;
    form.work_order_id = props.prefillOrder.id;
    for (const s of props.prefillOrder.services ?? []) {
        form.items.push({ type: 'service', reference_id: s.service_id, name: s.name, qty: s.qty, unit_price: s.unit_price, discount: 0 });
    }
    for (const p of props.prefillOrder.products ?? []) {
        form.items.push({ type: 'product', reference_id: p.product_id, name: p.name, qty: p.qty, unit_price: p.unit_price, discount: 0 });
    }
}

const itemSearch    = ref('');
const itemResults   = ref([]);
const searchType    = ref('service'); // 'service' or 'product'
const isSearching   = ref(false);

let searchTimer = null;
let abortCtrl = null;
const searchCache = new Map();

function onSearchInput() {
    clearTimeout(searchTimer);
    const q = itemSearch.value.trim();
    if (!q) {
        itemResults.value = [];
        return;
    }
    const cacheKey = `${searchType.value}:${q.toLowerCase()}`;
    if (searchCache.has(cacheKey)) {
        itemResults.value = searchCache.get(cacheKey);
        return;
    }
    searchTimer = setTimeout(searchItems, 220);
}

async function searchItems() {
    const q = itemSearch.value.trim();
    if (!q) {
        itemResults.value = [];
        return;
    }

    const cacheKey = `${searchType.value}:${q.toLowerCase()}`;
    if (searchCache.has(cacheKey)) {
        itemResults.value = searchCache.get(cacheKey);
        return;
    }

    if (abortCtrl) abortCtrl.abort();
    abortCtrl = new AbortController();

    isSearching.value = true;
    try {
        const url = searchType.value === 'service'
            ? `/services/search?q=${encodeURIComponent(q)}`
            : `/products/search?q=${encodeURIComponent(q)}`;
        const res = await fetch(url, { signal: abortCtrl.signal });
        const data = await res.json();
        searchCache.set(cacheKey, data);
        itemResults.value = data;
    } catch (err) {
        if (err.name !== 'AbortError') console.error(err);
    } finally {
        isSearching.value = false;
    }
}

function addItem(item) {
    if (searchType.value === 'product' && (item.stock === undefined || item.stock <= 0)) {
        alert(`No es posible agregar '${item.name}' porque no tiene stock disponible.`);
        return;
    }
    form.items.push({
        type:         searchType.value,
        reference_id: item.id,
        name:         item.name,
        qty:          1,
        unit_price:   item.price ?? 0,
        discount:     0,
        stock:        searchType.value === 'product' ? item.stock : null,
    });
    itemSearch.value  = '';
    itemResults.value = [];
}

function addCustomItem() {
    form.items.push({ type: 'other', reference_id: null, name: '', qty: 1, unit_price: 0, discount: 0 });
}

function removeItem(i) { form.items.splice(i, 1); }

// Payment split
const paymentMethod = ref('cash');
const paymentRef    = ref('');
const paymentAmount = ref('');

const subtotal  = computed(() => form.items.reduce((s, i) => s + ((i.qty * i.unit_price) - i.discount), 0));
const total     = computed(() => Math.max(0, subtotal.value + Number(form.tax) - Number(form.discount)));
const amountPaid = computed(() => form.payments.reduce((s, p) => s + Number(p.amount), 0));
const balance   = computed(() => (total.value - amountPaid.value).toFixed(2));

function addPayment() {
    const remaining = parseFloat(balance.value);
    if (remaining <= 0) return;
    const amt = parseFloat(paymentAmount.value) > 0 ? parseFloat(paymentAmount.value) : remaining;
    form.payments.push({ method: paymentMethod.value, amount: amt.toFixed(2), reference: paymentRef.value });
    paymentRef.value   = '';
    paymentAmount.value = '';
}

function removePayment(i) { form.payments.splice(i, 1); }

function submit() {
    for (const item of form.items) {
        if (item.type === 'product' && item.stock !== null && item.stock !== undefined && item.qty > item.stock) {
            alert(`No puedes vender más de la cantidad disponible para '${item.name}' (Stock disponible: ${item.stock}).`);
            return;
        }
    }
    form.post('/invoices');
}
</script>

<template>
    <AppLayout>
        <template #header>
            <div class="page-header">
                <h1 class="page-title">{{ t('invoices.newInvoice') }}</h1>
                <a href="/invoices" class="btn btn-ghost">{{ t('common.viewAll') }}</a>
            </div>
        </template>

        <!-- Caja cerrada / sin abrir -->
        <div v-if="!cashOpen" class="mb-4 flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3.5 text-red-800">
            <svg class="w-5 h-5 mt-0.5 flex-shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div>
                <p class="font-semibold text-sm">Caja no abierta</p>
                <p class="text-xs mt-0.5">No se pueden registrar ventas hasta que se <a href="/reports/cash-close" class="underline font-medium hover:text-red-900">abra la caja</a> del día.</p>
            </div>
        </div>

        <form @submit.prevent="submit" class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            <!-- Left: items -->
            <div class="lg:col-span-2 space-y-5">
                <!-- Customer -->
                <div class="card">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="label">{{ t('invoices.pos.customer') }}</label>
                            <select v-model="form.customer_id" class="input w-full">
                                <option :value="null">— {{ t('common.walkIn') }} —</option>
                                <option v-for="c in customers" :key="c.id" :value="c.id">{{ c.name }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="label">{{ t('common.notes') }}</label>
                            <input v-model="form.notes" type="text" class="input w-full" :placeholder="t('invoices.pos.notes')" />
                        </div>
                    </div>
                    <div v-if="form.work_order_id" class="mt-2 text-xs text-text-3">
                        {{ t('invoices.pos.linkedOrder') }} #{{ prefillOrder?.number }}
                    </div>
                </div>

                <!-- Item search -->
                <div class="card space-y-4">
                    <h2 class="section-title">{{ t('invoices.pos.addItems') }}</h2>
                    <div class="flex gap-2">
                        <div class="flex rounded-lg border border-border overflow-x-auto">
                            <button type="button" @click="searchType='service'; itemResults=[]"
                                :class="['px-3 py-1.5 text-xs font-medium', searchType==='service' ? 'bg-brand-600 text-white' : 'text-text-2 hover:bg-slate-50']">
                                {{ t('invoices.pos.services') }}
                            </button>
                            <button type="button" @click="searchType='product'; itemResults=[]"
                                :class="['px-3 py-1.5 text-xs font-medium', searchType==='product' ? 'bg-brand-600 text-white' : 'text-text-2 hover:bg-slate-50']">
                                {{ t('invoices.pos.products') }}
                            </button>
                        </div>
                        <div class="relative flex-1">
                            <input v-model="itemSearch" @input="onSearchInput" type="search"
                                :placeholder="searchType === 'service' ? t('invoices.pos.searchServices') : t('invoices.pos.searchProducts')"
                                class="input w-full pr-8" />
                            <div v-if="isSearching" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                            </div>
                            <div v-if="itemResults.length" class="absolute z-20 w-full mt-1 bg-white rounded-lg border border-border shadow-lg overflow-x-auto max-h-60 overflow-y-auto">
                                <button v-for="item in itemResults" :key="item.id" type="button" @click="addItem(item)"
                                    class="w-full flex items-center justify-between px-3 py-2 text-sm border-b border-slate-100 last:border-0"
                                    :class="searchType === 'product' && (!item.stock || item.stock <= 0) ? 'opacity-50 bg-slate-50 cursor-not-allowed' : 'hover:bg-slate-50'"
                                >
                                    <div class="flex items-center gap-2">
                                        <span class="text-text font-medium">{{ item.name }}</span>
                                        <span v-if="searchType === 'product'"
                                            class="text-[10px] font-semibold px-1.5 py-0.5 rounded"
                                            :class="item.stock > 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-red-50 text-red-700 border border-red-200'">
                                            {{ item.stock > 0 ? `Stock: ${item.stock}` : 'Sin stock (0)' }}
                                        </span>
                                    </div>
                                    <span class="font-mono text-brand-700 font-semibold">${{ item.price }}</span>
                                </button>
                            </div>
                        </div>
                        <button type="button" @click="addCustomItem" class="btn btn-ghost btn-sm whitespace-nowrap">{{ t('invoices.pos.customItem') }}</button>
                    </div>

                    <!-- Items table -->
                    <div class="overflow-x-auto" v-if="form.items.length"><table class="w-full text-sm whitespace-nowrap" >
                        <thead><tr class="border-b border-border">
                            <th class="pb-2 text-left text-text-2 font-medium">{{ t('invoices.pos.item') }}</th>
                            <th class="pb-2 text-center text-text-2 font-medium w-24">{{ t('invoices.pos.qty') }}</th>
                            <th class="pb-2 text-right text-text-2 font-medium w-28">{{ t('invoices.pos.price') }}</th>
                            <th class="pb-2 text-right text-text-2 font-medium w-24">{{ t('invoices.pos.disc') }}</th>
                            <th class="pb-2 text-right text-text-2 font-medium w-20">{{ t('invoices.pos.total') }}</th>
                            <th class="w-8"></th>
                        </tr></thead>
                        <tbody>
                            <tr v-for="(item, i) in form.items" :key="i" class="border-t border-border">
                                <td class="py-1 px-1">
                                    <input v-model="item.name" class="input w-full text-sm py-1" :placeholder="t('invoices.pos.item')" />
                                </td>
                                <td class="py-1 px-1">
                                    <input v-model="item.qty" type="number" min="1" :max="item.stock !== null && item.stock !== undefined ? item.stock : undefined" class="input w-full px-2 text-center text-sm py-1" />
                                    <span v-if="item.stock !== null && item.stock !== undefined" class="text-[10px] text-center block text-text-3 font-mono">
                                        Disp: {{ item.stock }}
                                    </span>
                                    <span v-if="item.stock !== null && item.stock !== undefined && item.qty > item.stock" class="text-[10px] text-center block text-red-500 font-bold">
                                        Excede stock
                                    </span>
                                </td>
                                <td class="py-1 px-1"><input v-model="item.unit_price" type="number" step="0.01" min="0" class="input w-full px-2 text-right text-sm py-1" /></td>
                                <td class="py-1 px-1"><input v-model="item.discount" type="number" step="0.01" min="0" class="input w-full px-2 text-right text-sm py-1" /></td>
                                <td class="py-1 px-1 text-right font-mono font-medium">
                                    ${{ Math.max(0, item.qty * item.unit_price - item.discount).toFixed(2) }}
                                </td>
                                <td class="py-1 px-1"><button type="button" @click="removeItem(i)" class="text-red-400 hover:text-red-600 text-lg">×</button></td>
                            </tr>
                        </tbody>
                    </table></div>

                    <div v-else class="flex flex-col items-center justify-center py-16 px-4 bg-slate-50/50 rounded-2xl border border-dashed border-slate-200">
                        <div class="w-16 h-16 bg-white text-slate-300 rounded-full flex items-center justify-center mb-4 shadow-sm">
                            <Icon name="cart" class="w-8 h-8" />
                        </div>
                        <h3 class="text-sm font-bold text-slate-700 mb-1">{{ t('invoices.pos.emptyTitle') }}</h3>
                        <p class="text-xs text-slate-500 mb-4 text-center max-w-xs">{{ t('invoices.pos.emptyHint') }}</p>
                    </div>

                </div>
            </div>

            <!-- Right: totals & payment -->
            <div class="space-y-5">
                <!-- Totals -->
                <div class="card space-y-4">
                    <h2 class="section-title">{{ t('invoices.pos.totals') }}</h2>
                    <div class="flex justify-between text-sm"><span class="text-text-2">{{ t('invoices.pos.subtotal') }}</span><span class="font-mono">${{ subtotal.toFixed(2) }}</span></div>
                    <div class="flex items-center justify-between gap-2 text-sm">
                        <label class="text-text-2 shrink-0">{{ t('invoices.pos.taxLabel') }}</label>
                        <input v-model="form.tax" type="number" step="0.01" min="0" class="input w-24 text-right py-1 text-sm" />
                    </div>
                    <div class="flex items-center justify-between gap-2 text-sm">
                        <label class="text-text-2 shrink-0">{{ t('invoices.pos.discountLabel') }}</label>
                        <input v-model="form.discount" type="number" step="0.01" min="0" class="input w-24 text-right py-1 text-sm" />
                    </div>
                    <div class="flex justify-between font-bold text-text border-t border-border pt-2 text-lg">
                        <span>{{ t('invoices.pos.total') }}</span>
                        <span class="font-mono">${{ total.toFixed(2) }}</span>
                    </div>
                </div>

                <!-- Payments -->
                <div class="card space-y-4">
                    <h2 class="section-title">{{ t('invoices.pos.payment') }}</h2>

                    <div v-for="(pay, i) in form.payments" :key="i" class="flex items-center justify-between gap-2 text-sm border rounded-lg px-3 py-2 bg-green-50 border-green-200">
                        <span class="font-medium text-green-800 capitalize">{{ paymentMethods[pay.method] ?? pay.method }}</span>
                        <span v-if="pay.reference" class="text-green-600 text-xs">{{ pay.reference }}</span>
                        <span class="font-mono font-semibold text-green-800">${{ pay.amount }}</span>
                        <button type="button" @click="removePayment(i)" class="text-red-400 hover:text-red-600">×</button>
                    </div>

                    <!-- Balance -->
                    <div :class="['flex justify-between text-sm font-semibold pt-1', parseFloat(balance) < 0 ? 'text-green-600' : parseFloat(balance) > 0 ? 'text-amber-600' : 'text-emerald-600']">
                        <span>{{ parseFloat(balance) < 0 ? t('invoices.pos.balanceLabel.change') : parseFloat(balance) === 0 ? t('invoices.pos.balanceLabel.paid') : t('invoices.pos.balanceLabel.pending') }}</span>
                        <span class="font-mono">{{ parseFloat(balance) === 0 ? '' : `$${Math.abs(balance)}` }}</span>
                    </div>

                    <!-- Add payment -->
                    <div class="space-y-2 border-t border-border pt-3">
                        <select v-model="paymentMethod" class="input w-full">
                            <option v-for="(label, key) in paymentMethods" :key="key" :value="key">{{ label }}</option>
                        </select>
                        <!-- Amount input for partial payments -->
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-text-3 text-sm pointer-events-none">$</span>
                            <input v-model="paymentAmount" type="number" step="0.01" min="0.01"
                                class="input input-currency w-full text-sm"
                                :placeholder="parseFloat(balance) > 0 ? balance : '0.00'" />
                        </div>
                        <input v-if="paymentMethod !== 'cash'" v-model="paymentRef" type="text"
                            :placeholder="paymentMethod === 'card' ? t('invoices.pos.modal.lastFour') : t('invoices.pos.modal.refConfirm')"
                            class="input w-full" />
                        <button type="button" @click="addPayment" :disabled="parseFloat(balance) <= 0"
                            class="btn btn-ghost w-full">{{ t('invoices.pos.addPayment') }}</button>
                    </div>
                </div>

                <!-- Aviso crédito -->
                <div v-if="form.items.length > 0 && parseFloat(balance) > 0"
                    class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 flex gap-3 items-start">
                    <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                    <div>
                        <p class="text-sm font-semibold text-amber-800">{{ t('invoices.pos.creditTitle') }}</p>
                        <p class="text-xs text-amber-700 mt-0.5">
                            <span class="font-mono font-bold">${{ balance }}</span>
                            {{ t('invoices.pos.creditText') }}
                        </p>
                    </div>
                </div>

                <!-- Submit -->
                <button type="submit" :disabled="form.processing || form.items.length === 0 || !cashOpen"
                    :class="[
                        'w-full py-3 text-base font-semibold rounded-xl transition-colors',
                        form.items.length === 0 || form.processing || !cashOpen
                            ? 'bg-slate-200 text-slate-400 cursor-not-allowed'
                            : parseFloat(balance) <= 0
                                ? 'bg-brand-600 hover:bg-brand-700 text-white'
                                : 'bg-amber-500 hover:bg-amber-600 text-white'
                    ]"
                >
                    <span v-if="form.processing">{{ t('invoices.pos.submit.processing') }}</span>
                    <span v-else-if="parseFloat(balance) <= 0">{{ t('invoices.pos.submit.full') }}</span>
                    <span v-else-if="amountPaid > 0">{{ t('invoices.pos.submit.partial') }}</span>
                    <span v-else>{{ t('invoices.pos.submit.credit') }}</span>
                </button>
            </div>
        </form>
    </AppLayout>
</template>
