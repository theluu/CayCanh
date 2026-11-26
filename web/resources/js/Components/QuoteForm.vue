<script setup>
import { useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    type: { type: String, default: 'contact' },
    subjectType: { type: String, default: null },
    subjectId: { type: Number, default: null },
    subjectLabel: { type: String, default: null },
    title: { type: String, default: null },
    submitLabel: { type: String, default: 'Gửi yêu cầu' },
    variant: { type: String, default: 'default' },
});

const form = useForm({
    type: props.type,
    subject_type: props.subjectType,
    subject_id: props.subjectId,
    name: '',
    phone: '',
    email: '',
    address: '',
    message: '',
    meta: { project_kind: '', location: '', area: '', budget: '', timeline: '' },
    website: '',
});

const isSurvey = computed(() => props.type === 'survey');
const fieldClass = computed(() => (props.variant === 'template' ? 'field-template' : 'field'));
const labelClass = computed(() => (props.variant === 'template' ? 'sr-only' : 'field-label'));
const ph = (text) => (props.variant === 'template' ? text : undefined);

const submit = () => {
    form.post('/lien-he', {
        preserveScroll: true,
        onSuccess: () => form.reset('name', 'phone', 'email', 'address', 'message', 'meta'),
    });
};

const projectKinds = ['Văn phòng', 'Biệt thự / nhà phố', 'Khu đô thị / chung cư', 'Resort / khách sạn', 'Trường học / bệnh viện', 'Nhà xưởng / KCN', 'Khác'];
</script>

<template>
    <form class="space-y-4" novalidate @submit.prevent="submit">
        <h3 v-if="title" class="text-2xl font-bold">{{ title }}</h3>
        <p v-if="subjectLabel" class="border-l-4 border-brand-500 bg-brand-50 px-4 py-2 text-sm">Yêu cầu cho: <strong>{{ subjectLabel }}</strong></p>

        <div class="hidden" aria-hidden="true">
            <label>Website <input v-model="form.website" type="text" tabindex="-1" autocomplete="off" /></label>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label :class="labelClass" :for="'name-' + type">Họ tên *</label>
                <input :id="'name-' + type" v-model="form.name" :placeholder="ph('Họ tên *')" type="text" :class="fieldClass" autocomplete="name" required />
                <p v-if="form.errors.name" class="mt-1 text-xs text-red-600">{{ form.errors.name }}</p>
            </div>
            <div>
                <label :class="labelClass" :for="'phone-' + type">Số điện thoại *</label>
                <input :id="'phone-' + type" v-model="form.phone" :placeholder="ph('Số điện thoại *')" type="tel" :class="fieldClass" autocomplete="tel" inputmode="tel" required />
                <p v-if="form.errors.phone" class="mt-1 text-xs text-red-600">{{ form.errors.phone }}</p>
            </div>
            <div :class="isSurvey ? '' : 'sm:col-span-2'">
                <label :class="labelClass" :for="'email-' + type">Email</label>
                <input :id="'email-' + type" v-model="form.email" :placeholder="ph('Email')" type="email" :class="fieldClass" autocomplete="email" />
                <p v-if="form.errors.email" class="mt-1 text-xs text-red-600">{{ form.errors.email }}</p>
            </div>
            <template v-if="isSurvey">
                <div>
                    <label :class="labelClass" for="project-kind">Loại công trình</label>
                    <select id="project-kind" v-model="form.meta.project_kind" :class="fieldClass">
                        <option value="">{{ variant === 'template' ? 'Loại công trình' : '-- Chọn --' }}</option>
                        <option v-for="kind in projectKinds" :key="kind">{{ kind }}</option>
                    </select>
                </div>
                <div>
                    <label :class="labelClass" for="location">Địa điểm công trình</label>
                    <input id="location" v-model="form.meta.location" :placeholder="ph('Địa điểm công trình')" type="text" :class="fieldClass" />
                </div>
                <div>
                    <label :class="labelClass" for="area">Diện tích (m²)</label>
                    <input id="area" v-model="form.meta.area" :placeholder="ph('Diện tích (m²)')" type="text" :class="fieldClass" />
                </div>
                <div>
                    <label :class="labelClass" for="budget">Ngân sách dự kiến</label>
                    <input id="budget" v-model="form.meta.budget" type="text" :class="fieldClass" :placeholder="variant === 'template' ? 'Ngân sách dự kiến' : 'VD: 50 – 100 triệu'" />
                </div>
                <div>
                    <label :class="labelClass" for="timeline">Thời gian mong muốn</label>
                    <input id="timeline" v-model="form.meta.timeline" type="text" :class="fieldClass" :placeholder="variant === 'template' ? 'Thời gian mong muốn' : 'VD: Tháng 1/2027'" />
                </div>
            </template>
        </div>
        <div>
            <label :class="labelClass" :for="'message-' + type">{{ isSurvey ? 'Mô tả nhu cầu' : 'Nội dung' }}</label>
            <textarea :id="'message-' + type" v-model="form.message" :placeholder="ph('Mô tả nhu cầu / nội dung')" rows="4" :class="fieldClass" />
            <p v-if="form.errors.message" class="mt-1 text-xs text-red-600">{{ form.errors.message }}</p>
        </div>
        <p v-if="form.recentlySuccessful" class="rounded-sm bg-brand-50 px-4 py-3 text-sm text-brand-800">
            Cảm ơn bạn! Chúng tôi đã nhận yêu cầu và sẽ liên hệ trong thời gian sớm nhất.
        </p>
        <p v-if="form.errors.throttle" class="text-sm text-red-600">{{ form.errors.throttle }}</p>
        <button type="submit" class="btn-primary w-full sm:w-auto" :class="{ 'sm:float-right sm:w-44': variant === 'template' }" :disabled="form.processing">{{ form.processing ? 'Đang gửi…' : submitLabel }}</button>
    </form>
</template>
