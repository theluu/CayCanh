<script setup>
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import Icon from './Icon.vue';

const props = defineProps({
    categories: { type: Array, default: () => [] },
    priceRanges: { type: Array, default: () => [] },
    filters: { type: Object, required: true },
    basePath: { type: String, required: true },
    rootPath: { type: String, required: true },
    isArt: { type: Boolean, default: false },
    activeCategory: { type: String, default: null },
});

const q = ref(props.filters.q ?? '');

const apply = (changes) => {
    const params = { ...props.filters, ...changes };
    Object.keys(params).forEach((key) => (params[key] === null || params[key] === '' || (key === 'sap-xep' && params[key] === 'moi')) && delete params[key]);
    router.get(props.basePath, params, { preserveScroll: true, preserveState: true });
};
</script>

<template>
    <aside class="space-y-8">
        <form class="flex" role="search" @submit.prevent="apply({ q })">
            <label for="catalog-search" class="sr-only">Tìm kiếm</label>
            <input id="catalog-search" v-model="q" type="search" class="field" :placeholder="isArt ? 'Tìm cây…' : 'Tìm sản phẩm…'" />
            <button type="submit" class="btn-primary rounded-l-none px-4">Tìm</button>
        </form>
        <div v-if="categories.length" class="bg-paper p-6">
            <h3 class="mb-4 text-xl font-bold">Danh mục</h3>
            <ul class="space-y-1.5 text-sm">
                <li>
                    <Link :href="rootPath" class="flex items-center gap-2 py-1 hover:text-brand-600" :class="{ 'font-semibold text-brand-600': !activeCategory }">
                        <Icon name="chevronRight" class="size-3 text-brand-500" />Tất cả
                    </Link>
                </li>
                <li v-for="category in categories" :key="category.slug">
                    <Link :href="category.url" class="flex items-center justify-between py-1 hover:text-brand-600" :class="{ 'font-semibold text-brand-600': activeCategory === category.slug }">
                        <span class="flex items-center gap-2"><Icon name="chevronRight" class="size-3 text-brand-500" />{{ category.name }}</span>
                        <span class="text-muted">({{ category.count }})</span>
                    </Link>
                </li>
            </ul>
        </div>
        <div v-if="priceRanges.length" class="bg-paper p-6">
            <h3 class="mb-4 text-xl font-bold">Khoảng giá</h3>
            <div class="space-y-2 text-sm">
                <label v-for="range in priceRanges" :key="range.value" class="flex cursor-pointer items-center gap-2">
                    <input type="radio" name="gia" :value="range.value" :checked="filters.gia === range.value" class="accent-brand-500" @change="apply({ gia: range.value })" />
                    {{ range.label }}
                </label>
                <button v-if="filters.gia" type="button" class="text-xs text-brand-600 underline" @click="apply({ gia: null })">Bỏ lọc giá</button>
            </div>
        </div>
        <div v-if="isArt" class="bg-paper p-6">
            <h3 class="mb-4 text-xl font-bold">Tình trạng</h3>
            <div class="space-y-2 text-sm">
                <label v-for="option in [{ value: null, label: 'Tất cả' }, { value: 'con-hang', label: 'Còn hàng' }, { value: 'da-ban', label: 'Đã bán' }]" :key="option.label" class="flex cursor-pointer items-center gap-2">
                    <input type="radio" name="tinh-trang" :checked="filters['tinh-trang'] === option.value" class="accent-brand-500" @change="apply({ 'tinh-trang': option.value })" />
                    {{ option.label }}
                </label>
            </div>
        </div>
    </aside>
</template>
