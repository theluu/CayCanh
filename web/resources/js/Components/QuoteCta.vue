<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    title: { type: String, default: 'Cây xanh cho công trình của bạn – từ tư vấn tới chăm sóc trọn gói.' },
    button: { type: String, default: 'Nhận báo giá' },
    href: { type: String, default: '/bao-gia' },
});
</script>

<template>
    <section class="bg-brand-500">
        <div class="container-site flex flex-col items-center justify-between gap-5 py-10 text-center md:flex-row md:text-left">
            <h2 class="font-serif text-2xl font-bold text-white md:text-3xl">{{ title }}</h2>
            <Link :href="href" class="btn shrink-0 bg-white text-brand-700 hover:bg-ink hover:text-white">{{ button }}</Link>
        </div>
    </section>
</template>
