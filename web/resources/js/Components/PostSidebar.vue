<script setup>
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import Icon from './Icon.vue';

const props = defineProps({
    categories: { type: Array, default: () => [] },
    query: { type: String, default: '' },
});

const q = ref(props.query ?? '');
const search = () => router.get('/tin-tuc', q.value ? { q: q.value } : {}, { preserveState: true });
</script>

<template>
    <aside class="space-y-8">
        <form class="flex" role="search" @submit.prevent="search">
            <label for="post-search" class="sr-only">Tìm bài viết</label>
            <input id="post-search" v-model="q" type="search" class="field" placeholder="Tìm bài viết…" />
            <button type="submit" class="btn-primary rounded-l-none px-4">Tìm</button>
        </form>
        <div v-if="categories.length" class="bg-paper p-6">
            <h3 class="mb-4 text-xl font-bold">Chuyên mục</h3>
            <ul class="space-y-2 text-sm">
                <li v-for="category in categories" :key="category.url">
                    <Link :href="category.url" class="flex items-center justify-between py-1 hover:text-brand-600">
                        <span class="flex items-center gap-2"><Icon name="chevronRight" class="size-3 text-brand-500" />{{ category.name }}</span>
                        <span class="text-muted">({{ category.count }})</span>
                    </Link>
                </li>
            </ul>
        </div>
        <div class="bg-ink p-6 text-white">
            <h3 class="text-xl font-bold text-white">Cần tư vấn cây xanh?</h3>
            <p class="mt-2 text-sm text-gray-300">Đội ngũ kỹ thuật sẵn sàng khảo sát và báo giá miễn phí.</p>
            <Link href="/bao-gia" class="btn-primary mt-5">Nhận báo giá</Link>
        </div>
    </aside>
</template>
