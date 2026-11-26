<script setup>
import { usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import FloatingContact from '@/Components/FloatingContact.vue';
import SiteFooter from '@/Components/SiteFooter.vue';
import SiteHeader from '@/Components/SiteHeader.vue';

const page = usePage();
const toast = ref(null);
const flash = computed(() => page.props.flash ?? {});

watch(flash, (value) => {
    const message = value.success || value.error;
    if (!message) return;
    toast.value = { message, type: value.success ? 'success' : 'error' };
    if (typeof window !== 'undefined') setTimeout(() => (toast.value = null), 5000);
}, { immediate: true });
</script>

<template>
    <div class="flex min-h-screen flex-col">
        <a href="#noi-dung" class="sr-only focus:not-sr-only focus:absolute focus:z-50 focus:bg-white focus:p-3">Bỏ qua tới nội dung</a>
        <SiteHeader />
        <main id="noi-dung" class="flex-1">
            <slot />
        </main>
        <SiteFooter />
        <FloatingContact />
        <transition enter-from-class="translate-y-4 opacity-0" leave-to-class="translate-y-4 opacity-0" enter-active-class="transition" leave-active-class="transition">
            <div v-if="toast" role="status"
                class="fixed bottom-6 left-1/2 z-50 -translate-x-1/2 rounded-sm px-5 py-3 text-sm font-medium text-white shadow-lg"
                :class="toast.type === 'success' ? 'bg-brand-600' : 'bg-red-600'">
                {{ toast.message }}
            </div>
        </transition>
    </div>
</template>
