<script setup lang="ts">
import { computed } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

interface DashboardProps {
    stats: {
        users: number;
        posts: number;
        comments: number;
    };
}

const props = defineProps<DashboardProps>();

const flash = computed(() => (usePage().props as any).flash);
</script>

<template>
    <AuthenticatedLayout title="Dashboard">
        <Head title="Dashboard" />

        <div v-if="flash?.success" class="mb-4 rounded-lg bg-green-50 p-4 text-green-800">
            {{ flash.success }}
        </div>
        <div v-if="flash?.error" class="mb-4 rounded-lg bg-red-50 p-4 text-red-800">
            {{ flash.error }}
        </div>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
            <div class="card">
                <h3 class="text-lg font-semibold text-gray-700">Users</h3>
                <p class="text-3xl font-bold text-ml-primary-600">{{ props.stats.users }}</p>
            </div>
            <div class="card">
                <h3 class="text-lg font-semibold text-gray-700">Posts</h3>
                <p class="text-3xl font-bold text-ml-primary-600">{{ props.stats.posts }}</p>
            </div>
            <div class="card">
                <h3 class="text-lg font-semibold text-gray-700">Comments</h3>
                <p class="text-3xl font-bold text-ml-primary-600">{{ props.stats.comments }}</p>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
