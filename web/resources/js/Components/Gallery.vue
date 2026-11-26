<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import Icon from './Icon.vue';
import Picture from './Picture.vue';

const props = defineProps({
    images: { type: Array, default: () => [] },
    videoUrl: { type: String, default: null },
    alt: { type: String, default: '' },
});

const active = ref(0);
let lightbox = null;

const elements = computed(() => [
    ...props.images.map((image) => ({ href: image.large || image.original, type: 'image', description: image.alt || props.alt })),
    ...(props.videoUrl ? [{ href: props.videoUrl, type: 'video' }] : []),
]);

onMounted(async () => {
    if (!elements.value.length) return;
    await import('glightbox/dist/css/glightbox.min.css');
    const { default: GLightbox } = await import('glightbox');
    lightbox = GLightbox({ elements: elements.value, touchNavigation: true, loop: true });
});

onBeforeUnmount(() => lightbox?.destroy());

const open = (index) => lightbox?.openAt(index);
</script>

<template>
    <div>
        <button type="button" class="relative block aspect-square w-full overflow-hidden bg-paper md:aspect-[4/3]" :aria-label="'Phóng to ảnh ' + alt" @click="open(active)">
            <Picture :image="images[active] ?? null" :alt="alt" size="large" sizes="(min-width: 1024px) 50vw, 100vw" eager />
            <span v-if="images.length" class="absolute right-3 bottom-3 rounded-sm bg-black/60 px-2.5 py-1 text-xs text-white">{{ active + 1 }}/{{ images.length }} · Bấm để phóng to</span>
        </button>
        <div v-if="images.length > 1 || videoUrl" class="mt-3 grid grid-cols-5 gap-2">
            <button v-for="(image, index) in images" :key="index" type="button" class="aspect-square overflow-hidden border-2"
                :class="index === active ? 'border-brand-500' : 'border-transparent'" :aria-label="'Ảnh ' + (index + 1)" @click="active = index">
                <Picture :image="image" :alt="alt" size="thumb" sizes="120px" />
            </button>
            <button v-if="videoUrl" type="button" class="grid aspect-square place-items-center bg-ink text-white" aria-label="Xem video" @click="open(images.length)">
                <Icon name="play" class="size-8" />
            </button>
        </div>
    </div>
</template>
