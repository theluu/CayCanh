<script setup>
import { ref } from 'vue';
import Icon from './Icon.vue';

defineProps({
    faqs: { type: Array, required: true },
});

const open = ref(0);
</script>

<template>
    <div class="divide-y divide-gray-100 border border-gray-100">
        <div v-for="(faq, index) in faqs" :key="faq.id">
            <button type="button" class="flex w-full items-center justify-between gap-4 px-5 py-4 text-left font-medium hover:text-brand-600"
                :aria-expanded="open === index" @click="open = open === index ? null : index">
                <span>{{ faq.question }}</span>
                <Icon name="chevronDown" class="size-4 shrink-0 transition" :class="{ 'rotate-180 text-brand-500': open === index }" />
            </button>
            <div v-show="open === index" class="rich px-5 pb-5 text-sm" v-html="faq.answer" />
        </div>
    </div>
</template>
