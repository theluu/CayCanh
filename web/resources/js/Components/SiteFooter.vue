<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useSite } from '@/Composables/useSite';
import Icon from './Icon.vue';
import Logo from './Logo.vue';

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
    { label: 'Tin tức – kiến thức', href: '/tin-tuc' },
    { label: 'Câu hỏi thường gặp', href: '/cau-hoi-thuong-gap' },
    { label: 'Đánh giá khách hàng', href: '/danh-gia-khach-hang' },
];

const socials = computed(() => [
    { name: 'facebook', href: site.value.facebook_url },
    { name: 'tiktok', href: site.value.tiktok_url },
    { name: 'youtube', href: site.value.youtube_url },
].filter((item) => item.href));
</script>

<template>
    <footer class="bg-ink text-sm text-gray-400">
        <div class="container-site grid gap-10 py-16 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <Logo :name="site.site_name" light />
                <p class="mt-5 leading-relaxed">
                    Đơn vị tư vấn, cung cấp, cho thuê và chăm sóc cây xanh công trình; sưu tầm và chế tác cây cảnh nghệ thuật độc bản.
                </p>
                <Link href="/bao-gia" class="btn-primary mt-6">Nhận báo giá</Link>
            </div>
            <div>
                <h3 class="mb-5 text-lg text-white">Dịch vụ</h3>
                <ul class="space-y-2.5">
                    <li v-for="service in services" :key="service.url">
                        <Link :href="service.url" class="inline-flex items-center gap-2 hover:text-brand-400">
                            <Icon name="chevronRight" class="size-3 text-brand-500" />{{ service.name }}
                        </Link>
                    </li>
                </ul>
            </div>
            <div>
                <h3 class="mb-5 text-lg text-white">Liên kết</h3>
                <ul class="space-y-2.5">
                    <li v-for="link in links" :key="link.href">
                        <Link :href="link.href" class="inline-flex items-center gap-2 hover:text-brand-400">
                            <Icon name="chevronRight" class="size-3 text-brand-500" />{{ link.label }}
                        </Link>
                    </li>
                </ul>
            </div>
            <div>
                <h3 class="mb-5 text-lg text-white">Liên hệ</h3>
                <ul class="space-y-4">
                    <li v-if="site.address" class="flex gap-3"><Icon name="pin" class="size-5 shrink-0 text-brand-500" />{{ site.address }}</li>
                    <li v-if="site.hotline" class="flex gap-3"><Icon name="phone" class="size-5 shrink-0 text-brand-500" /><a :href="telHref" class="hover:text-white">{{ site.hotline }}</a></li>
                    <li v-if="site.email" class="flex gap-3"><Icon name="mail" class="size-5 shrink-0 text-brand-500" /><a :href="'mailto:' + site.email" class="hover:text-white">{{ site.email }}</a></li>
                    <li v-if="site.working_hours" class="flex gap-3"><Icon name="clock" class="size-5 shrink-0 text-brand-500" />{{ site.working_hours }}</li>
                </ul>
            </div>
        </div>
        <div class="border-t border-white/10">
            <div class="container-site flex flex-col items-center justify-between gap-3 py-5 sm:flex-row">
                <p>© {{ year }} {{ site.site_name }}. Bảo lưu mọi quyền.</p>
                <div class="flex gap-2">
                    <a v-for="social in socials" :key="social.name" :href="social.href" target="_blank" rel="noopener"
                        class="grid size-8 place-items-center rounded-full bg-white/10 text-white hover:bg-brand-500" :aria-label="social.name">
                        <Icon :name="social.name" class="size-4" />
                    </a>
                </div>
            </div>
        </div>
    </footer>
</template>
