<script setup>
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import FaqList from '@/Components/FaqList.vue';
import Gallery from '@/Components/Gallery.vue';
import Icon from '@/Components/Icon.vue';
import PackageCard from '@/Components/PackageCard.vue';
import PageBanner from '@/Components/PageBanner.vue';
import ProjectCard from '@/Components/ProjectCard.vue';
import QuoteForm from '@/Components/QuoteForm.vue';
import SectionTitle from '@/Components/SectionTitle.vue';
import ContactInfo from '@/Components/ContactInfo.vue';
import SeoHead from '@/Components/SeoHead.vue';
import SiteLayout from '@/Layouts/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

const props = defineProps({ seo: Object, service: Object, otherServices: Array });

const selected = ref(null);
const choose = (pkg) => {
    selected.value = pkg;
    document.getElementById('bao-gia')?.scrollIntoView({ behavior: 'smooth' });
};
</script>

<template>
    <SeoHead :seo="seo" />
    <PageBanner :title="service.name" :breadcrumbs="[{ label: 'Dịch vụ', href: '/dich-vu' }, { label: service.name }]" />
    <section class="container-site grid gap-12 py-20 lg:grid-cols-3">
        <div class="space-y-10 lg:col-span-2">
            <Gallery v-if="service.gallery.length || service.image" :images="service.gallery.length ? service.gallery : [service.image]" :alt="service.name" />
            <p v-if="service.summary" class="text-lg leading-relaxed text-muted">{{ service.summary }}</p>
            <div v-if="service.body" class="rich" v-html="service.body" />
            <div v-if="service.packages.length">
                <SectionTitle title="Các gói" accent="dịch vụ" />
                <div class="mt-8 grid gap-6 md:grid-cols-2">
                    <PackageCard v-for="pkg in service.packages" :key="pkg.id" :pkg="pkg" @quote="choose" />
                </div>
            </div>
            <div v-if="service.faqs.length">
                <SectionTitle title="Câu hỏi" accent="thường gặp" />
                <FaqList :faqs="service.faqs" class="mt-8" />
            </div>
        </div>
        <aside class="space-y-8">
            <div id="bao-gia" class="border-t-4 border-brand-500 bg-paper p-6">
                <QuoteForm :key="selected?.id ?? 'service'" type="quote_service" :subject-type="selected ? 'package' : 'service'" :subject-id="selected?.id ?? service.id"
                    :subject-label="selected ? service.name + ' – ' + selected.name : service.name" title="Nhận báo giá" />
            </div>
            <div v-if="otherServices.length" class="bg-paper p-6">
                <h3 class="mb-4 text-xl font-bold">Dịch vụ khác</h3>
                <ul class="space-y-2">
                    <li v-for="other in otherServices" :key="other.url">
                        <Link :href="other.url" class="flex items-center gap-2 py-1 text-sm hover:text-brand-600"><Icon name="chevronRight" class="size-3 text-brand-500" />{{ other.name }}</Link>
                    </li>
                </ul>
            </div>
            <div class="bg-paper p-6"><ContactInfo /></div>
        </aside>
    </section>
    <section v-if="service.projects.length" class="bg-paper py-20">
        <div class="container-site">
            <SectionTitle title="Dự án" accent="đã thực hiện" />
            <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <ProjectCard v-for="project in service.projects" :key="project.id" :project="project" />
            </div>
        </div>
    </section>
</template>
