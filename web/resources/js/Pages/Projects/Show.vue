<script setup>
import { Link } from '@inertiajs/vue3';
import Gallery from '@/Components/Gallery.vue';
import Icon from '@/Components/Icon.vue';
import PageBanner from '@/Components/PageBanner.vue';
import ProjectCard from '@/Components/ProjectCard.vue';
import QuoteCta from '@/Components/QuoteCta.vue';
import SectionTitle from '@/Components/SectionTitle.vue';
import SeoHead from '@/Components/SeoHead.vue';
import SiteLayout from '@/Layouts/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

defineProps({ seo: Object, project: Object, related: Array });
</script>

<template>
    <SeoHead :seo="seo" />
    <PageBanner :title="project.name" :breadcrumbs="[{ label: 'Dự án', href: '/du-an' }, { label: project.name }]" />
    <section class="container-site grid gap-12 py-20 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <Gallery :images="project.gallery.length ? project.gallery : (project.image ? [project.image] : [])" :video-url="project.videoUrl" :alt="project.name" />
            <p v-if="project.summary" class="mt-8 text-lg leading-relaxed text-muted">{{ project.summary }}</p>
            <div v-if="project.body" class="rich mt-6" v-html="project.body" />
        </div>
        <aside>
            <div class="border-t-4 border-brand-500 bg-paper p-6">
                <h2 class="mb-5 text-xl font-bold">Thông tin dự án</h2>
                <dl class="divide-y divide-gray-200 text-sm">
                    <div v-if="project.type" class="flex justify-between gap-4 py-2.5"><dt class="text-muted">Loại công trình</dt><dd class="text-right font-medium">{{ project.type.name }}</dd></div>
                    <div v-if="project.client" class="flex justify-between gap-4 py-2.5"><dt class="text-muted">Chủ đầu tư</dt><dd class="text-right font-medium">{{ project.client }}</dd></div>
                    <div v-if="project.location" class="flex justify-between gap-4 py-2.5"><dt class="text-muted">Địa điểm</dt><dd class="text-right font-medium">{{ project.location }}</dd></div>
                    <div v-if="project.year" class="flex justify-between gap-4 py-2.5"><dt class="text-muted">Năm</dt><dd class="text-right font-medium">{{ project.year }}</dd></div>
                    <div v-if="project.scale" class="flex justify-between gap-4 py-2.5"><dt class="text-muted">Quy mô</dt><dd class="text-right font-medium">{{ project.scale }}</dd></div>
                </dl>
                <div v-if="project.services.length" class="mt-5">
                    <p class="mb-2 text-sm text-muted">Dịch vụ đã thực hiện</p>
                    <ul class="space-y-1.5">
                        <li v-for="service in project.services" :key="service.url"><Link :href="service.url" class="flex items-center gap-2 text-sm font-medium hover:text-brand-600"><Icon name="check" class="size-4 text-brand-500" />{{ service.name }}</Link></li>
                    </ul>
                </div>
                <Link href="/bao-gia" class="btn-primary mt-6 w-full">Yêu cầu công trình tương tự</Link>
            </div>
        </aside>
    </section>
    <section v-if="related.length" class="bg-paper py-20">
        <div class="container-site">
            <SectionTitle title="Dự án" accent="liên quan" />
            <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-3"><ProjectCard v-for="item in related" :key="item.id" :project="item" /></div>
        </div>
    </section>
    <QuoteCta />
</template>
