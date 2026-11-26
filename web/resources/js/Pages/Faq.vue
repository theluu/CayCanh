<script setup>
import { computed } from 'vue';
import ContactInfo from '@/Components/ContactInfo.vue';
import FaqList from '@/Components/FaqList.vue';
import PageBanner from '@/Components/PageBanner.vue';
import QuoteForm from '@/Components/QuoteForm.vue';
import SeoHead from '@/Components/SeoHead.vue';
import SiteLayout from '@/Layouts/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

const props = defineProps({ seo: Object, faqs: Array });

const groups = computed(() => props.faqs.reduce((acc, faq) => {
    (acc[faq.group] ??= []).push(faq);
    return acc;
}, {}));
</script>

<template>
    <SeoHead :seo="seo" />
    <PageBanner title="Câu hỏi thường gặp" :breadcrumbs="[{ label: 'Câu hỏi thường gặp' }]" />
    <section class="container-site grid gap-12 py-20 lg:grid-cols-3">
        <div class="space-y-10 lg:col-span-2">
            <div v-for="(items, group) in groups" :key="group">
                <h2 class="mb-4 text-2xl font-bold">{{ group }}</h2>
                <FaqList :faqs="items" />
            </div>
            <p v-if="!faqs.length" class="text-muted">Chưa có câu hỏi nào.</p>
        </div>
        <aside class="space-y-8">
            <div class="border-t-4 border-brand-500 bg-paper p-6"><QuoteForm title="Đặt câu hỏi cho chúng tôi" /></div>
            <div class="bg-paper p-6"><ContactInfo /></div>
        </aside>
    </section>
</template>
