<script setup lang="ts">
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import NavLink from '@/Components/NavLink.vue';

defineProps<{ title?: string }>();

const auth = computed(() => (usePage().props as any).auth);
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <nav class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center space-x-8">
                        <Link href="/dashboard" class="text-xl font-bold text-ml-primary-600">
                            MonKeysLegion
                        </Link>
                        <div class="hidden md:flex space-x-4">
                            <NavLink href="/dashboard" :active="$page.url === '/dashboard'">
                                Dashboard
                            </NavLink>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span v-if="auth?.user" class="text-sm text-gray-600">
                            {{ auth.user.name }}
                        </span>
                        <Link v-else href="/login" class="text-sm text-ml-primary-600 hover:underline">
                            Login
                        </Link>
                    </div>
                </div>
            </div>
        </nav>

        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <h1 v-if="title" class="text-2xl font-bold mb-6">{{ title }}</h1>
            <slot />
        </main>
    </div>
</template>
