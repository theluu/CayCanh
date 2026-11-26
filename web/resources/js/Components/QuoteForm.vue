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
                <label class="field-label" :for="'name-' + type">Họ tên *</label>
                <input :id="'name-' + type" v-model="form.name" type="text" class="field" autocomplete="name" required />
                <p v-if="form.errors.name" class="mt-1 text-xs text-red-600">{{ form.errors.name }}</p>
            </div>
            <div>
                <label class="field-label" :for="'phone-' + type">Số điện thoại *</label>
                <input :id="'phone-' + type" v-model="form.phone" type="tel" class="field" autocomplete="tel" inputmode="tel" required />
                <p v-if="form.errors.phone" class="mt-1 text-xs text-red-600">{{ form.errors.phone }}</p>
            </div>
            <div :class="isSurvey ? '' : 'sm:col-span-2'">
                <label class="field-label" :for="'email-' + type">Email</label>
                <input :id="'email-' + type" v-model="form.email" type="email" class="field" autocomplete="email" />
                <p v-if="form.errors.email" class="mt-1 text-xs text-red-600">{{ form.errors.email }}</p>
            </div>
            <template v-if="isSurvey">
                <div>
                    <label class="field-label" for="project-kind">Loại công trình</label>
                    <select id="project-kind" v-model="form.meta.project_kind" class="field">
                        <option value="">-- Chọn --</option>
                        <option v-for="kind in projectKinds" :key="kind">{{ kind }}</option>
                    </select>
                </div>
                <div>
                    <label class="field-label" for="location">Địa điểm công trình</label>
                    <input id="location" v-model="form.meta.location" type="text" class="field" />
                </div>
                <div>
                    <label class="field-label" for="area">Diện tích (m²)</label>
                    <input id="area" v-model="form.meta.area" type="text" class="field" />
                </div>
                <div>
                    <label class="field-label" for="budget">Ngân sách dự kiến</label>
                    <input id="budget" v-model="form.meta.budget" type="text" class="field" placeholder="VD: 50 – 100 triệu" />
                </div>
                <div>
                    <label class="field-label" for="timeline">Thời gian mong muốn</label>
                    <input id="timeline" v-model="form.meta.timeline" type="text" class="field" placeholder="VD: Tháng 1/2027" />
                </div>
            </template>
        </div>
        <div>
            <label class="field-label" :for="'message-' + type">{{ isSurvey ? 'Mô tả nhu cầu' : 'Nội dung' }}</label>
            <textarea :id="'message-' + type" v-model="form.message" rows="4" class="field" />
            <p v-if="form.errors.message" class="mt-1 text-xs text-red-600">{{ form.errors.message }}</p>
        </div>
        <p v-if="form.recentlySuccessful" class="rounded-sm bg-brand-50 px-4 py-3 text-sm text-brand-800">
            Cảm ơn bạn! Chúng tôi đã nhận yêu cầu và sẽ liên hệ trong thời gian sớm nhất.
        </p>
        <p v-if="form.errors.throttle" class="text-sm text-red-600">{{ form.errors.throttle }}</p>
        <button type="submit" class="btn-primary w-full sm:w-auto" :disabled="form.processing">{{ form.processing ? 'Đang gửi…' : submitLabel }}</button>
    </form>
</template>
