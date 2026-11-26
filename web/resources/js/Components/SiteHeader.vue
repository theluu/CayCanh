<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import { useSite } from '@/Composables/useSite';
import Icon from './Icon.vue';
import Logo from './Logo.vue';

const page = usePage();
const { site, telHref } = useSite();
const open = ref(false);
const servicesOpen = ref(false);

const services = computed(() => page.props.menu?.services ?? []);
const cartCount = computed(() => page.props.cart?.count ?? 0);

const nav = [
    { label: 'Trang chủ', href: '/' },
    { label: 'Giới thiệu', href: '/gioi-thieu' },
    { label: 'Dịch vụ', href: '/dich-vu', children: true },
    { label: 'Cây nghệ thuật', href: '/cay-canh-nghe-thuat' },
    { label: 'Sản phẩm', href: '/san-pham' },
    { label: 'Dự án', href: '/du-an' },
    { label: 'Tin tức', href: '/tin-tuc' },
    { label: 'Liên hệ', href: '/lien-he' },
];

const isActive = (href) => (href === '/' ? page.url === '/' : page.url.startsWith(href));

watch(() => page.url, () => {
    open.value = false;
    servicesOpen.value = false;
});

const socials = computed(() => [
    { name: 'facebook', href: site.value.facebook_url },
    { name: 'tiktok', href: site.value.tiktok_url },
    { name: 'youtube', href: site.value.youtube_url },
].filter((item) => item.href));
</script>

<template>
    <header class="relative z-40">
        <div class="hidden bg-ink text-xs text-gray-300 md:block">
            <div class="container-site flex h-9 items-center justify-between">
                <p>Tư vấn – cung cấp – cho thuê – chăm sóc <span class="text-brand-400">cây xanh công trình</span> & cây cảnh nghệ thuật</p>
                <div class="flex items-center gap-4">
                    <a v-if="site.email" :href="'mailto:' + site.email" class="hover:text-white">{{ site.email }}</a>
                    <Link href="/bao-gia" class="hover:text-white">Yêu cầu báo giá</Link>
                </div>
            </div>
        </div>

        <div class="bg-white">
            <div class="container-site flex items-center justify-between gap-6 py-4">
                <Logo :name="site.site_name" />
                <div class="hidden items-center gap-8 lg:flex">
                    <div v-if="site.address" class="flex max-w-64 items-start gap-3">
                        <Icon name="pin" class="mt-0.5 size-6 shrink-0 text-brand-500" />
                        <p class="text-sm leading-snug text-muted">{{ site.address }}</p>
                    </div>
                    <div v-if="site.working_hours" class="flex items-start gap-3">
                        <Icon name="clock" class="mt-0.5 size-6 shrink-0 text-brand-500" />
                        <p class="max-w-48 text-sm leading-snug text-muted">{{ site.working_hours }}</p>
                    </div>
                    <div v-if="socials.length" class="flex gap-2">
                        <a v-for="social in socials" :key="social.name" :href="social.href" target="_blank" rel="noopener"
                            class="grid size-8 place-items-center rounded-full bg-brand-500 text-white transition hover:bg-brand-600" :aria-label="social.name">
                            <Icon :name="social.name" class="size-4" />
                        </a>
                    </div>
                </div>
                <div class="flex items-center gap-2 lg:hidden">
                    <Link href="/gio-hang" class="relative p-2 text-ink" aria-label="Giỏ hàng">
                        <Icon name="cart" class="size-6" />
                        <span v-if="cartCount" class="absolute -top-0.5 -right-0.5 grid size-5 place-items-center rounded-full bg-brand-500 text-[10px] font-bold text-white">{{ cartCount }}</span>
                    </Link>
                    <button type="button" class="p-2 text-ink" :aria-expanded="open" aria-label="Mở menu" @click="open = !open">
                        <Icon :name="open ? 'close' : 'menu'" class="size-7" />
                    </button>
                </div>
            </div>
        </div>

        <nav class="bg-ink" aria-label="Menu chính">
            <div class="container-site flex items-stretch justify-between">
                <ul class="hidden items-stretch lg:flex">
                    <li v-for="item in nav" :key="item.href" class="group relative">
                        <Link :href="item.href"
                            class="flex h-full items-center gap-1 px-3.5 py-4 text-[13px] font-semibold tracking-wide text-white uppercase transition hover:bg-brand-500 xl:px-4"
                            :class="{ 'bg-brand-500': isActive(item.href) }">
                            {{ item.label }}
                            <Icon v-if="item.children && services.length" name="chevronDown" class="size-3.5" />
                        </Link>
                        <ul v-if="item.children && services.length"
                            class="invisible absolute top-full left-0 min-w-64 translate-y-2 bg-white py-2 opacity-0 shadow-xl transition group-hover:visible group-hover:translate-y-0 group-hover:opacity-100">
                            <li v-for="service in services" :key="service.url">
                                <Link :href="service.url" class="block border-l-2 border-transparent px-5 py-2.5 text-sm text-ink hover:border-brand-500 hover:bg-brand-50 hover:text-brand-700">{{ service.name }}</Link>
                            </li>
                        </ul>
                    </li>
                </ul>
                <div class="hidden items-center lg:flex">
                    <Link href="/gio-hang" class="relative px-4 py-4 text-white hover:text-brand-300" aria-label="Giỏ hàng">
                        <Icon name="cart" class="size-5" />
                        <span v-if="cartCount" class="absolute top-2 right-1.5 grid size-4.5 place-items-center rounded-full bg-brand-500 text-[10px] font-bold">{{ cartCount }}</span>
                    </Link>
                </div>
                <a v-if="telHref" :href="telHref" class="flex items-center gap-2 bg-brand-500 px-5 py-3 font-semibold text-white lg:py-0">
                    <Icon name="phone" class="size-5" />
                    <span>{{ site.hotline }}</span>
                </a>
            </div>

            <div v-show="open" class="border-t border-white/10 lg:hidden">
                <ul class="container-site py-2">
                    <li v-for="item in nav" :key="item.href">
                        <div class="flex items-center justify-between">
                            <Link :href="item.href" class="block flex-1 py-3 text-sm font-semibold text-white uppercase" :class="{ 'text-brand-400': isActive(item.href) }">{{ item.label }}</Link>
                            <button v-if="item.children && services.length" type="button" class="p-3 text-white" aria-label="Mở dịch vụ" @click="servicesOpen = !servicesOpen">
                                <Icon name="chevronDown" class="size-4 transition" :class="{ 'rotate-180': servicesOpen }" />
                            </button>
                        </div>
                        <ul v-if="item.children" v-show="servicesOpen" class="mb-2 border-l border-brand-500 pl-4">
                            <li v-for="service in services" :key="service.url">
                                <Link :href="service.url" class="block py-2 text-sm text-gray-300">{{ service.name }}</Link>
                            </li>
                        </ul>
                    </li>
                    <li class="py-3"><Link href="/bao-gia" class="btn-primary w-full">Nhận báo giá</Link></li>
                </ul>
            </div>
        </nav>
    </header>
</template>
