<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';

const { t } = useI18n();

const props = defineProps({
    order:             Object,
    selected_customer: Object,   // pre-populated in edit mode (with vehicles)
    technicians:       Array,
});

const form = useForm({
    customer_id:    props.order?.customer_id ?? null,
    vehicle_id:     props.order?.vehicle_id  ?? null,
    assigned_to:    props.order?.assigned_to ?? null,
    notes:          props.order?.notes          ?? '',
    internal_notes: props.order?.internal_notes ?? '',
    promised_at:    props.order?.promised_at?.substring(0, 16) ?? '',
    services:       props.order?.services  ?? [],
    products:       props.order?.products  ?? [],
});

// ── Customer combobox ──────────────────────────────────────
const selectedCustomer   = ref(props.selected_customer ?? null);
const customerVehicles   = ref(props.selected_customer?.vehicles ?? []);
const customerSearch     = ref('');
const customerResults    = ref([]);
const showDropdown       = ref(false);
const searching          = ref(false);
const customerWrapRef    = ref(null);
let   searchTimer        = null;

function handleOutsideClick(e) {
    if (customerWrapRef.value && !customerWrapRef.value.contains(e.target)) {
        showDropdown.value = false;
    }
}
onMounted(() => document.addEventListener('mousedown', handleOutsideClick));
onUnmounted(() => document.removeEventListener('mousedown', handleOutsideClick));

async function searchCustomers() {
    clearTimeout(searchTimer);
    if (!customerSearch.value.trim()) {
        customerResults.value = [];
        showDropdown.value = false;
        return;
    }
    searchTimer = setTimeout(async () => {
        searching.value = true;
        try {
            const res = await fetch(`/customers/search?q=${encodeURIComponent(customerSearch.value)}`);
            customerResults.value = await res.json();
            showDropdown.value = true;
        } finally {
            searching.value = false;
        }
    }, 250);
}

function selectCustomer(c) {
    selectedCustomer.value  = c;
    form.customer_id        = c.id;
    form.vehicle_id         = null;
    customerVehicles.value  = c.vehicles ?? [];
    customerResults.value   = [];
    customerSearch.value    = '';
    showDropdown.value      = false;
}

function clearCustomer() {
    selectedCustomer.value = null;
    form.customer_id       = null;
    form.vehicle_id        = null;
    customerVehicles.value = [];
    customerSearch.value   = '';
}

// ── Services / Products search ─────────────────────────────
const serviceSearch  = ref('');
const productSearch  = ref('');
const serviceResults = ref([]);
const productResults = ref([]);
const isSearchingServices = ref(false);
const isSearchingProducts = ref(false);

let serviceTimer = null;
let productTimer = null;
let serviceAbort = null;
let productAbort = null;
const searchCache = new Map();

function onServiceInput() {
    clearTimeout(serviceTimer);
    const q = serviceSearch.value.trim();
    if (!q) { serviceResults.value = []; return; }
    if (searchCache.has(`svc:${q.toLowerCase()}`)) {
        serviceResults.value = searchCache.get(`svc:${q.toLowerCase()}`);
        return;
    }
    serviceTimer = setTimeout(searchServices, 220);
}

async function searchServices() {
    const q = serviceSearch.value.trim();
    if (!q) { serviceResults.value = []; return; }
    const key = `svc:${q.toLowerCase()}`;
    if (searchCache.has(key)) {
        serviceResults.value = searchCache.get(key);
        return;
    }
    if (serviceAbort) serviceAbort.abort();
    serviceAbort = new AbortController();

    isSearchingServices.value = true;
    try {
        const res = await fetch(`/services/search?q=${encodeURIComponent(q)}`, { signal: serviceAbort.signal });
        const data = await res.json();
        searchCache.set(key, data);
        serviceResults.value = data;
    } catch (err) {
        if (err.name !== 'AbortError') console.error(err);
    } finally {
        isSearchingServices.value = false;
    }
}

function onProductInput() {
    clearTimeout(productTimer);
    const q = productSearch.value.trim();
    if (!q) { productResults.value = []; return; }
    if (searchCache.has(`prod:${q.toLowerCase()}`)) {
        productResults.value = searchCache.get(`prod:${q.toLowerCase()}`);
        return;
    }
    productTimer = setTimeout(searchProducts, 220);
}

async function searchProducts() {
    const q = productSearch.value.trim();
    if (!q) { productResults.value = []; return; }
    const key = `prod:${q.toLowerCase()}`;
    if (searchCache.has(key)) {
        productResults.value = searchCache.get(key);
        return;
    }
    if (productAbort) productAbort.abort();
    productAbort = new AbortController();

    isSearchingProducts.value = true;
    try {
        const res = await fetch(`/products/search?q=${encodeURIComponent(q)}`, { signal: productAbort.signal });
        const data = await res.json();
        searchCache.set(key, data);
        productResults.value = data;
    } catch (err) {
        if (err.name !== 'AbortError') console.error(err);
    } finally {
        isSearchingProducts.value = false;
    }
}

