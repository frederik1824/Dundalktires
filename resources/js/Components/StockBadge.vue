<template>
  <span :class="badgeClass">{{ label }}</span>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  qty: { type: Number, required: true },
  min: { type: Number, default: 0 },
})

const isOut  = computed(() => props.qty <= 0)
const isLow  = computed(() => !isOut.value && props.qty <= props.min)

const label = computed(() => {
  if (isOut.value) return 'Out of stock'
  if (isLow.value) return `Low (${props.qty})`
  return props.qty.toString()
})

const badgeClass = computed(() => {
  if (isOut.value) return 'inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-50 text-red-700'
  if (isLow.value) return 'inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-50 text-amber-700'
  return 'inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-50 text-emerald-700'
})
</script>
