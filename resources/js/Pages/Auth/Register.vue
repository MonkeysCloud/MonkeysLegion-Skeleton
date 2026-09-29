<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { FormEvent } from 'vue';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = (e: FormEvent) => {
    e.preventDefault();
    form.post('/register', {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-50">
        <Head title="Register" />
        <div class="card w-full max-w-md">
            <h1 class="text-2xl font-bold text-center mb-6">Create Account</h1>
            <form @submit="submit" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                    <input type="text" class="input" v-model="form.name" required />
                    <p v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</p>
                </div>
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
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                    <input type="password" class="input" v-model="form.password_confirmation" required />
                </div>
                <button type="submit" :disabled="form.processing" class="btn-primary w-full">
                    {{ form.processing ? 'Creating...' : 'Register' }}
                </button>
            </form>
            <p class="text-center mt-4 text-sm text-gray-600">
                Already have an account?
                <Link href="/login" class="text-ml-primary-600 hover:underline">Sign In</Link>
            </p>
        </div>
    </div>
</template>
