<script setup>
import { router } from '@inertiajs/vue3';
import CatalogFilters from '@/Components/CatalogFilters.vue';
import PageBanner from '@/Components/PageBanner.vue';
import Pagination from '@/Components/Pagination.vue';
import ProductCard from '@/Components/ProductCard.vue';
import SeoHead from '@/Components/SeoHead.vue';
import SiteLayout from '@/Layouts/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

const props = defineProps({ seo: Object, products: Object, category: Object, categories: Array, priceRanges: Array, filters: Object, basePath: String });

const root = '/san-pham';
const rootLabel = 'Sản phẩm';

const sort = (event) => {
    const params = { ...props.filters, 'sap-xep': event.target.value };
    Object.keys(params).forEach((key) => (params[key] === null || params[key] === 'moi') && delete params[key]);
    router.get(props.basePath, params, { preserveScroll: true, preserveState: true });
};
</script>

<template>
    <SeoHead :seo="seo" />
    <PageBanner :title="category ? category.name : rootLabel" :breadcrumbs="category ? [{ label: rootLabel, href: root }, { label: category.name }] : [{ label: rootLabel }]" />
    <section class="container-site grid gap-12 py-16 lg:grid-cols-4">
        <CatalogFilters class="order-2 lg:order-1" :categories="categories" :price-ranges="priceRanges" :filters="filters" :base-path="basePath" :root-path="root" :is-art="false" :active-category="category?.slug" />
        <div class="order-1 lg:order-2 lg:col-span-3">
            <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
                <p class="text-sm text-muted">Hiển thị {{ products.meta.from ?? 0 }}–{{ products.meta.to ?? 0 }} trên {{ products.meta.total }} {{ 'sản phẩm' }}</p>
                <label class="flex items-center gap-2 text-sm">Sắp xếp
                    <select class="field w-auto py-2" :value="filters['sap-xep']" @change="sort">
                        <option value="moi">Mới nhất</option>
                        <option value="gia-tang">Giá tăng dần</option>
                        <option value="gia-giam">Giá giảm dần</option>
                    </select>
                </label>
            </div>
            <div class="grid grid-cols-2 gap-4 md:gap-6 xl:grid-cols-3">
                <ProductCard v-for="product in products.data" :key="product.id" :product="product" />
            </div>
            <p v-if="!products.data.length" class="py-16 text-center text-muted">Không tìm thấy sản phẩm phù hợp.</p>
            <Pagination :meta="products.meta" />
        </div>
    </section>
</template>
