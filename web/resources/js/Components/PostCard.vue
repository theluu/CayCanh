<script setup>
import { Link } from '@inertiajs/vue3';
import Icon from './Icon.vue';
import Picture from './Picture.vue';

defineProps({
    post: { type: Object, required: true },
});
</script>

<template>
    <article class="group flex flex-col">
        <Link :href="post.url" class="block aspect-[16/10] overflow-hidden bg-paper">
            <Picture :image="post.image" :alt="post.title" img-class="size-full object-cover transition duration-500 group-hover:scale-105" />
        </Link>
        <div class="flex flex-1 flex-col pt-4">
            <div class="mb-2 flex items-center gap-3 text-xs text-muted">
                <span v-if="post.date" class="inline-flex items-center gap-1"><Icon name="clock" class="size-3.5" />{{ post.date }}</span>
                <span v-if="post.category" class="text-brand-600">{{ post.category.name }}</span>
            </div>
            <h3 class="font-serif text-lg leading-snug font-bold"><Link :href="post.url" class="hover:text-brand-600">{{ post.title }}</Link></h3>
            <p v-if="post.excerpt" class="mt-2 line-clamp-3 text-sm leading-relaxed text-muted">{{ post.excerpt }}</p>
            <Link :href="post.url" class="mt-3 inline-flex items-center gap-1 text-xs font-semibold tracking-wide text-ink uppercase hover:text-brand-600">
                Đọc tiếp <Icon name="arrowRight" class="size-3.5" />
            </Link>
        </div>
    </article>
</template>
