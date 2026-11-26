<script setup>
import { Link, router } from '@inertiajs/vue3';
import Icon from '@/Components/Icon.vue';
import PageBanner from '@/Components/PageBanner.vue';
import Picture from '@/Components/Picture.vue';
import SeoHead from '@/Components/SeoHead.vue';
import SiteLayout from '@/Layouts/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

defineProps({ seo: Object, lines: Array, total: String, pruned: Array });

const setQty = (line, qty) => {
    router.patch('/gio-hang/' + line.productId, { qty: Math.max(0, Math.min(99, Number(qty) || 0)) }, { preserveScroll: true });
};
const remove = (line) => router.delete('/gio-hang/' + line.productId, { preserveScroll: true });
</script>

<template>
    <SeoHead :seo="seo" />
    <PageBanner title="Giỏ hàng" :breadcrumbs="[{ label: 'Giỏ hàng' }]" />
    <section class="container-site py-16">
        <div v-if="pruned.length" class="mb-6 border-l-4 border-amber-500 bg-amber-50 px-4 py-3 text-sm">
            Một số sản phẩm không còn bán đã được bỏ khỏi giỏ: <strong>{{ pruned.join(', ') }}</strong>.
        </div>
        <div v-if="lines.length" class="grid gap-10 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <div class="hidden grid-cols-12 border-b border-gray-200 pb-3 text-xs font-semibold tracking-wide text-muted uppercase md:grid">
                    <span class="col-span-6">Sản phẩm</span><span class="col-span-2 text-right">Đơn giá</span><span class="col-span-2 text-center">Số lượng</span><span class="col-span-2 text-right">Thành tiền</span>
                </div>
                <div v-for="line in lines" :key="line.productId" class="grid grid-cols-12 items-center gap-3 border-b border-gray-100 py-5">
                    <div class="col-span-12 flex items-center gap-4 md:col-span-6">
                        <button type="button" class="text-gray-400 hover:text-red-600" :aria-label="'Xoá ' + line.name" @click="remove(line)"><Icon name="trash" class="size-5" /></button>
                        <Link :href="line.url" class="size-20 shrink-0 overflow-hidden bg-paper"><Picture :image="line.image" :alt="line.name" size="thumb" /></Link>
                        <Link :href="line.url" class="font-serif font-bold hover:text-brand-600">{{ line.name }}</Link>
                    </div>
                    <span class="col-span-4 text-sm md:col-span-2 md:text-right">{{ line.unitPrice }}</span>
                    <div class="col-span-4 flex justify-center md:col-span-2">
                        <div class="flex items-center border border-gray-200">
                            <button type="button" class="px-2.5 py-1.5" aria-label="Giảm" @click="setQty(line, line.qty - 1)">−</button>
                            <input :value="line.qty" type="number" min="0" max="99" class="w-12 border-x border-gray-200 py-1.5 text-center text-sm" :aria-label="'Số lượng ' + line.name" @change="setQty(line, $event.target.value)" />
                            <button type="button" class="px-2.5 py-1.5" aria-label="Tăng" @click="setQty(line, line.qty + 1)">+</button>
                        </div>
                    </div>
                    <span class="col-span-4 text-right font-semibold text-brand-600 md:col-span-2">{{ line.lineTotal }}</span>
                </div>
                <Link href="/san-pham" class="mt-6 inline-flex items-center gap-1 text-sm font-semibold text-brand-600 hover:underline">← Tiếp tục mua sắm</Link>
            </div>
            <aside class="h-fit border-t-4 border-brand-500 bg-paper p-6">
                <h2 class="text-xl font-bold">Tổng đơn hàng</h2>
                <dl class="mt-5 space-y-3 text-sm">
                    <div class="flex justify-between"><dt>Tạm tính</dt><dd class="font-semibold">{{ total }}</dd></div>
                    <div class="flex justify-between gap-6"><dt>Phí giao hàng</dt><dd class="text-right text-muted">Nhân viên sẽ liên hệ xác nhận</dd></div>
                    <div class="flex justify-between border-t border-gray-200 pt-3 text-base"><dt class="font-bold">Tổng cộng</dt><dd class="font-serif text-xl font-bold text-brand-600">{{ total }}</dd></div>
                </dl>
                <Link href="/dat-hang" class="btn-primary mt-6 w-full">Tiến hành đặt hàng</Link>
            </aside>
        </div>
        <div v-else class="py-16 text-center">
            <Icon name="cart" class="mx-auto size-16 text-gray-300" />
            <p class="mt-4 text-lg">Giỏ hàng của bạn đang trống.</p>
            <Link href="/san-pham" class="btn-primary mt-6">Xem sản phẩm</Link>
        </div>
    </section>
</template>
