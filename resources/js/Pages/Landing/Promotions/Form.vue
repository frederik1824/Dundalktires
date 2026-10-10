<script setup>
import { useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

const { t } = useI18n();

const props = defineProps({
    promotion: { type: Object, default: null },
});

const form = useForm({
    title:       props.promotion?.title       ?? '',
    description: props.promotion?.description ?? '',
    badge_text:  props.promotion?.badge_text  ?? '',
    cta_text:    props.promotion?.cta_text    ?? 'Book Now',
    is_active:   props.promotion?.is_active   ?? true,
    starts_at:   props.promotion?.starts_at   ?? '',
    ends_at:     props.promotion?.ends_at     ?? '',
    sort_order:  props.promotion?.sort_order  ?? 0,
});

function submit() {
    if (props.promotion) {
        form.put(`/landing/promotions/${props.promotion.id}`);
    } else {
        form.post('/landing/promotions');
    }
}
</script>

<template>
    <AppLayout>
        <template #header>
            <div class="page-header">
                <div>
                    <h1 class="page-title">
                        {{ promotion ? t('promotions.editPromotion') : t('promotions.createPromotion') }}
                    </h1>
                </div>
                <Link href="/landing/promotions" class="btn btn-ghost btn-sm">← {{ t('common.back') }}</Link>
            </div>
        </template>

        <form @submit.prevent="submit" class="card max-w-2xl space-y-5">
            <div>
                <label class="label">{{ t('promotions.promoTitle') }} *</label>
                <input v-model="form.title" type="text" class="input" required />
                <p v-if="form.errors.title" class="field-error">{{ form.errors.title }}</p>
            </div>

            <div>
                <label class="label">{{ t('promotions.description') }}</label>
                <textarea v-model="form.description" rows="3" class="input"></textarea>
                <p v-if="form.errors.description" class="field-error">{{ form.errors.description }}</p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="label">{{ t('promotions.badgeText') }}</label>
                    <input v-model="form.badge_text" type="text" class="input"
                        placeholder="e.g. Limited Time" />
                    <p v-if="form.errors.badge_text" class="field-error">{{ form.errors.badge_text }}</p>
                </div>
                <div>
                    <label class="label">{{ t('promotions.ctaText') }}</label>
                    <input v-model="form.cta_text" type="text" class="input" />
                    <p v-if="form.errors.cta_text" class="field-error">{{ form.errors.cta_text }}</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="label">{{ t('promotions.startsAt') }}</label>
                    <input v-model="form.starts_at" type="date" class="input" />
                    <p v-if="form.errors.starts_at" class="field-error">{{ form.errors.starts_at }}</p>
                </div>
                <div>
                    <label class="label">{{ t('promotions.endsAt') }}</label>
                    <input v-model="form.ends_at" type="date" class="input" />
                    <p v-if="form.errors.ends_at" class="field-error">{{ form.errors.ends_at }}</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="label">{{ t('promotions.sortOrder') }}</label>
                    <input v-model.number="form.sort_order" type="number" min="0" class="input w-24" />
                    <p v-if="form.errors.sort_order" class="field-error">{{ form.errors.sort_order }}</p>
                </div>
                <div class="flex items-center gap-2 pt-6">
                    <input v-model="form.is_active" type="checkbox" id="is_active" class="rounded" />
                    <label for="is_active" class="text-sm text-text cursor-pointer">
                        {{ t('promotions.isActive') }}
                    </label>
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" :disabled="form.processing" class="btn btn-primary">
                    {{ t('promotions.saveChanges') }}
                </button>
                <Link href="/landing/promotions" class="btn btn-ghost btn-sm">
                    {{ t('promotions.cancel') }}
                </Link>
            </div>
        </form>
    </AppLayout>
</template>
