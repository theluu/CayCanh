<script setup>
import { Link } from '@inertiajs/vue3';
import Icon from './Icon.vue';
import Picture from './Picture.vue';

defineProps({
    service: { type: Object, required: true },
});
</script>

<template>
    <article class="group flex flex-col">
        <Link :href="service.url" class="block aspect-[4/3] overflow-hidden bg-paper">
            <Picture :image="service.image" :alt="service.name" img-class="size-full object-cover transition duration-500 group-hover:scale-105" />
        </Link>
        <h3 class="mt-4 font-serif text-lg font-bold"><Link :href="service.url" class="hover:text-brand-600">{{ service.name }}</Link></h3>
        <p v-if="service.summary" class="mt-2 line-clamp-3 text-sm leading-relaxed text-muted">{{ service.summary }}</p>
        <Link :href="service.url" class="mt-3 inline-flex items-center gap-1 text-xs font-semibold tracking-wide uppercase hover:text-brand-600">
            Xem chi tiết <Icon name="arrowRight" class="size-3.5" />
        </Link>
    </article>
</template>
