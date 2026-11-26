<script setup>
import { Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { Autoplay, EffectFade, Navigation, Pagination } from 'swiper/modules';
import { Swiper, SwiperSlide } from 'swiper/vue';
import 'swiper/css/effect-fade';
import Icon from '@/Components/Icon.vue';
import Picture from '@/Components/Picture.vue';
import PostCard from '@/Components/PostCard.vue';
import ProductCard from '@/Components/ProductCard.vue';
import ProjectCard from '@/Components/ProjectCard.vue';
import QuoteCta from '@/Components/QuoteCta.vue';
import SectionTitle from '@/Components/SectionTitle.vue';
import SeoHead from '@/Components/SeoHead.vue';
import ServiceCard from '@/Components/ServiceCard.vue';
import SiteLayout from '@/Layouts/SiteLayout.vue';

defineOptions({ layout: SiteLayout });

const props = defineProps({
    seo: Object,
    slides: Array,
    stats: Object,
    services: Array,
    featuredArt: Array,
    featuredProjects: Array,
    products: Array,
    testimonials: Array,
    posts: Array,
    partners: Array,
});

const projectFilter = ref(null);
const projectTypes = computed(() => {
    const seen = new Map();
    props.featuredProjects.forEach((project) => project.type && seen.set(project.type.slug, project.type.name));
    return [...seen].map(([slug, name]) => ({ slug, name }));
});
const visibleProjects = computed(() =>
    projectFilter.value ? props.featuredProjects.filter((project) => project.type?.slug === projectFilter.value) : props.featuredProjects,
);

const statItems = computed(() => [
    { value: props.stats?.years, label: 'Năm kinh nghiệm' },
    { value: props.stats?.projects, label: 'Công trình đã thực hiện' },
    { value: props.stats?.trees, label: 'Cây xanh đang chăm sóc' },
].filter((item) => item.value));

const reasons = [
    { icon: 'sparkles', title: 'Cây khỏe, tuyển chọn kỹ', text: 'Nguồn cây từ vườn ươm đối tác, được dưỡng và kiểm tra trước khi giao.' },
    { icon: 'users', title: 'Đội ngũ kỹ thuật lành nghề', text: 'Kỹ sư cảnh quan và kỹ thuật viên chăm sóc giàu kinh nghiệm.' },
    { icon: 'shield', title: 'Bảo hành – thay cây miễn phí', text: 'Cam kết thay cây trong gói thuê nếu cây kém chất lượng.' },
    { icon: 'truck', title: 'Giao & thi công tận nơi', text: 'Vận chuyển, trồng và hoàn thiện tại công trình, đúng tiến độ.' },
    { icon: 'clock', title: 'Lịch chăm sóc định kỳ', text: 'Tưới, bón, cắt tỉa, phòng bệnh theo lịch rõ ràng, có báo cáo.' },
    { icon: 'leaf', title: 'Tư vấn theo không gian', text: 'Chọn cây hợp ánh sáng, phong thủy và ngân sách của bạn.' },
];
</script>

<template>
    <SeoHead :seo="seo" />

    <!-- Hero -->
    <section class="relative bg-ink">
        <Swiper v-if="slides.length" :modules="[Autoplay, EffectFade, Pagination]" effect="fade" :loop="slides.length > 1"
            :autoplay="{ delay: 6000, disableOnInteraction: false }" :pagination="{ clickable: true }" class="h-[480px] md:h-[620px]">
            <SwiperSlide v-for="(slide, index) in slides" :key="slide.id">
                <div class="relative size-full">
                    <Picture :image="slide.image" :alt="slide.title" size="large" sizes="100vw" :eager="index === 0" />
                    <div class="absolute inset-0 bg-gradient-to-r from-black/70 via-black/35 to-transparent" />
                    <div class="container-site absolute inset-0 flex items-center">
                        <div class="max-w-2xl text-white">
                            <p class="mb-3 inline-flex items-center gap-2 text-sm font-semibold tracking-[0.25em] text-brand-300 uppercase"><Icon name="leaf" class="size-4" /> Cây xanh & cảnh quan</p>
                            <h2 class="font-serif text-4xl leading-tight font-bold text-white md:text-6xl">{{ slide.title }}</h2>
                            <p v-if="slide.subtitle" class="mt-4 text-lg text-gray-200">{{ slide.subtitle }}</p>
                            <div class="mt-8 flex flex-wrap gap-3">
                                <Link v-if="slide.buttonUrl" :href="slide.buttonUrl" class="btn-primary">{{ slide.buttonText || 'Xem thêm' }}</Link>
                                <Link href="/du-an" class="btn-outline">Dự án đã làm</Link>
                            </div>
                        </div>
                    </div>
                </div>
            </SwiperSlide>
        </Swiper>
        <div v-else class="relative h-[480px] bg-cover bg-center md:h-[560px]" style="background-image: url('/images/hero-fallback.jpg')">
            <div class="absolute inset-0 bg-black/50" />
            <div class="container-site relative flex h-full items-center">
                <div class="max-w-2xl text-white">
                    <h1 class="font-serif text-4xl font-bold text-white md:text-6xl">Mang mảng xanh vào mọi công trình</h1>
                    <Link href="/bao-gia" class="btn-primary mt-8">Nhận báo giá</Link>
                </div>
            </div>
        </div>
    </section>

    <!-- Intro + stats -->
    <section class="container-site grid gap-12 py-20 lg:grid-cols-2 lg:items-center">
        <div>
            <h1 class="text-3xl leading-tight font-bold md:text-4xl">
                Chào mừng tới <span class="text-brand-500">{{ $page.props.site.site_name }}</span><br />cây xanh công trình & cây cảnh nghệ thuật
            </h1>
            <p class="mt-5 leading-relaxed text-muted">
                Chúng tôi tư vấn, cung cấp, mua bán, cho thuê và chăm sóc cây xanh cho văn phòng, biệt thự, khu đô thị, resort…
                đồng thời sưu tầm những tác phẩm cây cảnh nghệ thuật độc bản cho người chơi cây.
            </p>
            <div class="mt-8 flex flex-wrap gap-3">
                <Link href="/dich-vu" class="btn-primary">Xem dịch vụ</Link>
                <Link href="/gioi-thieu" class="btn-ghost">Về chúng tôi</Link>
            </div>
        </div>
        <div class="grid grid-cols-3 gap-4">
            <div v-for="item in statItems" :key="item.label" class="border-b-4 border-brand-500 bg-paper p-6 text-center">
                <p class="font-serif text-3xl font-bold text-brand-600 md:text-4xl">{{ Number(item.value).toLocaleString('vi-VN') }}+</p>
                <p class="mt-2 text-sm text-muted">{{ item.label }}</p>
            </div>
        </div>
    </section>

    <!-- Services -->
    <section v-if="services.length" class="bg-paper py-20">
        <div class="container-site">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <SectionTitle title="Dịch vụ" accent="của chúng tôi" subtitle="Trọn gói từ tư vấn, cung cấp tới cho thuê và chăm sóc cây xanh công trình." />
                <Link href="/dich-vu" class="inline-flex items-center gap-1 text-sm font-semibold text-brand-600 uppercase hover:text-brand-700">Tất cả dịch vụ <Icon name="arrowRight" class="size-4" /></Link>
            </div>
            <div class="mt-10 grid gap-8 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
                <ServiceCard v-for="service in services" :key="service.id" :service="service" />
            </div>
        </div>
    </section>

    <QuoteCta title="Cần cây xanh cho công trình? Khảo sát & báo giá miễn phí." />

    <!-- Featured art trees -->
    <section v-if="featuredArt.length" class="py-20">
        <div class="container-site">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <SectionTitle title="Cây cảnh" accent="nghệ thuật" subtitle="Mỗi tác phẩm là độc bản – liên hệ để xem cây trực tiếp và nhận báo giá." />
                <Link href="/cay-canh-nghe-thuat" class="inline-flex items-center gap-1 text-sm font-semibold text-brand-600 uppercase hover:text-brand-700">Xem bộ sưu tập <Icon name="arrowRight" class="size-4" /></Link>
            </div>
            <Swiper :modules="[Navigation, Pagination]" :space-between="24" :slides-per-view="1.2" navigation :pagination="{ clickable: true }"
                :breakpoints="{ 640: { slidesPerView: 2.2 }, 1024: { slidesPerView: 4 } }" class="mt-10 !pb-12">
                <SwiperSlide v-for="product in featuredArt" :key="product.id" class="!h-auto">
                    <ProductCard :product="product" class="h-full" />
                </SwiperSlide>
            </Swiper>
        </div>
    </section>

    <!-- Projects -->
    <section v-if="featuredProjects.length" class="bg-paper py-20">
        <div class="container-site">
            <div class="flex flex-wrap items-end justify-between gap-6">
                <SectionTitle title="Dự án" accent="tiêu biểu" />
                <div v-if="projectTypes.length > 1" class="flex flex-wrap gap-2">
                    <button type="button" class="rounded-sm px-4 py-2 text-sm font-medium" :class="projectFilter === null ? 'bg-brand-500 text-white' : 'bg-white text-ink hover:text-brand-600'" @click="projectFilter = null">Tất cả</button>
                    <button v-for="type in projectTypes" :key="type.slug" type="button" class="rounded-sm px-4 py-2 text-sm font-medium"
                        :class="projectFilter === type.slug ? 'bg-brand-500 text-white' : 'bg-white text-ink hover:text-brand-600'" @click="projectFilter = type.slug">{{ type.name }}</button>
                </div>
            </div>
            <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <ProjectCard v-for="project in visibleProjects" :key="project.id" :project="project" />
            </div>
            <div class="mt-10 text-center"><Link href="/du-an" class="btn-ghost">Xem tất cả dự án</Link></div>
        </div>
    </section>

    <!-- Products -->
    <section v-if="products.length" class="py-20">
        <div class="container-site">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <SectionTitle title="Mua cây" accent="online" subtitle="Cây nội thất, cây văn phòng, chậu và vật tư – đặt hàng nhanh, giao tận nơi." />
                <Link href="/san-pham" class="inline-flex items-center gap-1 text-sm font-semibold text-brand-600 uppercase hover:text-brand-700">Cửa hàng <Icon name="arrowRight" class="size-4" /></Link>
            </div>
            <div class="mt-10 grid grid-cols-2 gap-4 md:gap-6 lg:grid-cols-4">
                <ProductCard v-for="product in products" :key="product.id" :product="product" />
            </div>
        </div>
    </section>

    <!-- Why us -->
    <section class="relative bg-ink py-20">
        <div class="container-site">
            <SectionTitle title="Vì sao chọn" accent="chúng tôi" light center />
            <div class="mt-12 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                <div v-for="reason in reasons" :key="reason.title" class="flex gap-4">
                    <span class="grid size-14 shrink-0 place-items-center rounded-full bg-brand-500 text-white"><Icon :name="reason.icon" class="size-6" /></span>
                    <div>
                        <h3 class="text-lg font-bold text-white">{{ reason.title }}</h3>
                        <p class="mt-1.5 text-sm leading-relaxed text-gray-400">{{ reason.text }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section v-if="testimonials.length" class="py-20">
        <div class="container-site">
            <SectionTitle title="Khách hàng" accent="nói gì" center />
            <Swiper :modules="[Autoplay, Pagination]" :space-between="24" :slides-per-view="1" :autoplay="{ delay: 7000 }" :pagination="{ clickable: true }"
                :breakpoints="{ 768: { slidesPerView: 2 } }" class="mt-10 !pb-12">
                <SwiperSlide v-for="item in testimonials" :key="item.id" class="!h-auto">
                    <figure class="flex h-full flex-col border border-gray-100 bg-paper p-8">
                        <Icon name="quote" class="size-8 text-brand-500" />
                        <blockquote class="mt-4 flex-1 leading-relaxed text-muted italic">“{{ item.content }}”</blockquote>
                        <figcaption class="mt-6 flex items-center gap-4">
                            <span class="size-12 overflow-hidden rounded-full bg-brand-100"><Picture :image="item.avatar" :alt="item.name" size="thumb" /></span>
                            <span>
                                <span class="block font-serif font-bold">{{ item.name }}</span>
                                <span v-if="item.role" class="block text-sm text-muted">{{ item.role }}</span>
                                <span class="mt-1 flex text-amber-400"><Icon v-for="n in item.rating" :key="n" name="star" class="size-4 fill-current" /></span>
                            </span>
                        </figcaption>
                    </figure>
                </SwiperSlide>
            </Swiper>
        </div>
    </section>

    <!-- Posts -->
    <section v-if="posts.length" class="bg-paper py-20">
        <div class="container-site">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <SectionTitle title="Tin tức &" accent="kiến thức" />
                <Link href="/tin-tuc" class="inline-flex items-center gap-1 text-sm font-semibold text-brand-600 uppercase hover:text-brand-700">Xem tất cả <Icon name="arrowRight" class="size-4" /></Link>
            </div>
            <div class="mt-10 grid gap-8 md:grid-cols-3">
                <PostCard v-for="post in posts" :key="post.id" :post="post" />
            </div>
        </div>
    </section>

    <!-- Partners -->
    <section v-if="partners.length" class="border-t border-gray-100 py-12">
        <div class="container-site flex flex-wrap items-center justify-center gap-10 opacity-70">
            <a v-for="partner in partners" :key="partner.name" :href="partner.url || null" target="_blank" rel="noopener" class="h-12 w-32 grayscale hover:grayscale-0">
                <Picture :image="partner.logo" :alt="partner.name" size="thumb" img-class="size-full object-contain" />
            </a>
        </div>
    </section>

    <QuoteCta />
</template>
