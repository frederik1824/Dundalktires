<script setup>
import { computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

const { t } = useI18n();

const props = defineProps({
    testimonial: { type: Object, default: null },
});

const form = useForm({
    author_name: props.testimonial?.author_name ?? '',
    rating:      props.testimonial?.rating      ?? 5,
    body:        props.testimonial?.body        ?? '',
    reviewed_at: props.testimonial?.reviewed_at ?? '',
    is_active:   props.testimonial?.is_active   ?? true,
    sort_order:  props.testimonial?.sort_order  ?? 0,
});

const starLabels = ['Terrible', 'Bad', 'Average', 'Good', 'Excellent'];

const preview = computed(() => {
    if (!form.author_name && !form.body) return null;
    return {
        initials: form.author_name
            ? form.author_name.split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2)
            : '?',
        stars: '★'.repeat(form.rating) + '☆'.repeat(5 - form.rating),
    };
});

function submit() {
    if (props.testimonial) {
        form.put(`/landing/testimonials/${props.testimonial.id}`);
    } else {
        form.post('/landing/testimonials');
    }
}
</script>

<template>
    <AppLayout>
        <template #header>
            <div class="page-header">
                <div>
                    <h1 class="page-title">
                        {{ testimonial ? t('testimonials.editReview') : t('testimonials.createReview') }}
                    </h1>
                </div>
                <Link href="/landing/testimonials" class="btn btn-ghost btn-sm">← {{ t('common.back') }}</Link>
            </div>
        </template>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 max-w-4xl">
            <!-- Form -->
            <form @submit.prevent="submit" class="card space-y-5">
                <div>
                    <label class="label">{{ t('testimonials.authorName') }} *</label>
                    <input v-model="form.author_name" type="text" class="input"
                        placeholder="e.g. John D." required />
                    <p v-if="form.errors.author_name" class="field-error">{{ form.errors.author_name }}</p>
                </div>

                <!-- Star rating picker -->
                <div>
                    <label class="label">{{ t('testimonials.rating') }} *</label>
                    <div class="flex items-center gap-1 mt-1">
                        <button v-for="n in 5" :key="n" type="button"
                            @click="form.rating = n"
                            :class="['text-2xl transition-colors', n <= form.rating ? 'text-yellow-400' : 'text-gray-300 hover:text-yellow-300']">
                            ★
                        </button>
                        <span class="ml-2 text-sm text-text-2">{{ starLabels[form.rating - 1] }}</span>
                    </div>
                    <p v-if="form.errors.rating" class="field-error">{{ form.errors.rating }}</p>
                </div>

                <div>
                    <label class="label">{{ t('testimonials.body') }} *</label>
                    <textarea v-model="form.body" rows="5" class="input"
                        placeholder="Paste the review text exactly as written by the customer…"
                        required></textarea>
                    <p class="text-xs text-text-3 mt-1">{{ form.body.length }} / 1000</p>
                    <p v-if="form.errors.body" class="field-error">{{ form.errors.body }}</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="label">{{ t('testimonials.reviewedAt') }}</label>
                        <input v-model="form.reviewed_at" type="date" class="input" />
                        <p v-if="form.errors.reviewed_at" class="field-error">{{ form.errors.reviewed_at }}</p>
                    </div>
                    <div>
                        <label class="label">{{ t('testimonials.sortOrder') }}</label>
                        <input v-model.number="form.sort_order" type="number" min="0" class="input" />
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <input v-model="form.is_active" type="checkbox" id="is_active" class="rounded" />
                    <label for="is_active" class="text-sm text-text cursor-pointer">
                        {{ t('testimonials.isActive') }}
                    </label>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" :disabled="form.processing" class="btn btn-primary">
                        {{ t('testimonials.saveChanges') }}
                    </button>
                    <Link href="/landing/testimonials" class="btn btn-ghost btn-sm">
                        {{ t('testimonials.cancel') }}
                    </Link>
                </div>
            </form>

            <!-- Live preview -->
            <div v-if="preview" class="card bg-gray-50 flex flex-col gap-3">
                <p class="text-xs font-bold uppercase tracking-widest text-text-3">Preview</p>
                <div class="bg-white rounded-lg p-5 shadow-sm border border-gray-200">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-full bg-red-600 flex items-center justify-center text-white font-bold text-sm shrink-0">
                            {{ preview.initials }}
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 text-sm">{{ form.author_name || 'Customer Name' }}</p>
                            <p class="text-yellow-400 text-sm tracking-wider">{{ preview.stars }}</p>
                        </div>
                        <!-- Google G logo placeholder -->
                        <div class="ml-auto w-6 h-6 rounded-full bg-white border border-gray-200 flex items-center justify-center">
                            <span class="text-xs font-bold text-blue-600">G</span>
                        </div>
                    </div>
                    <p class="text-gray-700 text-sm leading-relaxed">{{ form.body || 'Review text will appear here…' }}</p>
                    <p v-if="form.reviewed_at" class="text-xs text-gray-400 mt-2">{{ form.reviewed_at }}</p>
                </div>
                <p class="text-xs text-text-3">Así se verá esta reseña en la página pública.</p>
            </div>
        </div>
    </AppLayout>
</template>
