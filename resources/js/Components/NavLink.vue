<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

defineProps<{
    href: string;
    active?: boolean;
}>();
</script>

<template>
    <Link
        :href="href"
        :class="[
            'px-3 py-2 rounded-md text-sm font-medium transition-colors',
            active
                ? 'bg-ml-primary-100 text-ml-primary-700'
                : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100'
        ]"
    >
        <slot />
    </Link>
</template>
