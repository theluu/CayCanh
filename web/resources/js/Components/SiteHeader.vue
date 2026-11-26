<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { useSite } from '@/Composables/useSite';
import Icon from './Icon.vue';
import Logo from './Logo.vue';

const page = usePage();
const { site, telHref, zaloHref } = useSite();
const open = ref(false);
const servicesOpen = ref(false);
const sticky = ref(false);
const brandRow = ref(null);

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

// Address shown like the template: first part bold, the rest grey underneath.
const addressMain = computed(() => (site.value.address ?? '').split(',')[0]);
const addressRest = computed(() => (site.value.address ?? '').split(',').slice(1).join(',').trim());

const socials = computed(() => [
    { name: 'facebook', href: site.value.facebook_url },
    { name: 'tiktok', href: site.value.tiktok_url },
    { name: 'youtube', href: site.value.youtube_url },
].filter((item) => item.href));

const onScroll = () => {
    const limit = (brandRow.value?.offsetTop ?? 0) + (brandRow.value?.offsetHeight ?? 0);
    sticky.value = window.scrollY > limit;
};

onMounted(() => {
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
});
onBeforeUnmount(() => window.removeEventListener('scroll', onScroll));

watch(() => page.url, () => {
    open.value = false;
    servicesOpen.value = false;
});
</script>

