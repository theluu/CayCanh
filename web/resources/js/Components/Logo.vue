<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    name: { type: String, default: 'Cây Cảnh' },
    light: { type: Boolean, default: false },
});
</script>

<template>
    <Link href="/" class="inline-flex items-center gap-3" :aria-label="name + ' – Trang chủ'">
        <img src="/images/logo.png" srcset="/images/logo.png 1x, /images/logo@2x.png 2x" :alt="name" width="480" height="320"
            class="h-14 w-auto sm:h-[72px]" fetchpriority="high" />
        <span class="leading-none">
            <span class="block font-serif text-[26px] font-bold text-brand-500 sm:text-[32px]">{{ name }}</span>
            <span class="mt-1.5 hidden font-serif text-sm whitespace-nowrap sm:block" :class="light ? 'text-gray-400' : 'text-muted'">Cây xanh &amp; cây cảnh nghệ thuật</span>
        </span>
    </Link>
</template>
