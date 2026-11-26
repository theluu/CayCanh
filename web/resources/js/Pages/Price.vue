<script setup>
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import PackageCard from '@/Components/PackageCard.vue';
import PageBanner from '@/Components/PageBanner.vue';
import QuoteForm from '@/Components/QuoteForm.vue';
import SectionTitle from '@/Components/SectionTitle.vue';
import SeoHead from '@/Components/SeoHead.vue';
import SiteLayout from '@/Layouts/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

defineProps({ seo: Object, services: Array });

const selected = ref(null);
const choose = (service, pkg) => {
    selected.value = { service: service.name, pkg };
    document.getElementById('bao-gia')?.scrollIntoView({ behavior: 'smooth' });
};
</script>

<template>
    <SeoHead :seo="seo" />
    <PageBanner title="Bảng giá dịch vụ" :breadcrumbs="[{ label: 'Bảng giá' }]" />
    <section class="container-site space-y-20 py-20">
        <p class="mx-auto max-w-3xl text-center text-muted">Giá tham khảo, có thể thay đổi theo số lượng cây, vị trí công trình và yêu cầu chăm sóc. Liên hệ để được khảo sát và báo giá chính xác.</p>
        <div v-for="service in services" :key="service.id">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <SectionTitle :title="service.name" />
                <Link :href="service.url" class="text-sm font-semibold text-brand-600 uppercase">Chi tiết dịch vụ</Link>
            </div>
            <div class="mt-8 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <PackageCard v-for="pkg in service.packages" :key="pkg.id" :pkg="pkg" @quote="(p) => choose(service, p)" />
            </div>
        </div>
        <p v-if="!services.length" class="text-center text-muted">Bảng giá đang được cập nhật.</p>
        <div id="bao-gia" class="mx-auto max-w-3xl border-t-4 border-brand-500 bg-paper p-8">
            <QuoteForm :key="selected?.pkg.id ?? 'none'" type="quote_service" :subject-type="selected ? 'package' : null" :subject-id="selected?.pkg.id ?? null"
                :subject-label="selected ? selected.service + ' – ' + selected.pkg.name : null" title="Nhận báo giá chi tiết" />
        </div>
    </section>
</template>
