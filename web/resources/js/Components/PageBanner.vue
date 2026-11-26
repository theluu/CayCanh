<script setup>
import { Link } from '@inertiajs/vue3';
import Icon from './Icon.vue';

defineProps({
    title: { type: String, required: true },
    breadcrumbs: { type: Array, default: () => [] },
    image: { type: String, default: '/images/banner-inner.jpg' },
});
</script>

<template>
    <section class="relative bg-ink bg-cover bg-center" :style="{ backgroundImage: 'url(' + image + ')' }">
        <div class="absolute inset-0 bg-ink/70" />
        <div class="container-site relative py-16 md:py-20">
            <h1 class="text-3xl font-bold text-white md:text-5xl">{{ title }}</h1>
            <nav class="mt-4" aria-label="Breadcrumb">
                <ol class="flex flex-wrap items-center gap-1.5 text-sm text-gray-300">
                    <li><Link href="/" class="hover:text-white">Trang chủ</Link></li>
                    <li v-for="crumb in breadcrumbs" :key="crumb.label" class="flex items-center gap-1.5">
                        <Icon name="chevronRight" class="size-3" />
                        <Link v-if="crumb.href" :href="crumb.href" class="hover:text-white">{{ crumb.label }}</Link>
                        <span v-else class="text-brand-300">{{ crumb.label }}</span>
                    </li>
                </ol>
            </nav>
        </div>
    </section>
</template>