function addService(svc) {
    form.services.push({ service_id: svc.id, name: svc.name, qty: 1, unit_price: svc.price, cost_price: svc.cost_price ?? 0, notes: '' });
    serviceSearch.value  = '';
    serviceResults.value = [];
}

function addProduct(prod) {
    if (prod.stock !== undefined && prod.stock <= 0) {
        alert(`No es posible agregar '${prod.name}' porque no tiene stock disponible.`);
        return;
    }
    form.products.push({
        product_id: prod.id,
        name: prod.name,
        qty: 1,
        unit_price: prod.price ?? 0,
        cost_price: prod.cost_price ?? 0,
        stock: prod.stock,
        notes: '',
    });
    productSearch.value  = '';
    productResults.value = [];
}

function removeService(i) { form.services.splice(i, 1); }
function removeProduct(i) { form.products.splice(i, 1); }

const subtotal = computed(() => {
    const s = form.services.reduce((sum, s) => sum + (s.qty * parseFloat(s.unit_price || 0)), 0);
    const p = form.products.reduce((sum, p) => sum + (p.qty * parseFloat(p.unit_price || 0)), 0);
    return (s + p).toFixed(2);
});

function submit() {
    for (const prod of form.products) {
        if (prod.stock !== null && prod.stock !== undefined && prod.qty > prod.stock) {
            alert(`No puedes agregar más de la cantidad disponible para '${prod.name}' (Stock disponible: ${prod.stock}).`);
            return;
        }
    }
    if (props.order) {
        form.put(`/work-orders/${props.order.id}`);
    } else {
        form.post('/work-orders');
    }
}
</script>

