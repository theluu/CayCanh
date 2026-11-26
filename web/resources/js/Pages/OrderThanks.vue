<script setup>
import { Link } from '@inertiajs/vue3';
import Icon from '@/Components/Icon.vue';
import SeoHead from '@/Components/SeoHead.vue';
import SiteLayout from '@/Layouts/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

defineProps({ seo: Object, order: Object });
</script>

<template>
    <SeoHead :seo="seo" />
    <section class="container-site max-w-3xl py-20">
        <div class="text-center">
            <span class="mx-auto grid size-16 place-items-center rounded-full bg-brand-500 text-white"><Icon name="check" class="size-8" /></span>
            <h1 class="mt-5 text-3xl font-bold">Đặt hàng thành công!</h1>
            <p class="mt-3 text-muted">Cảm ơn {{ order.customerName }}. Mã đơn của bạn là <strong class="text-ink">{{ order.code }}</strong>. Nhân viên sẽ gọi tới <strong class="text-ink">{{ order.phone }}</strong> để xác nhận đơn và phí giao hàng.</p>
        </div>
        <div class="mt-10 border-t-4 border-brand-500 bg-paper p-6">
            <ul class="divide-y divide-gray-200 text-sm">
                <li v-for="item in order.items" :key="item.name" class="flex justify-between py-3"><span>{{ item.name }} × {{ item.qty }}</span><span class="font-semibold">{{ item.lineTotal }}</span></li>
            </ul>
            <dl class="mt-4 space-y-2 border-t border-gray-200 pt-4 text-sm">
                <div class="flex justify-between"><dt class="text-muted">Tổng tạm tính</dt><dd class="font-bold text-brand-600">{{ order.total }}</dd></div>
                <div class="flex justify-between gap-6"><dt class="text-muted">Giao tới</dt><dd class="text-right">{{ order.address }}</dd></div>
                <div class="flex justify-between"><dt class="text-muted">Thanh toán</dt><dd>{{ order.paymentMethod }}</dd></div>
            </dl>
        </div>
        <div class="mt-8 text-center"><Link href="/" class="btn-primary">Về trang chủ</Link></div>
    </section>
</template>
