<script setup>
import ContactInfo from '@/Components/ContactInfo.vue';
import Icon from '@/Components/Icon.vue';
import PageBanner from '@/Components/PageBanner.vue';
import QuoteForm from '@/Components/QuoteForm.vue';
import SeoHead from '@/Components/SeoHead.vue';
import SiteLayout from '@/Layouts/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

defineProps({ seo: Object });

const steps = [
    { title: 'Gửi yêu cầu', text: 'Cho chúng tôi biết loại công trình, địa điểm và nhu cầu.' },
    { title: 'Khảo sát miễn phí', text: 'Kỹ thuật viên tới tận nơi đo đạc, đánh giá ánh sáng, không gian.' },
    { title: 'Báo giá chi tiết', text: 'Phương án cây, chậu, chi phí và lịch chăm sóc rõ ràng.' },
    { title: 'Thi công & chăm sóc', text: 'Giao, trồng và chăm sóc định kỳ theo hợp đồng.' },
];
</script>

<template>
    <SeoHead :seo="seo" />
    <PageBanner title="Yêu cầu khảo sát & báo giá" :breadcrumbs="[{ label: 'Yêu cầu báo giá' }]" />
    <section class="container-site grid gap-12 py-16 lg:grid-cols-5">
        <div class="lg:col-span-2">
            <h2 class="text-3xl font-bold">Quy trình <span class="text-brand-500">làm việc</span></h2>
            <ol class="mt-8 space-y-6">
                <li v-for="(step, index) in steps" :key="step.title" class="flex gap-4">
                    <span class="grid size-11 shrink-0 place-items-center rounded-full bg-brand-500 font-serif text-lg font-bold text-white">{{ index + 1 }}</span>
                    <div><h3 class="text-lg font-bold">{{ step.title }}</h3><p class="mt-1 text-sm text-muted">{{ step.text }}</p></div>
                </li>
            </ol>
            <div class="mt-10 bg-paper p-6"><ContactInfo /></div>
        </div>
        <div class="border-t-4 border-brand-500 bg-paper p-8 lg:col-span-3">
            <p class="mb-6 flex items-center gap-2 text-sm text-muted"><Icon name="shield" class="size-5 text-brand-500" />Thông tin của bạn chỉ dùng để liên hệ tư vấn.</p>
            <QuoteForm type="survey" title="Thông tin công trình" submit-label="Gửi yêu cầu khảo sát" />
        </div>
    </section>
</template>
