<script setup>
import { useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '@/Layouts/AppLayout.vue';

const { t } = useI18n();

const props = defineProps({ user: Object });

const profileForm = useForm({
    name:  props.user.name,
    email: props.user.email,
});

const passwordForm = useForm({
    current_password:      '',
    password:              '',
    password_confirmation: '',
});

function updateProfile() {
    profileForm.put('/profile', { preserveScroll: true });
}

function changePassword() {
    passwordForm.post('/profile/password', {
        preserveScroll: true,
        onSuccess: () => passwordForm.reset(),
    });
}
</script>

<template>
    <AppLayout>
        <template #header>
            <div class="page-header">
                <h1 class="page-title">My Profile</h1>
            </div>
        </template>

        <div class="max-w-2xl space-y-6">

            <!-- Profile info -->
            <div class="card">
                <h2 class="text-base font-semibold mb-4" style="color:var(--color-text)">Profile Information</h2>
                <form @submit.prevent="updateProfile" class="space-y-4">
                    <div class="form-group">
                        <label class="label">Name</label>
                        <input v-model="profileForm.name" type="text" class="input" required />
                        <p v-if="profileForm.errors.name" class="form-error">{{ profileForm.errors.name }}</p>
                    </div>
                    <div class="form-group">
                        <label class="label">Email</label>
                        <input v-model="profileForm.email" type="email" class="input" required />
                        <p v-if="profileForm.errors.email" class="form-error">{{ profileForm.errors.email }}</p>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="btn btn-primary" :disabled="profileForm.processing">
                            {{ profileForm.processing ? 'Saving…' : 'Save' }}
                        </button>
                    </div>
                </form>
            </div>

            <!-- Password change -->
            <div class="card">
                <h2 class="text-base font-semibold mb-4" style="color:var(--color-text)">Change Password</h2>
                <form @submit.prevent="changePassword" class="space-y-4">
                    <div class="form-group">
                        <label class="label">Current Password</label>
                        <input v-model="passwordForm.current_password" type="password" class="input" required autocomplete="current-password" />
                        <p v-if="passwordForm.errors.current_password" class="form-error">{{ passwordForm.errors.current_password }}</p>
                    </div>
                    <div class="form-group">
                        <label class="label">New Password</label>
                        <input v-model="passwordForm.password" type="password" class="input" required autocomplete="new-password" />
                        <p v-if="passwordForm.errors.password" class="form-error">{{ passwordForm.errors.password }}</p>
                    </div>
                    <div class="form-group">
                        <label class="label">Confirm New Password</label>
                        <input v-model="passwordForm.password_confirmation" type="password" class="input" required autocomplete="new-password" />
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="btn btn-primary" :disabled="passwordForm.processing">
                            {{ passwordForm.processing ? 'Changing…' : 'Change Password' }}
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </AppLayout>
</template>
