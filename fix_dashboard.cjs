const fs = require('fs');

let file = 'resources/js/Pages/Dashboard/Index.vue';
let content = fs.readFileSync(file, 'utf8');

// Update chart colors to red
content = content.replace(/'rgba\(37, 99, 235, 0.15\)'/, "'rgba(220, 38, 38, 0.15)'");
content = content.replace(/'rgba\(37, 99, 235, 0.8\)'/, "'rgba(220, 38, 38, 0.8)'");

// Replace the entire template
const newTemplate = `
<template>
    <AppLayout>
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Panel de Control</h1>
            <p class="text-sm text-slate-500 mt-1">Aquí tienes el resumen y rendimiento de tu negocio hoy.</p>
        </div>

        <!-- KPIs Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div v-for="kpi in kpis" :key="kpi.label" class="card flex flex-col p-5 hover:shadow-md transition-shadow relative overflow-hidden group border border-slate-100">
                <div class="absolute -right-4 -top-4 opacity-5 group-hover:scale-125 transition-transform duration-500">
                    <Icon :name="kpi.icon" class="w-24 h-24" />
                </div>
                <div class="flex items-center justify-between mb-3 z-10">
                    <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wide">{{ kpi.label }}</h3>
                    <div :class="['w-8 h-8 rounded-full flex items-center justify-center shadow-sm', kpi.bg, kpi.color]">
                        <Icon :name="kpi.icon" class="w-4 h-4" />
                    </div>
                </div>
                <div class="z-10">
                    <p :class="['text-2xl font-black font-mono tracking-tight', kpi.color]">{{ kpi.value }}</p>
                    <p class="text-[10px] text-slate-400 font-medium mt-1 uppercase">{{ kpi.sub }}</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Revenue Chart -->
            <div class="lg:col-span-2 card flex flex-col border border-slate-100">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-bold text-slate-700">Ingresos (Últimos 7 días)</h2>
                </div>
                <div class="flex-1 min-h-[250px] relative">
                    <canvas ref="chartRef"></canvas>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card border border-slate-100 bg-slate-50/50">
                <h2 class="font-bold text-slate-700 mb-4">Acciones Rápidas</h2>
                <div class="grid grid-cols-1 gap-3">
                    <Link href="/pos" class="flex items-center p-3 rounded-xl bg-white border border-slate-200 hover:border-brand-500 hover:shadow-sm transition-all group">
                        <div class="w-10 h-10 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center mr-3 group-hover:scale-110 transition-transform"><Icon name="revenue" /></div>
                        <div>
                            <p class="text-sm font-bold text-slate-700 group-hover:text-brand-700 transition-colors">Nueva Venta (POS)</p>
                            <p class="text-xs text-slate-400">Crear factura rápida</p>
                        </div>
                    </Link>
                    <Link href="/work-orders/create" class="flex items-center p-3 rounded-xl bg-white border border-slate-200 hover:border-amber-500 hover:shadow-sm transition-all group">
                        <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center mr-3 group-hover:scale-110 transition-transform"><Icon name="wrench" /></div>
                        <div>
                            <p class="text-sm font-bold text-slate-700 group-hover:text-amber-700 transition-colors">Nueva Orden de Trabajo</p>
                            <p class="text-xs text-slate-400">Ingresar vehículo al taller</p>
                        </div>
                    </Link>
                    <Link href="/customers/create" class="flex items-center p-3 rounded-xl bg-white border border-slate-200 hover:border-emerald-500 hover:shadow-sm transition-all group">
                        <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center mr-3 group-hover:scale-110 transition-transform"><Icon name="user" /></div>
                        <div>
                            <p class="text-sm font-bold text-slate-700 group-hover:text-emerald-700 transition-colors">Nuevo Cliente</p>
                            <p class="text-xs text-slate-400">Registrar cliente y vehículo</p>
                        </div>
                    </Link>
                    <Link href="/purchases/create" class="flex items-center p-3 rounded-xl bg-white border border-slate-200 hover:border-violet-500 hover:shadow-sm transition-all group">
                        <div class="w-10 h-10 rounded-lg bg-violet-50 text-violet-600 flex items-center justify-center mr-3 group-hover:scale-110 transition-transform"><Icon name="cart" /></div>
                        <div>
                            <p class="text-sm font-bold text-slate-700 group-hover:text-violet-700 transition-colors">Nueva Compra</p>
                            <p class="text-xs text-slate-400">Ingresar inventario</p>
                        </div>
                    </Link>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent work orders -->
            <div class="card border border-slate-100 p-0 overflow-hidden flex flex-col">
                <div class="p-5 flex items-center justify-between border-b border-border">
                    <h2 class="font-bold text-slate-700">Órdenes Recientes</h2>
                    <Link href="/work-orders" class="text-xs text-brand-600 font-semibold hover:underline">Ver todas</Link>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm" v-if="recent_orders.length">
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="order in recent_orders" :key="order.id" class="hover:bg-slate-50 transition-colors">
                                <td class="p-4">
                                    <Link :href="\`/work-orders/\${order.id}\`" class="font-mono text-sm font-bold text-brand-700 hover:underline block mb-0.5">
                                        {{ order.number }}
                                    </Link>
                                    <span :class="['badge', statusColors[order.status] ?? 'badge-gray']">{{ t('workOrders.statuses.' + order.status) }}</span>
                                </td>
                                <td class="p-4">
                                    <p class="text-sm text-slate-800 font-medium">{{ order.customer?.name ?? t('common.walkIn') }}</p>
                                    <p v-if="order.vehicle" class="text-xs text-slate-500 mt-0.5">
                                        {{ order.vehicle.year }} {{ order.vehicle.make }} {{ order.vehicle.model }}
                                    </p>
                                </td>
                                <td class="p-4 text-right">
                                    <p class="font-mono font-semibold text-slate-700">\${{ order.total }}</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div v-else class="p-10 flex flex-col items-center justify-center text-center">
                        <div class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center text-slate-300 mb-3"><Icon name="clipboard" class="w-6 h-6"/></div>
                        <p class="text-sm font-medium text-slate-600">No hay órdenes recientes</p>
                    </div>
                </div>
            </div>

            <!-- Low stock alert panel -->
            <div class="card border border-red-100 p-0 overflow-hidden flex flex-col" v-if="low_stock_items?.length">
                <div class="p-5 flex items-center justify-between border-b border-red-100 bg-red-50/30">
                    <h2 class="font-bold text-red-600 flex items-center gap-2">
                        <Icon name="alert" class="w-5 h-5"/>
                        Inventario Bajo ({{ low_stock_items.length }})
                    </h2>
                    <Link href="/products?low_stock=true" class="text-xs text-red-600 font-semibold hover:underline">Ver inventario</Link>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="p in low_stock_items.slice(0, 5)" :key="p.id" class="hover:bg-red-50/50 transition-colors">
                                <td class="p-4">
                                    <p class="font-medium text-slate-800">{{ p.name }}</p>
                                    <p class="font-mono text-xs text-slate-400 mt-0.5">{{ p.sku ?? '—' }}</p>
                                </td>
                                <td class="p-4 text-center">
                                    <span class="inline-block px-2 py-1 rounded bg-red-100 text-red-700 font-bold text-xs">
                                        Quedan {{ p.stock_qty }}
                                    </span>
                                </td>
                                <td class="p-4 text-right">
                                    <Link :href="\`/products/\${p.id}\`" class="btn btn-sm bg-white border border-slate-200 text-slate-600 shadow-sm">Ver</Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div v-if="low_stock_items.length > 5" class="p-3 text-center bg-slate-50 border-t border-slate-100">
                        <Link href="/products?low_stock=true" class="text-xs font-medium text-slate-500 hover:text-brand-600">+ {{ low_stock_items.length - 5 }} productos más</Link>
                    </div>
                </div>
            </div>
            
            <div v-else class="card border border-emerald-100 bg-emerald-50/30 p-10 flex flex-col items-center justify-center text-center">
                <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <h3 class="font-bold text-emerald-800">Inventario Saludable</h3>
                <p class="text-sm text-emerald-600/80 mt-1">Ningún producto está por debajo de su límite mínimo.</p>
            </div>
        </div>
    </AppLayout>
</template>
`;

content = content.replace(/<template>[\s\S]*<\/template>/, newTemplate);

fs.writeFileSync(file, content);
