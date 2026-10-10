<script setup>
import Icon from '@/Components/Icon.vue';
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { storeLocale } from '@/i18n/index.js';
import FlashMessage from '@/Components/FlashMessage.vue';
import ToastContainer from '@/Components/ToastContainer.vue';
import NotificationBell from '@/Components/NotificationBell.vue';
import { usePermissions } from '@/composables/usePermissions.js';

const { t, locale } = useI18n();
const { can, canAny } = usePermissions();
const page = usePage();
const user = computed(() => page.props.auth?.user);
const sidebarOpen = ref(false);
const lowStockCount  = computed(() => page.props.low_stock_count ?? 0);
const newLeadsCount  = computed(() => page.props.new_leads_count ?? 0);

const userRole = computed(() => user.value?.roles?.[0] ?? null);

const allNavItems = computed(() => [
    // ── OPERACIONES ──────────────────────────────────────
        { group: t('nav.groups.operations'), name: t('nav.dashboard'),     href: '/dashboard',              icon: 'grid',       perm: 'view_dashboard' },
    { group: t('nav.groups.operations'), name: 'POS (Punto de Venta)', href: '/pos',                    icon: 'revenue',    perm: 'manage_sales' },
    { group: t('nav.groups.operations'), name: t('nav.customers'),     href: '/customers',              icon: 'users',      perm: 'view_customers' },
    { group: t('nav.groups.operations'), name: t('nav.appointments'),  href: '/appointments',           icon: 'calendar',   perm: 'view_work_orders' },
    { group: t('nav.groups.operations'), name: t('nav.workOrders'),    href: '/work-orders',            icon: 'clipboard',  perm: 'view_work_orders' },
    { group: t('nav.groups.operations'), name: t('nav.pos'),           href: '/invoices',               icon: 'receipt',    perm: 'view_sales' },
    // ── INVENTARIO ───────────────────────────────────────
    { group: t('nav.groups.inventory'),  name: t('nav.tires'),         href: '/products?type=tire',     icon: 'tire',       perm: 'view_inventory' },
    { group: t('nav.groups.inventory'),  name: t('nav.materials'),     href: '/products?type=material', icon: 'box',        perm: 'view_inventory' },
    { group: t('nav.groups.inventory'),  name: t('nav.suppliers'),     href: '/suppliers',              icon: 'truck',      perm: 'view_inventory' },
    { group: t('nav.groups.inventory'),  name: t('nav.purchases'),     href: '/purchases',              icon: 'cart',       perm: 'view_purchases' },
    // ── FINANZAS ─────────────────────────────────────────
    { group: t('nav.groups.finance'),    name: t('nav.expenses'),      href: '/expenses',               icon: 'cash',       perm: 'view_expenses' },
    { group: t('nav.groups.finance'),    name: t('nav.arAging'),       href: '/reports/ar',             icon: 'receivable', perm: 'view_reports' },
    { group: t('nav.groups.finance'),    name: t('nav.cashClose'),     href: '/reports/cash-close',     icon: 'register',   perm: 'view_reports' },
    { group: t('nav.groups.finance'),    name: t('nav.reports'),       href: '/reports',                icon: 'chart',      perm: 'view_reports' },
    // ── ADMINISTRACIÓN ───────────────────────────────────
    { group: t('nav.groups.admin'),      name: t('nav.services'),      href: '/services',               icon: 'wrench',     perm: 'view_services' },
    { group: t('nav.groups.admin'),      name: t('nav.employees'),     href: '/employees',              icon: 'user-group', perm: 'view_employees' },
    { group: t('nav.groups.admin'),      name: t('nav.settings'),      href: '/settings',               icon: 'cog',        perm: 'manage_settings' },
    // ── SITIO WEB ─────────────────────────────────────────
    { group: t('nav.groups.landing'),    name: t('nav.webLeads'),      href: '/web-leads',              icon: 'mail',       perm: 'view_customers' },
    { group: t('nav.groups.landing'),    name: t('nav.promotions'),    href: '/landing/promotions',     icon: 'tag',        perm: 'manage_settings' },
    { group: t('nav.groups.landing'),    name: t('nav.testimonials'),  href: '/landing/testimonials',   icon: 'star',       perm: 'manage_settings' },
]);

