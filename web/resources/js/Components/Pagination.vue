<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    meta: { type: Object, required: true },
});

const label = (text) => text.replace('&laquo; Previous', '‹').replace('Next &raquo;', '›').replace('pagination.previous', '‹').replace('pagination.next', '›');
</script>

<template>
    <nav v-if="meta.last_page > 1" class="mt-12 flex flex-wrap justify-center gap-2" aria-label="Phân trang">
        <template v-for="(link, index) in meta.links" :key="index">
            <Link v-if="link.url" :href="link.url" preserve-scroll
                class="grid min-w-10 place-items-center border px-3 py-2 text-sm font-medium"
                :class="link.active ? 'border-brand-500 bg-brand-500 text-white' : 'border-gray-200 hover:border-brand-500 hover:text-brand-600'"
                v-html="label(link.label)" />
            <span v-else class="grid min-w-10 place-items-center border border-gray-100 px-3 py-2 text-sm text-gray-300" v-html="label(link.label)" />
        </template>
    </nav>
</template>
