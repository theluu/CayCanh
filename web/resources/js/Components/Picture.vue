<script setup>
import Icon from './Icon.vue';

defineProps({
    image: { type: Object, default: null },
    alt: { type: String, default: '' },
    size: { type: String, default: 'medium' },
    sizes: { type: String, default: '(min-width: 1024px) 33vw, 100vw' },
    eager: { type: Boolean, default: false },
    imgClass: { type: String, default: 'size-full object-cover' },
});

const srcset = (image) => (image.thumb !== image.large ? image.thumb + ' 400w, ' + image.medium + ' 800w, ' + image.large + ' 1600w' : null);
</script>

<template>
    <img v-if="image" :src="image[size] || image.original" :srcset="srcset(image)" :sizes="sizes" :alt="alt || image.alt"
        :loading="eager ? 'eager' : 'lazy'" :fetchpriority="eager ? 'high' : null" decoding="async" :class="imgClass" />
    <div v-else class="grid size-full place-items-center bg-gradient-to-br from-brand-100 to-brand-300 text-brand-700" role="img" :aria-label="alt">
        <Icon name="leaf" class="size-12 opacity-60" />
    </div>
</template>
