<script setup>
import { Link } from '@inertiajs/vue3';
import PageBanner from '@/Components/PageBanner.vue';
import Pagination from '@/Components/Pagination.vue';
import ProjectCard from '@/Components/ProjectCard.vue';
import QuoteCta from '@/Components/QuoteCta.vue';
import SeoHead from '@/Components/SeoHead.vue';
import SiteLayout from '@/Layouts/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

defineProps({ seo: Object, projects: Object, types: Array, filters: Object });
</script>

<template>
    <SeoHead :seo="seo" />
    <PageBanner title="Dự án đã thực hiện" :breadcrumbs="[{ label: 'Dự án' }]" />
    <section class="container-site py-20">
        <div v-if="types.length" class="mb-10 flex flex-wrap justify-center gap-2">
            <Link href="/du-an" preserve-scroll class="rounded-sm px-4 py-2 text-sm font-medium" :class="!filters.loai ? 'bg-brand-500 text-white' : 'bg-paper hover:text-brand-600'">Tất cả</Link>
            <Link v-for="type in types" :key="type.slug" :href="'/du-an?loai=' + type.slug" preserve-scroll class="rounded-sm px-4 py-2 text-sm font-medium"
                :class="filters.loai === type.slug ? 'bg-brand-500 text-white' : 'bg-paper hover:text-brand-600'">{{ type.name }}</Link>
        </div>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <ProjectCard v-for="project in projects.data" :key="project.id" :project="project" />
        </div>
        <p v-if="!projects.data.length" class="text-center text-muted">Chưa có dự án nào.</p>
        <Pagination :meta="projects.meta" />
    </section>
    <QuoteCta />
</template>
