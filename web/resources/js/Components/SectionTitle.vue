<script setup>
import Icon from './Icon.vue';

defineProps({
    title: { type: String, required: true },
    accent: { type: String, default: '' },
    subtitle: { type: String, default: '' },
    center: { type: Boolean, default: false },
    light: { type: Boolean, default: false },
});
</script>

<template>
    <div :class="center ? 'mx-auto max-w-2xl text-center' : ''">
        <Icon name="leaf" class="mb-2 size-7 text-brand-500" :class="center ? 'mx-auto' : ''" />
        <h2 class="text-3xl font-bold md:text-4xl" :class="light ? 'text-white' : ''">
            {{ title }} <span v-if="accent" class="text-brand-500">{{ accent }}</span>
        </h2>
        <p v-if="subtitle" class="mt-3 leading-relaxed" :class="light ? 'text-gray-200' : 'text-muted'">{{ subtitle }}</p>
    </div>
</template>
