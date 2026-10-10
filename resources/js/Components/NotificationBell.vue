<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    lowStockCount: { type: Number, default: 0 },
});

const open = ref(false);
const bellRef = ref(null);

function toggle() { open.value = !open.value; }

function onClickOutside(e) {
    if (bellRef.value && !bellRef.value.contains(e.target)) open.value = false;
}

onMounted(() => document.addEventListener('mousedown', onClickOutside));
onUnmounted(() => document.removeEventListener('mousedown', onClickOutside));

const totalCount = () => props.lowStockCount;
</script>

<template>
    <div ref="bellRef" class="relative">
        <!-- Bell button -->
        <button
            @click="toggle"
            class="relative flex items-center justify-center w-9 h-9 rounded-lg transition-colors"
            :class="open ? 'bg-slate-100 text-slate-700' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-700'"
            title="Notificaciones"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            <!-- Badge -->
            <span v-if="lowStockCount > 0"
                class="absolute -top-0.5 -right-0.5 flex items-center justify-center min-w-[16px] h-4 px-1 rounded-full bg-red-500 text-white text-[10px] font-bold leading-none">
                {{ lowStockCount > 99 ? '99+' : lowStockCount }}
            </span>
        </button>

        <!-- Dropdown panel -->
        <Transition name="bell-drop">
            <div v-if="open"
                class="absolute right-0 mt-2 w-80 rounded-xl shadow-xl overflow-hidden z-50"
                style="background:#fff; border:1px solid #e2e8f0;"
            >
                <!-- Header -->
                <div class="flex items-center justify-between px-4 py-3" style="border-bottom:1px solid #e2e8f0;">
                    <span class="text-sm font-semibold text-slate-800">Notificaciones</span>
                    <span v-if="lowStockCount > 0"
                        class="inline-flex items-center gap-1 text-xs font-medium text-red-600 bg-red-50 px-2 py-0.5 rounded-full">
                        {{ lowStockCount }} activa{{ lowStockCount !== 1 ? 's' : '' }}
                    </span>
                </div>

                <!-- Notifications list -->
                <div class="max-h-72 overflow-y-auto">
                    <!-- Low stock alert -->
                    <div v-if="lowStockCount > 0" class="flex items-start gap-3 px-4 py-3 hover:bg-slate-50 transition-colors">
                        <div class="mt-0.5 flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center bg-amber-100">
                            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-slate-800">
                                Stock bajo detectado
                            </p>
                            <p class="text-xs text-slate-500 mt-0.5">
                                {{ lowStockCount }} producto{{ lowStockCount !== 1 ? 's' : '' }} bajo el mínimo
                            </p>
                            <Link href="/products?low_stock=true" @click="open = false"
                                class="inline-flex items-center gap-1 text-xs font-medium text-amber-600 hover:text-amber-800 mt-1.5 transition-colors">
                                Ver productos →
                            </Link>
                        </div>
                    </div>

                    <!-- Empty state -->
                    <div v-if="lowStockCount === 0" class="flex flex-col items-center justify-center py-10 text-center px-4">
                        <svg class="w-10 h-10 text-slate-200 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <p class="text-sm text-slate-400">Todo en orden</p>
                        <p class="text-xs text-slate-300 mt-0.5">Sin alertas pendientes</p>
                    </div>
                </div>
            </div>
        </Transition>
    </div>
</template>

<style scoped>
.bell-drop-enter-active { transition: all 0.18s ease; }
.bell-drop-leave-active { transition: all 0.15s ease; }
.bell-drop-enter-from  { opacity: 0; transform: translateY(-6px) scale(0.97); }
.bell-drop-leave-to    { opacity: 0; transform: translateY(-4px) scale(0.97); }
</style>
