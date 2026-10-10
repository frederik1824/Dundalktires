<script setup>
import { usePage } from '@inertiajs/vue3';
import { watch } from 'vue';
import { useToast } from '@/composables/useToast.js';

const page = usePage();
const toast = useToast();

watch(() => page.props.flash, (flash) => {
    if (flash?.success) toast.success(flash.success);
    else if (flash?.error)   toast.error(flash.error);
    else if (flash?.warning) toast.warning(flash.warning);
    else if (flash?.info)    toast.info(flash.info);
}, { immediate: true, deep: true });
</script>

<template><slot /></template>
