<script setup>
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import Icon from './Icon.vue';
import Picture from './Picture.vue';

const props = defineProps({
    product: { type: Object, required: true },
});

const adding = ref(false);

const addToCart = () => {
    router.post('/gio-hang', { product_id: props.product.id, qty: 1 }, {
        preserveScroll: true,
        onStart: () => (adding.value = true),
        onFinish: () => (adding.value = false),
    });
};
</script>

<template>
    <article class="group flex flex-col overflow-hidden border border-gray-100 bg-white transition hover:shadow-lg">
        <Link :href="product.url" class="relative block aspect-square overflow-hidden bg-paper">
            <Picture :image="product.image" :alt="product.name" sizes="(min-width: 1024px) 25vw, 50vw" img-class="size-full object-cover transition duration-500 group-hover:scale-105" />
            <span v-if="product.availability && product.availability.value !== 'available'"
                class="absolute top-3 left-3 rounded-sm px-2 py-1 text-xs font-semibold text-white"
                :class="product.availability.value === 'sold' ? 'bg-gray-700' : 'bg-amber-500'">
                {{ product.availability.label }}
            </span>
        </Link>
        <div class="flex flex-1 flex-col p-3 sm:p-4">
            <h3 class="font-serif text-base leading-snug font-bold sm:text-lg">
                <Link :href="product.url" class="hover:text-brand-600">{{ product.name }}</Link>
            </h3>
            <div class="mt-2 flex flex-wrap items-baseline gap-x-2">
                <span class="text-sm font-semibold whitespace-nowrap text-brand-600 sm:text-base">{{ product.price }}</span>
                <span v-if="product.originalPrice" class="text-xs whitespace-nowrap text-gray-400 line-through sm:text-sm">{{ product.originalPrice }}</span>
            </div>
            <div class="mt-auto pt-4">
                <button v-if="product.canAddToCart" type="button" class="btn-ghost w-full px-2 text-xs sm:px-5 sm:text-sm" :disabled="adding" @click="addToCart">
                    <Icon name="cart" class="hidden size-4 sm:block" /> Thêm vào giỏ
                </button>
                <Link v-else :href="product.url" class="btn-ghost w-full px-2 text-xs sm:px-5 sm:text-sm">Xem & báo giá</Link>
            </div>
        </div>
    </article>
</template>
