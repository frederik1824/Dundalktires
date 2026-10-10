<template>
  <AppLayout>
    <template #header>
      <div class="flex items-center gap-2 text-sm">
        <Link :href="route('suppliers.index')" class="text-slate-400 hover:text-slate-700">{{ t('nav.suppliers') }}</Link>
        <span class="text-slate-300">/</span>
        <span class="font-semibold text-slate-800">{{ isEdit ? supplier.name : t('inventory.newSupplier') }}</span>
      </div>
    </template>

    <form @submit.prevent="submit" class="max-w-2xl">
      <h1 class="page-title mb-5">{{ isEdit ? t('inventory.editSupplier') : t('inventory.newSupplier') }}</h1>
      <div class="card space-y-5">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div class="sm:col-span-2">
            <label class="label">{{ t('inventory.companyName') }} <span class="text-red-500">*</span></label>
            <input v-model="form.name" type="text" class="input" :class="{'input-error': form.errors.name}" required />
            <p v-if="form.errors.name" class="field-error">{{ form.errors.name }}</p>
          </div>

          <div>
            <label class="label">{{ t('inventory.contactPerson') }}</label>
            <input v-model="form.contact_name" type="text" class="input" />
          </div>

          <div>
            <label class="label">{{ t('customers.form.phone') }}</label>
            <input v-model="form.phone" type="tel" class="input" />
          </div>

          <div>
            <label class="label">{{ t('customers.form.email') }}</label>
            <input v-model="form.email" type="email" class="input" :class="{'input-error': form.errors.email}" />
            <p v-if="form.errors.email" class="field-error">{{ form.errors.email }}</p>
          </div>

          <div>
            <label class="label">{{ t('common.status') }}</label>
            <label class="flex items-center gap-2 mt-2 cursor-pointer">
              <input v-model="form.is_active" type="checkbox" class="rounded" />
              <span class="text-sm text-text">{{ t('inventory.activeSupplier') }}</span>
            </label>
          </div>

          <div class="sm:col-span-2">
            <label class="label">{{ t('customers.form.address') }}</label>
            <input v-model="form.address" type="text" class="input" />
          </div>

          <div class="sm:col-span-2">
            <label class="label">{{ t('common.notes') }}</label>
            <textarea v-model="form.notes" rows="3" class="input"></textarea>
          </div>
        </div>
      </div>

      <div class="flex justify-end gap-3 mt-4">
        <Link :href="route('suppliers.index')" class="btn btn-ghost">{{ t('common.cancel') }}</Link>
        <button type="submit" :disabled="form.processing" class="btn btn-primary">
          {{ form.processing ? t('common.saving') : (isEdit ? t('common.save') : t('inventory.createSupplier')) }}
        </button>
      </div>
    </form>
  </AppLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import AppLayout from '@/Layouts/AppLayout.vue'

const { t } = useI18n()

const props = defineProps({
  supplier: { type: Object, default: null },
})

const isEdit = computed(() => !!props.supplier)

const form = useForm({
  name:         props.supplier?.name || '',
  contact_name: props.supplier?.contact_name || '',
  phone:        props.supplier?.phone || '',
  email:        props.supplier?.email || '',
  address:      props.supplier?.address || '',
  notes:        props.supplier?.notes || '',
  is_active:    props.supplier?.is_active ?? true,
})

function submit() {
  if (isEdit.value) {
    form.put(route('suppliers.update', props.supplier.id))
  } else {
    form.post(route('suppliers.store'))
  }
}
</script>
