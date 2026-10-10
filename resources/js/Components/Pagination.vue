<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    links: Array,
});
</script>

<template>
    <div v-if="links && links.length > 3" class="flex items-center gap-1 flex-wrap">
        <template v-for="link in links" :key="link.label">
            <Link
                v-if="link.url"
                :href="link.url"
                v-html="link.label"
                :class="[
                    'px-3 py-1.5 text-sm rounded-lg transition-colors',
                    link.active
                        ? 'bg-brand-600 text-white font-semibold'
                        : 'text-text-2 hover:bg-slate-100'
                ]"
            />
            <span v-else v-html="link.label"
                class="px-3 py-1.5 text-sm text-text-3 cursor-not-allowed" />
        </template>
    </div>
</template>
