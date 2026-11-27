<script setup>
import { Link } from '@inertiajs/vue3';
import Icon from './Icon.vue';

defineProps({
    title: { type: String, required: true },
    // Pages that render their own <h1> (product detail) pass "p" so each page keeps a single h1.
    headingTag: { type: String, default: 'h1' },
    breadcrumbs: { type: Array, default: () => [] },
    image: { type: String, default: '/images/theme/inner-banner.jpg' },
    button: { type: Object, default: () => ({ label: 'Liên hệ ngay', href: '/lien-he' }) },
});
</script>

<template>
    <section class="bg-brand-500 bg-cover bg-center" :style="{ backgroundImage: 'url(' + image + ')' }">
        <div class="container-site flex flex-col items-start justify-between gap-6 pt-14 pb-12 md:flex-row md:items-center lg:pt-[110px] lg:pb-[90px]">
            <component :is="headingTag" class="text-[34px] leading-tight text-white md:text-[42px]">{{ title }}</component>
            <Link v-if="button" :href="button.href" class="btn-white w-[180px] shrink-0 !text-brand-500 hover:!text-white">{{ button.label }}</Link>
        </div>
    </section>
    <nav class="bg-paper" aria-label="Breadcrumb">
        <ol class="container-site flex flex-wrap items-center justify-center gap-3 py-5 text-sm font-black text-[#9e9e9e]">
            <li><Link href="/" class="hover:text-brand-500">Trang chủ</Link></li>
            <li v-for="crumb in breadcrumbs" :key="crumb.label" class="flex items-center gap-3">
                <Icon name="chevronRight" class="size-3" />
                <Link v-if="crumb.href" :href="crumb.href" class="hover:text-brand-500">{{ crumb.label }}</Link>
                <span v-else class="text-brand-500">{{ crumb.label }}</span>
            </li>
        </ol>
    </nav>
</template>
