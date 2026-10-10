<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

function submit() {
    form.post('/login', {
        onFinish: () => form.reset('password'),
    });
}
</script>

<template>
    <div class="min-h-screen flex flex-col items-center justify-center px-4" style="background-color: var(--color-bg)">
        <!-- Logo -->
        <div class="mb-6 flex flex-col items-center gap-2">
            <div class="w-12 h-12 rounded-xl bg-brand-600 flex items-center justify-center shadow-sm">
                <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm0-12c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm0 6c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2z"/>
                </svg>
            </div>
            <div class="text-center">
                <h1 class="text-lg font-bold text-slate-900">Dundalk Tires</h1>
                <p class="text-sm text-slate-500">Sistema de Gestión</p>
            </div>
        </div>

        <!-- Card -->
        <div class="w-full max-w-sm rounded-xl shadow-sm p-6" style="background-color: var(--color-surface); border: 1px solid var(--color-border);">
            <h2 class="text-base font-semibold text-slate-800 mb-4">Iniciar sesión</h2>

            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <label class="label">Correo electrónico</label>
                    <input
                        v-model="form.email"
                        type="email"
                        required
                        autocomplete="email"
                        placeholder="usuario@dundalk.com"
                        class="w-full rounded-lg px-3 py-2 text-sm text-slate-900 placeholder-slate-400 outline-none transition focus:ring-2 focus:ring-brand-500"
                        style="border: 1px solid var(--color-border); background-color: var(--color-surface);"
                    />
                    <p v-if="form.errors.email" class="mt-1 text-xs text-red-600">{{ form.errors.email }}</p>
                </div>

                <div>
                    <label class="label">Contraseña</label>
                    <input
                        v-model="form.password"
                        type="password"
                        required
                        autocomplete="current-password"
                        placeholder="••••••••"
                        class="w-full rounded-lg px-3 py-2 text-sm text-slate-900 placeholder-slate-400 outline-none transition focus:ring-2 focus:ring-brand-500"
                        style="border: 1px solid var(--color-border); background-color: var(--color-surface);"
                    />
                    <p v-if="form.errors.password" class="mt-1 text-xs text-red-600">{{ form.errors.password }}</p>
                </div>

                <div class="flex items-center gap-2">
                    <input id="remember" v-model="form.remember" type="checkbox" class="rounded border-slate-300 text-brand-600 focus:ring-brand-500" />
                    <label for="remember" class="text-sm text-slate-600">Recordarme</label>
                </div>

                <p v-if="form.errors.general" class="text-sm text-red-600 bg-red-50 rounded-lg px-3 py-2">{{ form.errors.general }}</p>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full rounded-lg px-4 py-2 text-sm font-medium text-white bg-brand-600 hover:bg-brand-700 active:bg-brand-800 disabled:opacity-60 transition"
                >
                    {{ form.processing ? 'Ingresando...' : 'Iniciar sesión' }}
                </button>
            </form>
        </div>

        <p class="mt-4 text-xs text-slate-400">Dundalk Used &amp; New Tires · Sistema interno</p>
    </div>
</template>
