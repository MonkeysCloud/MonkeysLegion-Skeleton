<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { FormEvent } from 'vue';

const form = useForm({
    email: '',
    password: '',
});

const submit = (e: FormEvent) => {
    e.preventDefault();
    form.post('/login', {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-50">
        <Head title="Login" />
        <div class="card w-full max-w-md">
            <h1 class="text-2xl font-bold text-center mb-6">Sign In</h1>
            <form @submit="submit" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" class="input" v-model="form.email" required />
                    <p v-if="form.errors.email" class="text-red-500 text-sm mt-1">{{ form.errors.email }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" class="input" v-model="form.password" required />
                    <p v-if="form.errors.password" class="text-red-500 text-sm mt-1">{{ form.errors.password }}</p>
                </div>
                <button type="submit" :disabled="form.processing" class="btn-primary w-full">
                    {{ form.processing ? 'Signing in...' : 'Sign In' }}
                </button>
            </form>
            <p class="text-center mt-4 text-sm text-gray-600">
                Don't have an account?
                <Link href="/register" class="text-ml-primary-600 hover:underline">Register</Link>
            </p>
        </div>
    </div>
</template>
