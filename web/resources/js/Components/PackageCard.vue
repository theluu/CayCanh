<script setup>
import Icon from './Icon.vue';

defineProps({
    pkg: { type: Object, required: true },
});

defineEmits(['quote']);
</script>

<template>
    <div class="relative flex flex-col border bg-white p-7 transition hover:shadow-lg" :class="pkg.highlighted ? 'border-brand-500 shadow-md' : 'border-gray-100'">
        <span v-if="pkg.highlighted" class="absolute -top-3 left-7 bg-brand-500 px-3 py-1 text-xs font-semibold text-white uppercase">Phổ biến</span>
        <h3 class="text-xl font-bold">{{ pkg.name }}</h3>
        <p class="mt-3 font-serif text-3xl font-bold text-brand-600">{{ pkg.price }}<span v-if="pkg.unit" class="text-base font-normal text-muted">{{ pkg.unit }}</span></p>
        <ul class="mt-6 flex-1 space-y-2.5 text-sm">
            <li v-for="feature in pkg.features" :key="feature" class="flex gap-2"><Icon name="check" class="size-5 shrink-0 text-brand-500" />{{ feature }}</li>
        </ul>
        <button type="button" class="mt-7" :class="pkg.highlighted ? 'btn-primary' : 'btn-ghost'" @click="$emit('quote', pkg)">Nhận báo giá gói này</button>
    </div>
</template>
