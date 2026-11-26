<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import Icon from '@/Components/Icon.vue';
import PageBanner from '@/Components/PageBanner.vue';
import Picture from '@/Components/Picture.vue';
import QuoteCta from '@/Components/QuoteCta.vue';
import SectionTitle from '@/Components/SectionTitle.vue';
import SeoHead from '@/Components/SeoHead.vue';
import SiteLayout from '@/Layouts/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

const props = defineProps({ seo: Object, page: Object, stats: Object, testimonials: Array, partners: Array });

const statItems = computed(() => [
    { value: props.stats?.years, suffix: '', label: 'Năm kinh nghiệm' },
    { value: props.stats?.projects, suffix: '+', label: 'Công trình' },
    { value: props.stats?.trees, suffix: '+', label: 'Cây đang chăm sóc' },
].filter((item) => item.value));

const milestones = [
    { year: '2014', title: 'Thành lập vườn ươm đầu tiên', text: 'Khởi đầu với vườn ươm nhỏ tại Củ Chi, chuyên cây bóng mát và cây cảnh.' },
    { year: '2017', title: 'Mở dịch vụ cho thuê cây văn phòng', text: 'Phục vụ những tòa nhà văn phòng đầu tiên tại trung tâm TP.HCM.' },
    { year: '2019', title: 'Đội ngũ thiết kế cảnh quan', text: 'Thành lập bộ phận thiết kế, nhận thi công sân vườn biệt thự và resort.' },
    { year: '2021', title: 'Vườn ươm 3 ha', text: 'Mở rộng vườn ươm, chủ động nguồn cây công trình số lượng lớn.' },
    { year: '2023', title: 'Bộ sưu tập cây nghệ thuật', text: 'Ra mắt bộ sưu tập tùng, sanh, mai, linh sam độc bản.' },
    { year: '2026', title: 'Hơn 350 công trình', text: 'Tiếp tục đồng hành cùng chủ đầu tư, doanh nghiệp và gia đình.' },
];
</script>

<template>
    <SeoHead :seo="seo" />
    <PageBanner :title="page?.title || 'Giới thiệu'" :breadcrumbs="[{ label: 'Giới thiệu' }]" />

    <section class="container-site grid gap-12 py-20 lg:grid-cols-[370px_1fr] lg:py-[90px]">
        <div class="aspect-[370/480] overflow-hidden bg-paper">
            <Picture :image="page?.image" alt="Giới thiệu" size="large" />
        </div>
        <div>
            <SectionTitle title="Chào mừng đến" :accent="$page.props.site.site_name" />
            <div v-if="page?.body" class="rich mt-8 text-muted" v-html="page.body" />
            <div class="mt-10 grid grid-cols-3 gap-6">
                <div v-for="item in statItems" :key="item.label">
                    <p class="font-serif text-[48px] leading-none font-bold text-brand-500">{{ Number(item.value).toLocaleString('vi-VN') }}{{ item.suffix }}</p>
                    <p class="mt-2 font-serif text-lg text-muted italic">{{ item.label }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="container-site border-t border-gray-100 py-20 lg:py-[90px]">
        <div class="grid gap-x-[30px] gap-y-12 md:grid-cols-2 lg:grid-cols-3">
            <div v-for="item in milestones" :key="item.year">
                <div class="flex items-center gap-6">
                    <span class="bg-paper p-2"><span class="block border border-dashed border-brand-500/60 px-7 py-2 font-serif text-[30px] font-bold text-brand-500">{{ item.year }}</span></span>
                    <h3 class="text-2xl leading-snug">{{ item.title }}</h3>
                </div>
                <p class="mt-6 text-sm leading-6 text-muted">{{ item.text }}</p>
            </div>
        </div>
    </section>

    <section v-if="testimonials.length" class="bg-paper py-20 lg:py-[90px]">
        <div class="container-site">
            <SectionTitle title="nói gì" accent="Khách hàng" accent-first small />
            <div class="mt-12 grid gap-[30px] md:grid-cols-2">
                <figure v-for="item in testimonials" :key="item.id" class="flex gap-8 bg-white p-8">
                    <img src="/images/theme/qut.jpg" alt="" class="h-[42px] w-[49px] shrink-0" loading="lazy" />
                    <div>
                        <blockquote class="text-base leading-[30px] text-muted">{{ item.content }}</blockquote>
                        <figcaption class="mt-5">
                            <span class="block font-serif text-2xl font-bold text-ink">{{ item.name }}</span>
                            <span v-if="item.role" class="block text-sm text-muted">{{ item.role }}</span>
                            <span class="mt-2 flex text-brand-500"><Icon v-for="n in item.rating" :key="n" name="star" class="size-5 fill-current" /></span>
                        </figcaption>
                    </div>
                </figure>
            </div>
            <div class="mt-12 text-center"><Link href="/bao-gia" class="btn-primary w-60">Nhận tư vấn &amp; báo giá</Link></div>
        </div>
    </section>
    <QuoteCta />
</template>
