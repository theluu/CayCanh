<script setup>
import { useSite } from '@/Composables/useSite';
import Icon from './Icon.vue';

const { site, telHref, zaloHref } = useSite();
</script>

<template>
    <ul class="space-y-5 text-sm">
        <li v-if="site.hotline" class="flex gap-4">
            <span class="grid size-11 shrink-0 place-items-center rounded-full bg-brand-500 text-white"><Icon name="phone" /></span>
            <span><span class="block text-muted">Hotline</span><a :href="telHref" class="font-serif text-lg font-bold hover:text-brand-600">{{ site.hotline }}</a></span>
        </li>
        <li v-if="zaloHref" class="flex gap-4">
            <span class="grid size-11 shrink-0 place-items-center rounded-full bg-[#0068ff] text-white"><Icon name="zalo" /></span>
            <span><span class="block text-muted">Zalo</span><a :href="zaloHref" target="_blank" rel="noopener" class="font-semibold hover:text-brand-600">Chat Zalo {{ site.zalo_phone }}</a></span>
        </li>
        <li v-if="site.email" class="flex gap-4">
            <span class="grid size-11 shrink-0 place-items-center rounded-full bg-brand-500 text-white"><Icon name="mail" /></span>
            <span><span class="block text-muted">Email</span><a :href="'mailto:' + site.email" class="font-semibold hover:text-brand-600">{{ site.email }}</a></span>
        </li>
        <li v-if="site.address" class="flex gap-4">
            <span class="grid size-11 shrink-0 place-items-center rounded-full bg-brand-500 text-white"><Icon name="pin" /></span>
            <span><span class="block text-muted">Địa chỉ</span>{{ site.address }}</span>
        </li>
        <li v-if="site.working_hours" class="flex gap-4">
            <span class="grid size-11 shrink-0 place-items-center rounded-full bg-brand-500 text-white"><Icon name="clock" /></span>
            <span><span class="block text-muted">Giờ làm việc</span>{{ site.working_hours }}</span>
        </li>
    </ul>
</template>