<template>
    <AppLayout>
        <template #header>
            <div class="page-header">
                <h1 class="page-title">{{ order ? t('workOrders.form.editTitle') : t('workOrders.form.newTitle') }}</h1>
            </div>
        </template>

        <form @submit.prevent="submit" class="space-y-5 max-w-4xl">

            <!-- Customer & vehicle -->
            <div class="card grid grid-cols-1 md:grid-cols-2 gap-4">

                <!-- Customer combobox -->
                <div ref="customerWrapRef" class="relative">
                    <label class="label">{{ t('workOrders.form.customer') }}</label>

                    <!-- Selected state -->
                    <div v-if="selectedCustomer" class="input w-full flex items-center gap-2 pr-2">
                        <div class="flex-1 min-w-0">
                            <span class="text-sm font-medium text-text block truncate">{{ selectedCustomer.name }}</span>
                            <span v-if="selectedCustomer.phone" class="text-xs text-text-3">{{ selectedCustomer.phone }}</span>
                        </div>
                        <button type="button" @click="clearCustomer" class="text-text-3 hover:text-red-500 text-lg leading-none flex-shrink-0 transition-colors">×</button>
                    </div>

                    <!-- Search input -->
                    <div v-else>
                        <div class="relative">
                            <input
                                v-model="customerSearch"
                                @input="searchCustomers"
                                @focus="customerResults.length && (showDropdown = true)"
                                type="text"
                                :placeholder="t('common.walkIn') + ' — buscar cliente...'"
                                class="input w-full pr-8"
                                autocomplete="off"
                            />
                            <span v-if="searching" class="absolute right-2 top-1/2 -translate-y-1/2 text-text-3 text-xs animate-spin">⟳</span>
                        </div>

                        <!-- Dropdown -->
                        <div
                            v-if="showDropdown && customerResults.length"
                            class="absolute z-30 w-full mt-1 bg-white rounded-lg border border-border shadow-xl overflow-hidden"
                        >
                            <button
                                v-for="c in customerResults" :key="c.id"
                                type="button"
                                @click="selectCustomer(c)"
                                class="w-full flex items-center justify-between px-4 py-3 hover:bg-slate-50 text-left border-b border-slate-50 last:border-0 transition-colors"
                            >
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-text truncate">{{ c.name }}</p>
                                    <p v-if="c.phone" class="text-xs text-text-3 mt-0.5">{{ c.phone }}</p>
                                </div>
                                <span class="text-xs text-text-3 ml-4 flex-shrink-0">
                                    {{ c.vehicles?.length ?? 0 }} {{ c.vehicles?.length === 1 ? 'vehículo' : 'vehículos' }}
                                </span>
                            </button>
                        </div>

                        <p v-else-if="showDropdown && customerSearch && !searching" class="text-xs text-text-3 mt-1 italic">Sin resultados</p>
                    </div>
                </div>

                <!-- Vehicle -->
                <div>
                    <label class="label">{{ t('workOrders.form.vehicle') }}</label>
                    <select
                        v-model="form.vehicle_id"
                        class="input w-full"
                        :disabled="!selectedCustomer || !customerVehicles.length"
                    >
                        <option :value="null">— {{ selectedCustomer ? (customerVehicles.length ? 'Seleccionar vehículo' : 'Sin vehículos registrados') : 'Selecciona un cliente primero' }} —</option>
                        <option v-for="v in customerVehicles" :key="v.id" :value="v.id">
                            {{ v.year }} {{ v.make }} {{ v.model }}{{ v.plate ? ` · ${v.plate}` : '' }}{{ v.tire_size ? ` (${v.tire_size})` : '' }}
                        </option>
                    </select>
                </div>

                <!-- Technician -->
                <div>
                    <label class="label">{{ t('workOrders.form.assignedTo') }}</label>
                    <select v-model="form.assigned_to" class="input w-full">
                        <option :value="null">— Unassigned —</option>
                        <option v-for="tech in technicians" :key="tech.id" :value="tech.id">{{ tech.name }}</option>
                    </select>
                </div>

                <!-- Promised at -->
                <div>
                    <label class="label">{{ t('workOrders.form.promisedAt') }}</label>
                    <input v-model="form.promised_at" type="datetime-local" class="input w-full" />
                </div>
            </div>

            <!-- Services -->
            <div class="card space-y-3">
                <h2 class="section-title">{{ t('workOrders.form.services') }}</h2>

                <div class="relative">
                    <input v-model="serviceSearch" @input="onServiceInput" type="search"
                        :placeholder="t('workOrders.form.searchSvc')" class="input w-full pr-8" />
                    <div v-if="isSearchingServices" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                    </div>
                    <div v-if="serviceResults.length" class="absolute z-10 w-full mt-1 bg-white rounded-lg border border-border shadow-lg overflow-x-auto max-h-60 overflow-y-auto">
                        <button v-for="svc in serviceResults" :key="svc.id"
                            type="button" @click="addService(svc)"
                            class="w-full flex items-center justify-between px-3 py-2 text-sm border-b border-slate-100 last:border-0 hover:bg-slate-50"
                        >
                            <span class="text-text">{{ svc.name }}</span>
                            <span class="font-mono text-brand-700 font-semibold">${{ svc.price }}</span>
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto" v-if="form.services.length"><table class="w-full text-sm">
                    <thead><tr>
                        <th class="text-left py-1 px-2 text-text-2 font-medium">{{ t('workOrders.form.services') }}</th>
                        <th class="text-center py-1 px-2 text-text-2 font-medium w-16">{{ t('workOrders.form.qty') }}</th>
                        <th class="text-right py-1 px-2 text-text-2 font-medium w-24">{{ t('workOrders.form.price') }}</th>
                        <th class="text-right py-1 px-2 text-text-2 font-medium w-24 text-text-3" :title="t('services.form.costPriceHint')">{{ t('services.form.costPrice') }}</th>
                        <th class="text-right py-1 px-2 text-text-2 font-medium w-20">{{ t('workOrders.form.subtotal') }}</th>
                        <th class="w-8"></th>
                    </tr></thead>
                    <tbody>
                        <tr v-for="(svc, i) in form.services" :key="i" class="border-t border-border">
                            <td class="py-1 px-2"><input v-model="svc.name" class="input w-full text-sm py-1" /></td>
                            <td class="py-1 px-2"><input v-model="svc.qty" type="number" min="1" class="input text-center text-sm py-1" /></td>
                            <td class="py-1 px-2"><input v-model="svc.unit_price" type="number" step="0.01" class="input text-right text-sm py-1" /></td>
                            <td class="py-1 px-2">
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-text-3 text-xs pointer-events-none">$</span>
                                    <input v-model="svc.cost_price" type="number" step="0.01" min="0" class="input input-currency text-right text-sm py-1 text-text-3" />
                                </div>
                            </td>
                            <td class="py-1 px-2 text-right font-mono">${{ (svc.qty * svc.unit_price).toFixed(2) }}</td>
                            <td class="py-1 px-1 text-center"><button type="button" @click="removeService(i)" class="text-red-400 hover:text-red-600 text-lg leading-none">×</button></td>
                        </tr>
                    </tbody>
                </table></div>
                <p v-else class="text-sm text-text-3 italic">{{ t('workOrders.noResults') }}</p>
            </div>

            <!-- Products / Parts -->
            <div class="card space-y-3">
                <h2 class="section-title">{{ t('workOrders.form.products') }}</h2>
                <div class="relative">
                    <input v-model="productSearch" @input="onProductInput" type="search"
                        :placeholder="t('workOrders.form.searchProd')" class="input w-full pr-8" />
                    <div v-if="isSearchingProducts" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                    </div>
                    <div v-if="productResults.length" class="absolute z-10 w-full mt-1 bg-white rounded-lg border border-border shadow-lg overflow-x-auto max-h-60 overflow-y-auto">
                        <button v-for="prod in productResults" :key="prod.id"
                            type="button" @click="addProduct(prod)"
                            class="w-full flex items-center justify-between px-3 py-2 text-sm border-b border-slate-100 last:border-0"
                            :class="prod.stock <= 0 ? 'opacity-50 bg-slate-50 cursor-not-allowed' : 'hover:bg-slate-50'"
                        >
                            <div class="flex items-center gap-2">
                                <span class="text-text font-medium">{{ prod.name }}</span>
                                <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded"
                                    :class="prod.stock > 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-red-50 text-red-700 border border-red-200'">
                                    {{ prod.stock > 0 ? `Stock: ${prod.stock}` : 'Sin stock (0)' }}
                                </span>
                            </div>
                            <span class="font-mono text-brand-700 font-semibold ml-auto">${{ prod.price }}</span>
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto" v-if="form.products.length"><table class="w-full text-sm">
                    <thead><tr>
                        <th class="text-left py-1 px-2 text-text-2 font-medium">{{ t('workOrders.form.products') }}</th>
                        <th class="text-center py-1 px-2 text-text-2 font-medium w-24">{{ t('workOrders.form.qty') }}</th>
                        <th class="text-right py-1 px-2 text-text-2 font-medium w-24">{{ t('workOrders.form.price') }}</th>
                        <th class="text-right py-1 px-2 text-text-2 font-medium w-24 text-text-3" :title="t('services.form.costPriceHint')">{{ t('services.form.costPrice') }}</th>
                        <th class="text-right py-1 px-2 text-text-2 font-medium w-20">{{ t('workOrders.form.subtotal') }}</th>
                        <th class="w-8"></th>
                    </tr></thead>
                    <tbody>
                        <tr v-for="(prod, i) in form.products" :key="i" class="border-t border-border">
                            <td class="py-1 px-2"><input v-model="prod.name" class="input w-full text-sm py-1" /></td>
                            <td class="py-1 px-2">
                                <input v-model="prod.qty" type="number" min="1" :max="prod.stock !== null && prod.stock !== undefined ? prod.stock : undefined" class="input text-center text-sm py-1 w-full" />
                                <span v-if="prod.stock !== null && prod.stock !== undefined" class="text-[10px] text-center block text-text-3 font-mono">
                                    Disp: {{ prod.stock }}
                                </span>
                                <span v-if="prod.stock !== null && prod.stock !== undefined && prod.qty > prod.stock" class="text-[10px] text-center block text-red-500 font-bold">
                                    Excede stock
                                </span>
                            </td>
                            <td class="py-1 px-2"><input v-model="prod.unit_price" type="number" step="0.01" class="input text-right text-sm py-1" /></td>
                            <td class="py-1 px-2">
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-text-3 text-xs pointer-events-none">$</span>
                                    <input v-model="prod.cost_price" type="number" step="0.01" min="0" class="input input-currency text-right text-sm py-1 text-text-3" />
                                </div>
                            </td>
                            <td class="py-1 px-2 text-right font-mono">${{ (prod.qty * prod.unit_price).toFixed(2) }}</td>
                            <td class="py-1 px-1 text-center"><button type="button" @click="removeProduct(i)" class="text-red-400 hover:text-red-600 text-lg leading-none">×</button></td>
                        </tr>
                    </tbody>
                </table></div>
                <p v-else class="text-sm text-text-3 italic">{{ t('workOrders.noResults') }}</p>
            </div>

            <!-- Notes -->
            <div class="card grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="label">{{ t('workOrders.form.notes') }}</label>
                    <textarea v-model="form.notes" rows="3" class="input w-full" placeholder="Notas visibles al cliente…" />
                </div>
                <div>
                    <label class="label">{{ t('workOrders.form.internalNotes') }}</label>
                    <textarea v-model="form.internal_notes" rows="3" class="input w-full" placeholder="Notas internas del taller…" />
                </div>
            </div>

            <!-- Summary + actions -->
            <div class="flex items-center justify-between">
                <div class="text-sm text-text-2">
                    {{ t('workOrders.form.subtotal') }}: <span class="font-mono font-semibold text-text text-base">${{ subtotal }}</span>
                </div>
                <div class="flex gap-3">
                    <a :href="order ? `/work-orders/${order.id}` : '/work-orders'" class="btn btn-ghost">{{ t('workOrders.form.cancel') }}</a>
                    <button type="submit" :disabled="form.processing" class="btn btn-primary">
                        {{ form.processing ? t('common.saving') : (order ? t('workOrders.form.saveChanges') : t('workOrders.form.create')) }}
                    </button>
                </div>
            </div>
        </form>
    </AppLayout>
</template>
