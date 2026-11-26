<script setup>
import { Link } from '@inertiajs/vue3';
import Icon from './Icon.vue';
import Picture from './Picture.vue';

defineProps({
    project: { type: Object, required: true },
});
</script>

<template>
    <Link :href="project.url" class="group relative block aspect-[4/3] overflow-hidden bg-paper">
        <Picture :image="project.image" :alt="project.name" img-class="size-full object-cover transition duration-500 group-hover:scale-110" />
        <div class="absolute inset-0 flex flex-col justify-end bg-gradient-to-t from-black/75 via-black/10 to-transparent p-5 text-white">
            <span v-if="project.type" class="mb-1 text-xs font-semibold tracking-wider text-brand-300 uppercase">{{ project.type.name }}</span>
            <h3 class="font-serif text-xl font-bold text-white">{{ project.name }}</h3>
            <p v-if="project.location" class="mt-1 flex items-center gap-1 text-sm text-gray-200"><Icon name="pin" class="size-4" />{{ project.location }}</p>
        </div>
    </Link>
</template>
