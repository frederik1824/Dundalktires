<script setup>
import { useToast } from '@/composables/useToast.js';
const { toasts, remove } = useToast();

const icons = {
    success: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>`,
    error:   `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>`,
    warning: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>`,
    info:    `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>`,
};

const colors = {
    success: { border: '#22c55e', icon: '#16a34a', bg: '#f0fdf4' },
    error:   { border: '#ef4444', icon: '#dc2626', bg: '#fef2f2' },
    warning: { border: '#f59e0b', icon: '#d97706', bg: '#fffbeb' },
    info:    { border: '#3b82f6', icon: '#2563eb', bg: '#eff6ff' },
};
</script>

<template>
    <Teleport to="body">
        <div class="fixed top-4 right-4 z-[9999] flex flex-col gap-2 pointer-events-none" style="max-width: 360px; width: calc(100vw - 2rem)">
            <TransitionGroup name="toast">
                <div
                    v-for="toast in toasts"
                    :key="toast.id"
                    class="pointer-events-auto flex items-start gap-3 rounded-xl px-4 py-3 shadow-lg"
                    :style="`background:${colors[toast.type].bg}; border-left: 4px solid ${colors[toast.type].border};`"
                >
                    <!-- Icon -->
                    <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        :style="`color:${colors[toast.type].icon}`"
                        v-html="icons[toast.type]"
                    />
                    <!-- Message -->
                    <p class="flex-1 text-sm font-medium leading-snug" style="color:#1e293b">{{ toast.message }}</p>
                    <!-- Close -->
                    <button
                        @click="remove(toast.id)"
                        class="shrink-0 opacity-40 hover:opacity-80 transition-opacity"
                        style="color:#1e293b"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </TransitionGroup>
        </div>
    </Teleport>
</template>

<style scoped>
.toast-enter-active { transition: all 0.25s ease; }
.toast-leave-active { transition: all 0.2s ease; }
.toast-enter-from  { opacity: 0; transform: translateX(24px); }
.toast-leave-to    { opacity: 0; transform: translateX(24px); }
.toast-move        { transition: transform 0.2s ease; }
</style>
