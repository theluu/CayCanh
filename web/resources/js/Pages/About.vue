<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import PageBanner from '@/Components/PageBanner.vue';
import Picture from '@/Components/Picture.vue';
import QuoteCta from '@/Components/QuoteCta.vue';
import SectionTitle from '@/Components/SectionTitle.vue';
import Icon from '@/Components/Icon.vue';
import SeoHead from '@/Components/SeoHead.vue';
import SiteLayout from '@/Layouts/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

const props = defineProps({ seo: Object, page: Object, stats: Object, testimonials: Array, partners: Array });

const statItems = computed(() => [
    { value: props.stats?.years, label: 'Năm kinh nghiệm' },
    { value: props.stats?.projects, label: 'Công trình' },
    { value: props.stats?.trees, label: 'Cây chăm sóc' },
].filter((item) => item.value));
</script>

<template>
    <SeoHead :seo="seo" />
    <PageBanner :title="page?.title || 'Giới thiệu'" :breadcrumbs="[{ label: 'Giới thiệu' }]" />
    <section class="container-site grid gap-12 py-20 lg:grid-cols-5">
        <div class="lg:col-span-3">
            <div v-if="page?.body" class="rich" v-html="page.body" />
            <p v-else class="text-muted">Nội dung đang được cập nhật.</p>
        </div>
        <aside class="space-y-6 lg:col-span-2">
            <div class="aspect-[4/3] overflow-hidden bg-paper"><Picture :image="page?.image" alt="Giới thiệu" size="large" /></div>
            <div class="grid grid-cols-3 gap-3">
                <div v-for="item in statItems" :key="item.label" class="border-b-4 border-brand-500 bg-paper p-4 text-center">
                    <p class="font-serif text-2xl font-bold text-brand-600">{{ Number(item.value).toLocaleString('vi-VN') }}+</p>
                    <p class="mt-1 text-xs text-muted">{{ item.label }}</p>
                </div>
            </div>
            <Link href="/bao-gia" class="btn-primary w-full">Nhận tư vấn & báo giá</Link>
        </aside>
    </section>
    <section v-if="testimonials.length" class="bg-paper py-20">
        <div class="container-site">
            <SectionTitle title="Khách hàng" accent="nói gì" center />
            <div class="mt-10 grid gap-6 md:grid-cols-2">
                <figure v-for="item in testimonials" :key="item.id" class="bg-white p-7">
                    <Icon name="quote" class="size-7 text-brand-500" />
                    <blockquote class="mt-3 text-muted italic">“{{ item.content }}”</blockquote>
                    <figcaption class="mt-4 font-serif font-bold">{{ item.name }} <span v-if="item.role" class="font-sans text-sm font-normal text-muted">– {{ item.role }}</span></figcaption>
                </figure>
            </div>
        </div>
    </section>
    <QuoteCta />
</template>
