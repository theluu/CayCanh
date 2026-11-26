<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    title: { type: String, default: 'Mang mảng xanh bền vững vào công trình của bạn.' },
    button: { type: String, default: 'Nhận báo giá' },
    href: { type: String, default: '/bao-gia' },
});
</script>

<template>
    <section class="relative bg-brand-500 bg-[url('/images/theme/21.jpg')] bg-cover bg-center">
        <div class="absolute inset-0 bg-brand-500/85" aria-hidden="true" />
        <div class="container-site relative flex flex-col items-center justify-between gap-6 py-10 text-center md:flex-row md:text-left">
            <h2 class="font-serif text-2xl font-bold text-white md:text-[30px]">{{ title }}</h2>
            <Link :href="href" class="btn-white w-60 shrink-0">{{ button }}</Link>
        </div>
    </section>
</template>
