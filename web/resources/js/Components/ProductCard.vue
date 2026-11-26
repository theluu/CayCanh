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
        <div class="flex flex-1 flex-col p-4">
            <h3 class="font-serif text-lg leading-snug font-bold">
                <Link :href="product.url" class="hover:text-brand-600">{{ product.name }}</Link>
            </h3>
            <div class="mt-2 flex items-baseline gap-2">
                <span class="font-semibold text-brand-600">{{ product.price }}</span>
                <span v-if="product.originalPrice" class="text-sm text-gray-400 line-through">{{ product.originalPrice }}</span>
            </div>
            <div class="mt-auto pt-4">
                <button v-if="product.canAddToCart" type="button" class="btn-ghost w-full" :disabled="adding" @click="addToCart">
                    <Icon name="cart" class="size-4" /> Thêm vào giỏ
                </button>
                <Link v-else :href="product.url" class="btn-ghost w-full">Xem & nhận báo giá</Link>
            </div>
        </div>
    </article>
</template>
