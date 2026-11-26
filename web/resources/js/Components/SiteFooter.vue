<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useSite } from '@/Composables/useSite';
import Icon from './Icon.vue';

const page = usePage();
const { site, telHref } = useSite();
const services = computed(() => page.props.menu?.services ?? []);
const year = new Date().getFullYear();

const links = [
    { label: 'Giới thiệu', href: '/gioi-thieu' },
    { label: 'Cây cảnh nghệ thuật', href: '/cay-canh-nghe-thuat' },
    { label: 'Sản phẩm', href: '/san-pham' },
    { label: 'Dự án đã thực hiện', href: '/du-an' },
    { label: 'Bảng giá dịch vụ', href: '/bang-gia' },
    { label: 'Câu hỏi thường gặp', href: '/cau-hoi-thuong-gap' },
    { label: 'Đánh giá khách hàng', href: '/danh-gia-khach-hang' },
];

const socials = computed(() => [
    { name: 'facebook', href: site.value.facebook_url },
    { name: 'tiktok', href: site.value.tiktok_url },
    { name: 'youtube', href: site.value.youtube_url },
].filter((item) => item.href));

const toTop = () => window.scrollTo({ top: 0, behavior: 'smooth' });
</script>

<template>
    <footer class="bg-[#1e1e1e] bg-[url('/images/theme/20.jpg')] bg-center pt-[75px] text-[#9e9e9e]">
        <div class="container-site grid gap-12 border-b border-dashed border-[#303030] pb-16 sm:grid-cols-2 lg:grid-cols-[1.3fr_0.8fr_0.8fr_1.1fr] lg:pb-[90px]">
            <div>
                <img src="/images/logo.png" :alt="site.site_name" width="480" height="320" class="mb-6 h-16 w-auto" loading="lazy" />
                <h3 class="mb-9 text-2xl text-white">Về <span class="title-leaf title-leaf-xs">{{ site.site_name }}</span></h3>
                <p class="text-base leading-[30px]">
                    Đơn vị tư vấn, thiết kế, cung cấp, cho thuê và chăm sóc cây xanh cho văn phòng, biệt thự, khu đô thị, resort; sưu tầm và chế tác cây cảnh nghệ thuật độc bản.
                </p>
                <Link href="/bao-gia" class="btn-primary mt-8 w-60">Nhận báo giá</Link>
            </div>
            <div>
                <h3 class="mb-9 text-2xl text-white">Dịch vụ</h3>
                <ul class="space-y-3">
                    <li v-for="service in services" :key="service.url">
                        <Link :href="service.url" class="flex items-start gap-3 hover:text-brand-500">
                            <Icon name="check" class="mt-0.5 size-4 shrink-0 rounded-sm bg-[#9e9e9e] p-0.5 text-[#1e1e1e]" />{{ service.name }}
                        </Link>
                    </li>
                </ul>
            </div>
            <div>
                <h3 class="mb-9 text-2xl text-white">Liên kết</h3>
                <ul class="space-y-3">
                    <li v-for="link in links" :key="link.href">
                        <Link :href="link.href" class="flex items-start gap-3 hover:text-brand-500">
                            <Icon name="check" class="mt-0.5 size-4 shrink-0 rounded-sm bg-[#9e9e9e] p-0.5 text-[#1e1e1e]" />{{ link.label }}
                        </Link>
                    </li>
                </ul>
            </div>
            <div>
                <h3 class="mb-9 text-2xl text-white">Liên hệ</h3>
                <ul class="space-y-5 text-lg">
                    <li v-if="site.address" class="flex gap-4"><Icon name="pin" class="mt-1 size-5 shrink-0" />{{ site.address }}</li>
                    <li v-if="site.hotline" class="flex gap-4"><Icon name="phone" class="mt-1 size-5 shrink-0" /><a :href="telHref" class="hover:text-brand-500">Hotline: {{ site.hotline }}</a></li>
                    <li v-if="site.email" class="flex gap-4"><Icon name="mail" class="mt-1 size-5 shrink-0" /><a :href="'mailto:' + site.email" class="break-all hover:text-brand-500">{{ site.email }}</a></li>
                    <li v-if="site.working_hours" class="flex gap-4"><Icon name="clock" class="mt-1 size-5 shrink-0" />{{ site.working_hours }}</li>
                </ul>
            </div>
        </div>
        <div class="container-site relative flex flex-col items-center gap-4 py-5 md:flex-row md:justify-between">
            <p class="text-base font-medium">© {{ year }} <span class="text-brand-500">{{ site.site_name }}</span>. Bảo lưu mọi quyền.</p>
            <ul class="flex gap-3">
                <li v-for="social in socials" :key="social.name">
                    <a :href="social.href" target="_blank" rel="noopener" :aria-label="social.name"
                        class="grid size-[45px] place-items-center rounded-full border border-dashed border-[#424242] bg-[#262626] text-white transition hover:bg-brand-500">
                        <Icon :name="social.name" class="size-4" />
                    </a>
                </li>
            </ul>
            <button type="button" class="grid size-12 place-items-center rounded border-2 border-white/10 text-[#9e9e9e] hover:text-brand-500" aria-label="Lên đầu trang" @click="toTop">
                <Icon name="chevronDown" class="size-5 rotate-180" />
            </button>
        </div>
    </footer>
</template>
