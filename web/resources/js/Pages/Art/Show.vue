<script setup>
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import ContactInfo from '@/Components/ContactInfo.vue';
import Gallery from '@/Components/Gallery.vue';
import Icon from '@/Components/Icon.vue';
import PageBanner from '@/Components/PageBanner.vue';
import ProductCard from '@/Components/ProductCard.vue';
import QuoteForm from '@/Components/QuoteForm.vue';
import SectionTitle from '@/Components/SectionTitle.vue';
import SeoHead from '@/Components/SeoHead.vue';
import SiteLayout from '@/Layouts/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

const props = defineProps({ seo: Object, product: Object, related: Array });

const root = '/cay-canh-nghe-thuat';
const rootLabel = 'Cây cảnh nghệ thuật';
const qty = ref(1);
const adding = ref(false);

const addToCart = (goToCart = false) => {
    router.post('/gio-hang', { product_id: props.product.id, qty: qty.value, redirect: goToCart ? 'cart' : null }, {
        preserveScroll: true,
        onStart: () => (adding.value = true),
        onFinish: () => (adding.value = false),
    });
};

const crumbs = [{ label: rootLabel, href: root }, ...(props.product.category ? [{ label: props.product.category.name, href: props.product.category.url }] : []), { label: props.product.name }];
</script>

<template>
    <SeoHead :seo="seo" />
    <PageBanner :title="product.name" :breadcrumbs="crumbs" heading-tag="p" />
    <section class="container-site grid gap-12 py-16 lg:grid-cols-2">
        <Gallery :images="product.gallery" :video-url="product.videoUrl" :alt="product.name" />
        <div>
            <p v-if="product.category" class="text-sm"><Link :href="product.category.url" class="text-brand-600 hover:underline">{{ product.category.name }}</Link></p>
            <h1 class="mt-1 text-3xl font-bold md:text-4xl">{{ product.name }}</h1>
            <div class="mt-4 flex flex-wrap items-center gap-3">
                <span class="font-serif text-3xl font-bold text-brand-600">{{ product.price }}</span>
                <span v-if="product.originalPrice" class="text-lg text-gray-400 line-through">{{ product.originalPrice }}</span>
                <span v-if="product.availability" class="rounded-sm px-2.5 py-1 text-xs font-semibold text-white"
                    :class="{ 'bg-brand-500': product.availability.value === 'available', 'bg-amber-500': product.availability.value === 'reserved', 'bg-gray-700': product.availability.value === 'sold' }">
                    {{ product.availability.label }}
                </span>
                <span v-else-if="!product.inStock" class="rounded-sm bg-gray-700 px-2.5 py-1 text-xs font-semibold text-white">Tạm hết hàng</span>
            </div>
            <p v-if="product.summary" class="mt-5 leading-relaxed text-muted">{{ product.summary }}</p>

            <dl v-if="product.specs.length" class="mt-6 grid grid-cols-2 gap-px overflow-hidden border border-gray-100 bg-gray-100 text-sm">
                <div v-for="spec in product.specs" :key="spec.label" class="bg-white px-4 py-3">
                    <dt class="text-muted">{{ spec.label }}</dt>
                    <dd class="font-semibold">{{ spec.value }}</dd>
                </div>
            </dl>

            <div v-if="product.canAddToCart" class="mt-8 flex flex-wrap items-center gap-3">
                <label class="sr-only" for="qty">Số lượng</label>
                <div class="flex items-center border border-gray-200">
                    <button type="button" class="px-3 py-2.5 hover:text-brand-600" aria-label="Giảm" @click="qty = Math.max(1, qty - 1)">−</button>
                    <input id="qty" v-model.number="qty" type="number" min="1" max="99" class="w-14 border-x border-gray-200 py-2.5 text-center text-sm focus:outline-none" />
                    <button type="button" class="px-3 py-2.5 hover:text-brand-600" aria-label="Tăng" @click="qty = Math.min(99, qty + 1)">+</button>
                </div>
                <button type="button" class="btn-ghost" :disabled="adding" @click="addToCart(false)"><Icon name="cart" class="size-4" />Thêm vào giỏ</button>
                <button type="button" class="btn-primary" :disabled="adding" @click="addToCart(true)">Mua ngay</button>
            </div>

            <div v-if="product.canQuote" id="bao-gia" class="mt-8 border-t-4 border-brand-500 bg-paper p-6">
                <QuoteForm type="quote_product" subject-type="product" :subject-id="product.id" :subject-label="product.name"
                    title="Liên hệ xem cây & nhận báo giá" submit-label="Nhận báo giá" />
            </div>
            <div v-else-if="product.availability?.value === 'sold'" class="mt-8 bg-paper p-6">
                <p class="font-medium">Cây này đã có chủ. Xem thêm các tác phẩm khác trong bộ sưu tập hoặc liên hệ để chúng tôi tìm cây tương tự.</p>
                <Link :href="root + '?tinh-trang=con-hang'" class="btn-primary mt-4">Xem cây tương tự</Link>
            </div>
            <div class="mt-8"><ContactInfo /></div>
        </div>
    </section>
    <section v-if="product.description" class="container-site pb-16">
        <h2 class="mb-6 border-b border-gray-100 pb-3 text-2xl font-bold">Mô tả chi tiết</h2>
        <div class="rich" v-html="product.description" />
    </section>
    <section v-if="related.length" class="bg-paper py-16">
        <div class="container-site">
            <SectionTitle title="Cây" accent="tương tự" />
            <div class="mt-10 grid grid-cols-2 gap-4 md:gap-6 lg:grid-cols-4"><ProductCard v-for="item in related" :key="item.id" :product="item" /></div>
        </div>
    </section>
</template>
