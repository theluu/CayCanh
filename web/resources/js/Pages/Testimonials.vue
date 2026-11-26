<script setup>
import Icon from '@/Components/Icon.vue';
import PageBanner from '@/Components/PageBanner.vue';
import Picture from '@/Components/Picture.vue';
import QuoteCta from '@/Components/QuoteCta.vue';
import SeoHead from '@/Components/SeoHead.vue';
import SiteLayout from '@/Layouts/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

defineProps({ seo: Object, testimonials: Array });
</script>

<template>
    <SeoHead :seo="seo" />
    <PageBanner title="Đánh giá khách hàng" :breadcrumbs="[{ label: 'Đánh giá khách hàng' }]" />
    <section class="container-site grid gap-6 py-20 md:grid-cols-2 lg:grid-cols-3">
        <figure v-for="item in testimonials" :key="item.id" class="flex flex-col border border-gray-100 bg-paper p-7">
            <Icon name="quote" class="size-8 text-brand-500" />
            <blockquote class="mt-4 flex-1 leading-relaxed text-muted italic">“{{ item.content }}”</blockquote>
            <figcaption class="mt-6 flex items-center gap-4">
                <span class="size-12 overflow-hidden rounded-full bg-brand-100"><Picture :image="item.avatar" :alt="item.name" size="thumb" /></span>
                <span>
                    <span class="block font-serif font-bold">{{ item.name }}</span>
                    <span v-if="item.role" class="block text-sm text-muted">{{ item.role }}</span>
                    <span class="mt-1 flex text-amber-400"><Icon v-for="n in item.rating" :key="n" name="star" class="size-4 fill-current" /></span>
                </span>
            </figcaption>
        </figure>
        <p v-if="!testimonials.length" class="text-muted">Chưa có đánh giá.</p>
    </section>
    <QuoteCta />
</template>
