<script setup>
import PageBanner from '@/Components/PageBanner.vue';
import QuoteCta from '@/Components/QuoteCta.vue';
import ServiceCard from '@/Components/ServiceCard.vue';
import SeoHead from '@/Components/SeoHead.vue';
import SiteLayout from '@/Layouts/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

defineProps({ seo: Object, services: Array });
</script>

<template>
    <SeoHead :seo="seo" />
    <PageBanner title="Dịch vụ" :breadcrumbs="[{ label: 'Dịch vụ' }]" />
    <section class="container-site py-20">
        <div class="grid gap-10 sm:grid-cols-2 lg:grid-cols-3">
            <ServiceCard v-for="service in services" :key="service.id" :service="service" />
        </div>
        <p v-if="!services.length" class="text-center text-muted">Chưa có dịch vụ nào.</p>
    </section>
    <QuoteCta />
</template>
