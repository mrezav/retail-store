<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

const { pagination } = defineProps<{
    pagination: {
        current_page: number;
        from: number;
        last_page: number;
        total: number;
        per_page: number;
        links: Array<{
            url: string | null;
            label: string;
            page: number | null;
            active: boolean;
        }>;
    };
}>();

</script>

<template>
    <nav class="flex items-center justify-center gap-2 mt-6">
        <template v-for="link in pagination.links" :key="link.label">
            <Link preserve-scroll :href="link.url ?? ''">
            <button class="flex item-center justify-center px-4 py-2 rounded-lg text-gray-500 text-sm"
                :class="{ 'bg-blue-500 text-white': link.active, 'hover:bg-gray-200': !link.active, '!text-gray-300 cursor-not-allowed': !link.url, 'cursor-pointer': link.url }"
                :disabled="!link.url">
                <span v-html="link.label"></span>
            </button>
            </Link>
        </template>
    </nav>
</template>

<style lang="scss" scoped></style>