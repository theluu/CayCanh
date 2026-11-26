<script setup>
defineProps({
    title: { type: String, required: true },
    accent: { type: String, default: '' },
    after: { type: String, default: '' },
    accentFirst: { type: Boolean, default: false },
    subtitle: { type: String, default: '' },
    center: { type: Boolean, default: false },
    light: { type: Boolean, default: false },
    small: { type: Boolean, default: false },
});
</script>

<template>
    <div :class="center ? 'mx-auto max-w-3xl text-center' : ''">
        <h2 class="leading-tight font-bold" :class="[small ? 'text-[30px] md:text-[36px]' : 'text-[34px] md:text-[48px]', light ? 'text-white' : '']">
            <template v-if="accentFirst"><span class="title-leaf title-leaf-left" :class="small ? 'title-leaf-sm' : ''">{{ accent }}</span> {{ title }}</template>
            <template v-else>{{ title }} <span v-if="accent" class="title-leaf" :class="small ? 'title-leaf-sm' : ''">{{ accent }}</span> {{ after }}</template>
        </h2>
        <p v-if="subtitle" class="mt-4 font-serif text-lg leading-relaxed" :class="light ? 'text-gray-300' : 'text-muted'">{{ subtitle }}</p>
    </div>
</template>