const navigation = computed(() =>
    allNavItems.value.filter(item =>
        canAny(item.perm, item.perm.replace('view_', 'manage_'))
    )
);

// Group nav items by section, preserving order
const navGroups = computed(() => {
    const groups = [];
    let currentGroup = null;
    for (const item of navigation.value) {
        if (item.group !== currentGroup) {
            currentGroup = item.group;
            groups.push({ label: item.group, items: [] });
        }
        groups[groups.length - 1].items.push(item);
    }
    return groups;
});

function isActive(href) {
    if (href === '/') return false;
    const path = href.split('?')[0];
    const query = href.includes('?') ? href.split('?')[1] : null;
    if (!page.url.startsWith(path)) return false;
    if (query) return page.url.includes(query);
    return true;
}

function toggleLocale() {
    locale.value = locale.value === 'en' ? 'es' : 'en';
    storeLocale(locale.value);
}

// Oculta la barra de nav móvil en páginas de reportes (son back-office, navegación no aplica)
const hideBottomNav = computed(() => page.url.startsWith('/reports'));
</script>

<template>
    <div class="flex h-screen overflow-hidden" style="background-color: var(--color-bg)">
        <!-- Mobile overlay -->
        <div
            v-if="sidebarOpen"
            class="fixed inset-0 z-40 bg-black/30 lg:hidden"
            @click="sidebarOpen = false"
        />

        <!-- Sidebar -->
        <aside
            :class="[
                'fixed inset-y-0 left-0 z-50 flex w-60 flex-col transition-transform duration-300 lg:static lg:translate-x-0',
                sidebarOpen ? 'translate-x-0' : '-translate-x-full'
            ]"
            style="background-color: var(--color-surface); border-right: 1px solid var(--color-border);"
        >
            <!-- Logo -->
            <div class="h-16 shrink-0 flex items-center justify-center overflow-hidden" style="background: theme('colors.slate.900'); border-bottom:1px solid theme('colors.slate.800');">
                <Link href="/dashboard" class="flex items-center justify-center w-full h-full">
                    <img src="/logo.png" alt="Dundalk Used & New Tires" style="width: 85%; height: auto; transform: scale(1.2); object-fit: contain;" />
                </Link>
            </div>

            <!-- Nav -->
            <nav class="flex-1 overflow-y-auto py-2 px-2">
                <template v-for="group in navGroups" :key="group.label">
                    <!-- Group header -->
                    <p class="px-3 pt-3 pb-1 text-[10px] font-bold uppercase tracking-widest text-slate-400 select-none">
                        {{ group.label }}
                    </p>
                    <template v-for="item in group.items" :key="item.href">
                        <Link
                            prefetch
                            :href="item.href"
                            :class="[
                                'flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm mb-0.5 transition-colors group',
                                isActive(item.href)
                                    ? 'bg-brand-50 text-brand-700 font-semibold'
                                    : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'
                            ]"
                        >
                            <Icon
                                :name="item.icon"
                                class="w-4 h-4 shrink-0 transition-colors"
                                :class="isActive(item.href) ? 'text-brand-600' : 'text-slate-400 group-hover:text-slate-700'"
                            />
                            <span class="flex-1 truncate">{{ item.name }}</span>
                            <span v-if="lowStockCount > 0 && item.href === '/products?type=tire'"
                                class="ml-auto inline-flex items-center justify-center w-5 h-5 rounded-full bg-red-500 text-white text-[10px] font-bold">
                                {{ lowStockCount > 9 ? '9+' : lowStockCount }}
                            </span>
                            <span v-if="newLeadsCount > 0 && item.href === '/web-leads'"
                                class="ml-auto inline-flex items-center justify-center w-5 h-5 rounded-full bg-blue-500 text-white text-[10px] font-bold">
                                {{ newLeadsCount > 9 ? '9+' : newLeadsCount }}
                            </span>
                        </Link>
                    </template>
                </template>
            </nav>

            <!-- User -->
            <div class="shrink-0 p-3" style="border-top: 1px solid theme('colors.slate.800');">
                <div class="flex items-center gap-2.5 px-2 py-1.5 rounded-lg">
                    <Link href="/profile" class="w-7 h-7 rounded-full bg-brand-600 flex items-center justify-center text-white text-xs font-bold shrink-0 hover:bg-brand-700 transition-colors" title="My Profile">
                        {{ user?.name?.charAt(0)?.toUpperCase() ?? 'U' }}
                    </Link>
                    <div class="flex-1 min-w-0">
                        <Link href="/profile" class="text-sm font-medium truncate block hover:underline" style="color: var(--color-text)">{{ user?.name }}</Link>
                        <div class="text-xs truncate" style="color: var(--color-text-3)">
                            {{ userRole ?? user?.email }}
                        </div>
                    </div>
                    <Link href="/logout" method="post" as="button" class="shrink-0 text-slate-400 hover:text-slate-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </Link>
                </div>
            </div>
        </aside>

        <!-- Main content -->
        <div class="flex flex-1 flex-col min-w-0 overflow-hidden">
            <!-- Top bar -->
            <header class="flex h-14 items-center gap-3 px-4 shrink-0" style="background-color: var(--color-surface); border-bottom: 1px solid var(--color-border);">
                <button
                    class="lg:hidden p-1.5 rounded-lg text-slate-500 hover:bg-slate-100"
                    @click="sidebarOpen = !sidebarOpen"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                <div class="flex-1">
                    <!-- Title/Header is now moved to main content to avoid clipping -->
                </div>

                <div class="flex items-center gap-2">
                    <slot name="header-actions" />
                    <!-- Notification bell -->
                    <NotificationBell :low-stock-count="lowStockCount" />
                    <!-- Language toggle -->
                    <button
                        @click="toggleLocale"
                        class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-xs font-medium border border-border text-text-2 hover:bg-gray-100 transition-colors"
                        :title="locale === 'en' ? 'Switch to Spanish' : 'Cambiar a Inglés'"
                    >
                        <span>{{ locale === 'en' ? '🇺🇸 EN' : '🇪🇸 ES' }}</span>
                        <svg class="w-3 h-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    </button>
                </div>
            </header>

            <!-- Page content -->
            <main class="flex-1 overflow-y-auto p-4 pb-20 md:p-6 md:pb-6">
                <slot name="header" />
                <transition name="page" mode="out-in">
                    <div :key="$page.url.split('?')[0]">
                        <slot />
                    </div>
                </transition>
            </main>
        </div>
    
        <!-- Bottom Navigation Bar (Mobile Only — hidden on report pages) -->
        <nav v-if="!hideBottomNav" class="lg:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-border z-40 flex justify-around items-center h-16 select-none shadow-[0_-4px_10px_rgba(0,0,0,0.02)] pb-[env(safe-area-inset-bottom)]">
            <Link prefetch href="/dashboard" class="flex flex-col items-center justify-center w-full h-full text-slate-400 active:scale-95 transition-transform" :class="{'text-brand-600 font-semibold': isActive('/dashboard')}">
                <Icon name="grid" class="w-6 h-6 mb-1" />
                <span class="text-[10px]">Inicio</span>
            </Link>
            <Link prefetch href="/pos" class="flex flex-col items-center justify-center w-full h-full text-slate-400 active:scale-95 transition-transform" :class="{'text-brand-600 font-semibold': isActive('/pos')}">
                <Icon name="revenue" class="w-6 h-6 mb-1" />
                <span class="text-[10px]">POS</span>
            </Link>
            <Link prefetch href="/work-orders" class="flex flex-col items-center justify-center w-full h-full text-slate-400 active:scale-95 transition-transform" :class="{'text-brand-600 font-semibold': isActive('/work-orders')}">
                <Icon name="clipboard" class="w-6 h-6 mb-1" />
                <span class="text-[10px]">Órdenes</span>
            </Link>
            <Link prefetch href="/customers" class="flex flex-col items-center justify-center w-full h-full text-slate-400 active:scale-95 transition-transform" :class="{'text-brand-600 font-semibold': isActive('/customers')}">
                <Icon name="users" class="w-6 h-6 mb-1" />
                <span class="text-[10px]">Clientes</span>
            </Link>
            <button @click="sidebarOpen = true" class="flex flex-col items-center justify-center w-full h-full text-slate-400 active:scale-95 transition-transform">
                <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                <span class="text-[10px]">Menú</span>
            </button>
        </nav>
    </div>
    <FlashMessage />
    <ToastContainer />
</template>


