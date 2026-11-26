<script setup>
import { useForm } from '@inertiajs/vue3';
import PageBanner from '@/Components/PageBanner.vue';
import SeoHead from '@/Components/SeoHead.vue';
import SiteLayout from '@/Layouts/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

const props = defineProps({ seo: Object, lines: Array, total: String, paymentMethods: Array });

const form = useForm({
    customer_name: '',
    phone: '',
    email: '',
    province: '',
    address: '',
    note: '',
    payment_method: props.paymentMethods[0]?.value ?? 'cod',
});

const submit = () => form.post('/dat-hang');
</script>

<template>
    <SeoHead :seo="seo" />
    <PageBanner title="Đặt hàng" :breadcrumbs="[{ label: 'Giỏ hàng', href: '/gio-hang' }, { label: 'Đặt hàng' }]" />
    <form class="container-site grid gap-10 py-16 lg:grid-cols-3" novalidate @submit.prevent="submit">
        <div class="space-y-5 lg:col-span-2">
            <h2 class="text-2xl font-bold">Thông tin nhận hàng</h2>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="customer_name" class="field-label">Họ tên *</label>
                    <input id="customer_name" v-model="form.customer_name" class="field" autocomplete="name" />
                    <p v-if="form.errors.customer_name" class="mt-1 text-xs text-red-600">{{ form.errors.customer_name }}</p>
                </div>
                <div>
                    <label for="phone" class="field-label">Số điện thoại *</label>
                    <input id="phone" v-model="form.phone" type="tel" inputmode="tel" class="field" autocomplete="tel" />
                    <p v-if="form.errors.phone" class="mt-1 text-xs text-red-600">{{ form.errors.phone }}</p>
                </div>
                <div>
                    <label for="email" class="field-label">Email (để nhận xác nhận đơn)</label>
                    <input id="email" v-model="form.email" type="email" class="field" autocomplete="email" />
                    <p v-if="form.errors.email" class="mt-1 text-xs text-red-600">{{ form.errors.email }}</p>
                </div>
                <div>
                    <label for="province" class="field-label">Tỉnh / Thành phố *</label>
                    <input id="province" v-model="form.province" class="field" autocomplete="address-level1" />
                    <p v-if="form.errors.province" class="mt-1 text-xs text-red-600">{{ form.errors.province }}</p>
                </div>
                <div class="sm:col-span-2">
                    <label for="address" class="field-label">Địa chỉ nhận hàng *</label>
                    <input id="address" v-model="form.address" class="field" autocomplete="street-address" placeholder="Số nhà, đường, phường/xã, quận/huyện" />
                    <p v-if="form.errors.address" class="mt-1 text-xs text-red-600">{{ form.errors.address }}</p>
                </div>
                <div class="sm:col-span-2">
                    <label for="note" class="field-label">Ghi chú</label>
                    <textarea id="note" v-model="form.note" rows="3" class="field" placeholder="Thời gian nhận hàng, yêu cầu thêm…" />
                </div>
            </div>
            <fieldset>
                <legend class="mb-3 text-lg font-bold">Phương thức thanh toán</legend>
                <div class="space-y-2">
                    <label v-for="method in paymentMethods" :key="method.value" class="flex cursor-pointer items-center gap-3 border border-gray-200 px-4 py-3" :class="{ 'border-brand-500 bg-brand-50': form.payment_method === method.value }">
                        <input v-model="form.payment_method" type="radio" :value="method.value" class="accent-brand-500" />
                        <span class="text-sm font-medium">{{ method.label }}</span>
                    </label>
                </div>
                <p class="mt-2 text-xs text-muted">Website không thu tiền online. Nhân viên sẽ gọi xác nhận đơn, phí giao hàng và thông tin chuyển khoản (nếu có).</p>
                <p v-if="form.errors.payment_method" class="mt-1 text-xs text-red-600">{{ form.errors.payment_method }}</p>
            </fieldset>
        </div>
        <aside class="h-fit border-t-4 border-brand-500 bg-paper p-6">
            <h2 class="text-xl font-bold">Đơn hàng của bạn</h2>
            <ul class="mt-5 divide-y divide-gray-200 text-sm">
                <li v-for="line in lines" :key="line.productId" class="flex justify-between gap-4 py-3">
                    <span>{{ line.name }} <span class="text-muted">× {{ line.qty }}</span></span>
                    <span class="font-semibold whitespace-nowrap">{{ line.lineTotal }}</span>
                </li>
            </ul>
            <div class="mt-3 flex justify-between border-t border-gray-200 pt-3"><span class="font-bold">Tổng cộng</span><span class="font-serif text-xl font-bold text-brand-600">{{ total }}</span></div>
            <p class="mt-1 text-xs text-muted">Chưa gồm phí giao hàng.</p>
            <button type="submit" class="btn-primary mt-6 w-full" :disabled="form.processing">{{ form.processing ? 'Đang gửi…' : 'Xác nhận đặt hàng' }}</button>
        </aside>
    </form>
</template>
