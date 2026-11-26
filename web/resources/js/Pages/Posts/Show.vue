<script setup>
import { Link } from '@inertiajs/vue3';
import Icon from '@/Components/Icon.vue';
import PageBanner from '@/Components/PageBanner.vue';
import Picture from '@/Components/Picture.vue';
import PostCard from '@/Components/PostCard.vue';
import PostSidebar from '@/Components/PostSidebar.vue';
import SectionTitle from '@/Components/SectionTitle.vue';
import SeoHead from '@/Components/SeoHead.vue';
import SiteLayout from '@/Layouts/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

defineProps({ seo: Object, post: Object, related: Array, categories: Array });
</script>

<template>
    <SeoHead :seo="seo" />
    <PageBanner :title="post.title" :breadcrumbs="[{ label: 'Tin tức', href: '/tin-tuc' }, { label: post.title }]" />
    <section class="container-site grid gap-12 py-20 lg:grid-cols-4">
        <article class="lg:col-span-3">
            <div class="mb-6 flex flex-wrap items-center gap-4 text-sm text-muted">
                <span v-if="post.date" class="inline-flex items-center gap-1"><Icon name="clock" class="size-4" />{{ post.date }}</span>
                <Link v-if="post.category" :href="post.category.url" class="text-brand-600">{{ post.category.name }}</Link>
            </div>
            <div v-if="post.image" class="mb-8 aspect-[16/9] overflow-hidden"><Picture :image="post.image" :alt="post.title" size="large" sizes="(min-width: 1024px) 75vw, 100vw" eager /></div>
            <p v-if="post.excerpt" class="mb-6 text-lg font-medium text-ink">{{ post.excerpt }}</p>
            <div class="rich" v-html="post.body" />
            <div v-if="post.tags.length" class="mt-8 flex flex-wrap gap-2">
                <span v-for="tag in post.tags" :key="tag" class="bg-paper px-3 py-1 text-xs">#{{ tag }}</span>
            </div>
        </article>
        <PostSidebar :categories="categories" />
    </section>
    <section v-if="related.length" class="bg-paper py-20">
        <div class="container-site">
            <SectionTitle title="Bài viết" accent="liên quan" />
            <div class="mt-10 grid gap-8 md:grid-cols-3"><PostCard v-for="item in related" :key="item.id" :post="item" /></div>
        </div>
    </section>
</template>
