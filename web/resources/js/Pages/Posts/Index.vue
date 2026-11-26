<script setup>
import PageBanner from '@/Components/PageBanner.vue';
import Pagination from '@/Components/Pagination.vue';
import PostCard from '@/Components/PostCard.vue';
import PostSidebar from '@/Components/PostSidebar.vue';
import SeoHead from '@/Components/SeoHead.vue';
import SiteLayout from '@/Layouts/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

defineProps({ seo: Object, posts: Object, category: Object, categories: Array, filters: Object });
</script>

<template>
    <SeoHead :seo="seo" />
    <PageBanner :title="category ? category.name : 'Tin tức & kiến thức'"
        :breadcrumbs="category ? [{ label: 'Tin tức', href: '/tin-tuc' }, { label: category.name }] : [{ label: 'Tin tức' }]" />
    <section class="container-site grid gap-12 py-20 lg:grid-cols-4">
        <div class="lg:col-span-3">
            <p v-if="filters.q" class="mb-6 text-muted">Kết quả tìm kiếm cho “{{ filters.q }}”</p>
            <div class="grid gap-10 sm:grid-cols-2 xl:grid-cols-3">
                <PostCard v-for="post in posts.data" :key="post.id" :post="post" />
            </div>
            <p v-if="!posts.data.length" class="text-muted">Chưa có bài viết.</p>
            <Pagination :meta="posts.meta" />
        </div>
        <PostSidebar :categories="categories" :query="filters.q || ''" />
    </section>
</template>
