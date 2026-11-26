<script setup>
import { Link } from '@inertiajs/vue3';
import Icon from './Icon.vue';

defineProps({
    name: { type: String, default: 'Cây Cảnh' },
    light: { type: Boolean, default: false },
});
</script>

<template>
    <Link href="/" class="group inline-flex items-center gap-2.5" aria-label="Trang chủ">
        <span class="grid size-11 place-items-center rounded-full bg-brand-500 text-white shadow-sm">
            <Icon name="leaf" class="size-6" />
        </span>
        <span class="leading-tight">
            <span class="block font-serif text-2xl font-bold" :class="light ? 'text-white' : 'text-ink'">{{ name }}</span>
            <span class="block text-[11px] tracking-[0.2em] uppercase" :class="light ? 'text-gray-400' : 'text-muted'">Cây xanh · Cảnh quan</span>
        </span>
    </Link>
</template>