<template>
    <header class="relative z-40">
        <!-- Top bar -->
        <div class="hidden bg-ink py-2.5 text-sm font-black text-[#6b6b6b] md:block">
            <div class="container-site flex items-center justify-between gap-6">
                <p>Chuyên <span class="text-brand-500">tư vấn, cung cấp, cho thuê và chăm sóc</span> cây xanh công trình &amp; cây cảnh nghệ thuật.</p>
                <ul class="flex shrink-0 items-center gap-6">
                    <li v-if="zaloHref"><a :href="zaloHref" target="_blank" rel="noopener" class="hover:text-brand-500">Zalo</a></li>
                    <li><Link href="/bang-gia" class="hover:text-brand-500">Bảng giá</Link></li>
                    <li><Link href="/bao-gia" class="hover:text-brand-500">Yêu cầu báo giá</Link></li>
                </ul>
            </div>
        </div>

        <!-- Brand row -->
        <div ref="brandRow" class="bg-white">
            <div class="container-site flex items-center justify-between gap-6 pt-5 pb-4 lg:pb-[60px]">
                <Logo :name="site.site_name" />
                <div class="hidden items-center gap-10 lg:flex">
                    <div v-if="site.address" class="flex items-start gap-4">
                        <Icon name="pin" class="size-10 shrink-0 text-brand-500" />
                        <p class="max-w-56">
                            <span class="block text-xl leading-tight font-black text-ink">{{ addressMain }}</span>
                            <span class="block pt-1.5 text-base text-muted">{{ addressRest }}</span>
                        </p>
                    </div>
                    <div v-if="site.working_hours" class="flex items-start gap-4">
                        <span class="grid size-10 shrink-0 place-items-center rounded-full border-[3px] border-brand-500 text-brand-500"><Icon name="clock" class="size-6" /></span>
                        <p class="max-w-52">
                            <span class="block text-xl leading-tight font-black text-ink">{{ site.working_hours.split(':').slice(1).join(':').trim() || site.working_hours }}</span>
                            <span class="block pt-1.5 text-base text-muted">{{ site.working_hours.split(':')[0] }}</span>
                        </p>
                    </div>
                    <ul class="flex gap-3">
                        <li v-for="social in socials" :key="social.name">
                            <a :href="social.href" target="_blank" rel="noopener" :aria-label="social.name"
                                class="dashed-ring grid size-[45px] place-items-center rounded-full bg-brand-500 text-white transition hover:bg-ink">
                                <Icon :name="social.name" class="size-4" />
                            </a>
                        </li>
                        <li>
                            <Link href="/gio-hang" aria-label="Giỏ hàng" class="dashed-ring relative grid size-[45px] place-items-center rounded-full bg-ink text-white transition hover:bg-brand-500">
                                <Icon name="cart" class="size-5" />
                                <span class="absolute -top-1 -right-1 grid size-5 place-items-center rounded-full bg-brand-500 text-[11px] font-bold ring-2 ring-white">{{ cartCount }}</span>
                            </Link>
                        </li>
                    </ul>
                </div>
                <div class="flex items-center gap-1 lg:hidden">
                    <Link href="/gio-hang" class="relative p-2 text-ink" aria-label="Giỏ hàng">
                        <Icon name="cart" class="size-7" />
                        <span v-if="cartCount" class="absolute top-0 right-0 grid size-5 place-items-center rounded-full bg-brand-500 text-[10px] font-bold text-white">{{ cartCount }}</span>
                    </Link>
                    <button type="button" class="p-2 text-ink" :aria-expanded="open" aria-label="Mở menu" @click="open = !open">
                        <Icon :name="open ? 'close' : 'menu'" class="size-8" />
                    </button>
                </div>
            </div>
        </div>

        <!-- Main menu: dark menu + green hotline block, overlapping the hero like the template -->
        <div class="hidden lg:block" :class="sticky ? 'h-[90px]' : ''">
            <nav aria-label="Menu chính" class="z-50 w-full transition-shadow"
                :class="sticky ? 'fixed inset-x-0 top-0 shadow-[0_8px_20px_rgba(0,0,0,0.25)]' : 'absolute inset-x-0 -mt-[45px]'">
                <div class="absolute inset-y-0 right-0 left-1/2 bg-brand-500" aria-hidden="true" />
                <div class="absolute inset-y-0 left-0 w-1/2 bg-ink" :class="sticky ? '' : 'opacity-0'" aria-hidden="true" />
                <div class="container-site relative flex h-[90px]">
                    <div class="flex-1 bg-ink py-2.5 pl-2">
                        <ul class="flex h-full border border-r-0 border-dashed border-[#424242]">
                            <li v-for="item in nav" :key="item.href" class="group relative flex-auto">
                                <Link :href="item.href"
                                    class="flex h-full items-center justify-center border-r border-dashed border-[#424242] px-[17px] text-[13px] font-black whitespace-nowrap text-[#fcfcfc] uppercase transition hover:text-brand-500 xl:px-[19px] xl:text-sm"
                                    :class="{ '!text-brand-500': isActive(item.href) }">
                                    {{ item.label }}
                                </Link>
                                <ul v-if="item.children && services.length"
                                    class="invisible absolute top-[69px] left-0 w-64 rounded-b border-t-[10px] border-ink bg-brand-500 opacity-0 transition-all duration-500 group-hover:visible group-hover:opacity-100">
                                    <li v-for="service in services" :key="service.url">
                                        <Link :href="service.url" class="block border-b border-dashed border-brand-600 px-5 py-3 text-sm font-black text-white hover:bg-brand-600">{{ service.name }}</Link>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                    <div class="flex items-center bg-brand-500 py-2.5 pl-2.5 xl:pr-0">
                        <a v-if="telHref" :href="telHref" class="flex h-full items-center gap-4 border border-dashed border-white/60 px-5 font-serif text-[26px] font-bold whitespace-nowrap text-white xl:px-6 xl:text-[30px]">
                            <Icon name="phone" class="size-6 shrink-0" />
                            {{ site.hotline }}
                        </a>
                    </div>
                </div>
            </nav>
        </div>

        <!-- Mobile menu -->
        <nav v-show="open" class="bg-ink lg:hidden" aria-label="Menu di động">
            <a v-if="telHref" :href="telHref" class="flex items-center justify-center gap-3 bg-brand-500 py-3 font-serif text-xl font-bold text-white">
                <Icon name="phone" class="size-5" />{{ site.hotline }}
            </a>
            <ul class="container-site py-2">
                <li v-for="item in nav" :key="item.href" class="border-b border-dashed border-[#424242]">
                    <div class="flex items-center justify-between">
                        <Link :href="item.href" class="block flex-1 py-3.5 text-sm font-black text-white uppercase" :class="{ '!text-brand-500': isActive(item.href) }">{{ item.label }}</Link>
                        <button v-if="item.children && services.length" type="button" class="p-3 text-white" aria-label="Mở dịch vụ" @click="servicesOpen = !servicesOpen">
                            <Icon name="chevronDown" class="size-4 transition" :class="{ 'rotate-180': servicesOpen }" />
                        </button>
                    </div>
                    <ul v-if="item.children" v-show="servicesOpen" class="mb-2 bg-brand-500">
                        <li v-for="service in services" :key="service.url">
                            <Link :href="service.url" class="block border-b border-dashed border-brand-600 px-4 py-2.5 text-sm font-bold text-white">{{ service.name }}</Link>
                        </li>
                    </ul>
                </li>
                <li class="py-4"><Link href="/bao-gia" class="btn-primary w-full">Nhận báo giá</Link></li>
            </ul>
        </nav>
    </header>
</template>
