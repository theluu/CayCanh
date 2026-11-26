<script setup>
import { computed } from 'vue';
import { useSite } from '@/Composables/useSite';
import Icon from './Icon.vue';

const { site, telHref, zaloHref } = useSite();

const buttons = computed(() => [
    { key: 'messenger', href: site.value.messenger_url, label: 'Nhắn Messenger', class: 'bg-[#0084ff]', icon: 'messenger' },
    { key: 'zalo', href: zaloHref.value, label: 'Chat Zalo', class: 'bg-[#0068ff]', icon: 'zalo' },
    { key: 'phone', href: telHref.value, label: 'Gọi ngay', class: 'bg-brand-500', icon: 'phone' },
].filter((button) => button.href));
</script>

<template>
    <div v-if="buttons.length" class="fixed right-4 bottom-4 z-50 flex flex-col gap-3">
        <a v-for="button in buttons" :key="button.key" :href="button.href" :target="button.key === 'phone' ? null : '_blank'" rel="noopener"
            class="group relative grid size-12 place-items-center rounded-full text-white shadow-lg ring-4 ring-white/70 transition hover:scale-110"
            :class="button.class" :aria-label="button.label">
            <Icon :name="button.icon" class="size-6" />
            <span class="pointer-events-none absolute right-14 rounded-sm bg-ink px-2.5 py-1 text-xs whitespace-nowrap text-white opacity-0 transition group-hover:opacity-100">{{ button.label }}</span>
        </a>
    </div>
</template>
