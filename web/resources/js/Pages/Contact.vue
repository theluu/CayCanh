<script setup>
import ContactInfo from '@/Components/ContactInfo.vue';
import PageBanner from '@/Components/PageBanner.vue';
import QuoteForm from '@/Components/QuoteForm.vue';
import SeoHead from '@/Components/SeoHead.vue';
import SiteLayout from '@/Layouts/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

defineProps({ seo: Object, mapEmbedUrl: String });
</script>

<template>
    <SeoHead :seo="seo" />
    <PageBanner title="Liên hệ" :breadcrumbs="[{ label: 'Liên hệ' }]" />
    <section class="container-site grid gap-12 py-20 lg:grid-cols-5">
        <div class="lg:col-span-2">
            <h2 class="text-3xl font-bold">Thông tin <span class="text-brand-500">liên hệ</span></h2>
            <p class="mt-3 mb-8 text-muted">Gọi hotline, nhắn Zalo hoặc để lại thông tin – chúng tôi sẽ phản hồi trong giờ làm việc.</p>
            <ContactInfo />
        </div>
        <div class="border-t-4 border-brand-500 bg-paper p-8 lg:col-span-3">
            <QuoteForm title="Gửi tin nhắn cho chúng tôi" />
        </div>
    </section>
    <section v-if="mapEmbedUrl" class="h-96">
        <iframe :src="mapEmbedUrl" class="size-full border-0" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Bản đồ" allowfullscreen />
    </section>
</template>
