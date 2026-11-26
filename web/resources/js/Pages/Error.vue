<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import Icon from '@/Components/Icon.vue';
import SeoHead from '@/Components/SeoHead.vue';
import SiteLayout from '@/Layouts/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

const props = defineProps({ status: Number });

const messages = {
    403: { title: 'Không có quyền truy cập', text: 'Bạn không có quyền xem trang này.' },
    404: { title: 'Không tìm thấy trang', text: 'Trang bạn tìm có thể đã bị xoá hoặc đổi địa chỉ.' },
    419: { title: 'Phiên làm việc đã hết hạn', text: 'Vui lòng tải lại trang và thử lại.' },
    429: { title: 'Thao tác quá nhanh', text: 'Bạn đã gửi quá nhiều yêu cầu, vui lòng thử lại sau ít phút.' },
    500: { title: 'Có lỗi xảy ra', text: 'Hệ thống đang gặp sự cố, vui lòng thử lại sau.' },
    503: { title: 'Đang bảo trì', text: 'Website đang được bảo trì, vui lòng quay lại sau.' },
};
const info = computed(() => messages[props.status] ?? messages[500]);
const seo = computed(() => ({ title: info.value.title, description: info.value.text, jsonLd: [] }));
</script>

<template>
    <SeoHead :seo="seo" />
    <section class="container-site grid min-h-[60vh] place-items-center py-20 text-center">
        <div>
            <Icon name="leaf" class="mx-auto size-16 text-brand-500" />
            <p class="mt-4 font-serif text-7xl font-bold text-brand-500">{{ status }}</p>
            <h1 class="mt-2 text-3xl font-bold">{{ info.title }}</h1>
            <p class="mx-auto mt-3 max-w-md text-muted">{{ info.text }}</p>
            <div class="mt-8 flex justify-center gap-3">
                <Link href="/" class="btn-primary">Về trang chủ</Link>
                <Link href="/lien-he" class="btn-ghost">Liên hệ</Link>
            </div>
        </div>
    </section>
</template>
