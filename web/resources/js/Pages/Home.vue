<script setup>
import { Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { Autoplay, EffectFade, Navigation, Pagination } from 'swiper/modules';
import { Swiper, SwiperSlide } from 'swiper/vue';
import 'swiper/css/effect-fade';
import Icon from '@/Components/Icon.vue';
import Picture from '@/Components/Picture.vue';
import ProductCard from '@/Components/ProductCard.vue';
import QuoteCta from '@/Components/QuoteCta.vue';
import QuoteForm from '@/Components/QuoteForm.vue';
import SectionTitle from '@/Components/SectionTitle.vue';
import SeoHead from '@/Components/SeoHead.vue';
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

const mainServices = computed(() => props.services.slice(0, 2));
const otherServices = computed(() => props.services.slice(2));

const projectFilter = ref(null);
const projectTypes = computed(() => {
    const seen = new Map();
    props.featuredProjects.forEach((project) => project.type && seen.set(project.type.slug, project.type.name));
    return [...seen].map(([slug, name]) => ({ slug, name }));
});
const visibleProjects = computed(() =>
    projectFilter.value ? props.featuredProjects.filter((project) => project.type?.slug === projectFilter.value) : props.featuredProjects,
);

const reasons = [
    { icon: 'sparkles', title: 'Hơn 10 năm kinh nghiệm', text: 'Đội ngũ kỹ sư cảnh quan và kỹ thuật viên lành nghề, am hiểu từng loại cây và khí hậu địa phương.' },
    { icon: 'leaf', title: 'Cây khỏe, tuyển chọn kỹ', text: 'Nguồn cây từ vườn ươm riêng và đối tác, được dưỡng thuần trước khi giao tới công trình.' },
    { icon: 'shield', title: 'Bảo hành – thay cây miễn phí', text: 'Trong gói thuê và chăm sóc, cây kém chất lượng được thay mới nhanh chóng, không phát sinh chi phí.' },
    { icon: 'users', title: 'Tư vấn tận tâm, minh bạch', text: 'Khảo sát miễn phí, đề xuất phương án theo ánh sáng, phong thủy và ngân sách, báo giá rõ ràng.' },
    { icon: 'clock', title: 'Chăm sóc định kỳ có báo cáo', text: 'Lịch tưới, bón, cắt tỉa, phòng bệnh cụ thể; mỗi lượt chăm sóc đều có ảnh báo cáo.' },
    { icon: 'truck', title: 'Thi công đúng tiến độ', text: 'Vận chuyển, trồng và hoàn thiện tại công trình, bàn giao kèm hướng dẫn chăm sóc chi tiết.' },
];

const awards = computed(() => [
    { title: 'Đối tác cây xanh của hơn ' + (props.stats?.projects ?? 300) + ' công trình', text: 'Từ văn phòng, biệt thự tới khu đô thị và resort – mỗi công trình là một cam kết về chất lượng và tiến độ.', year: '2014 – nay' },
    { title: 'Vườn ươm và đội ngũ kỹ thuật riêng', text: 'Chủ động nguồn cây, kiểm soát chất lượng từ khâu ươm tới khi cây được trồng và chăm sóc tại công trình.', year: 'Củ Chi – TP.HCM' },
]);
const award = ref(0);
</script>

<template>
    <SeoHead :seo="seo" />

    <!-- ===== Hero slider ===== -->
    <section class="relative bg-ink">
        <Swiper v-if="slides.length" :modules="[Autoplay, EffectFade, Navigation]" effect="fade" :loop="slides.length > 1"
            :autoplay="{ delay: 6000, disableOnInteraction: false }" :navigation="{ prevEl: '.hero-prev', nextEl: '.hero-next' }"
            class="h-[520px] md:h-[700px]">
            <SwiperSlide v-for="(slide, index) in slides" :key="slide.id">
                <div class="relative size-full">
                    <Picture :image="slide.image" :alt="slide.title" size="large" sizes="100vw" :eager="index === 0" />
                    <div class="absolute inset-0 bg-black/35" />
                    <div class="container-site absolute inset-0 flex items-center justify-center pt-10 text-center">
                        <div class="max-w-4xl text-white">
                            <h2 class="font-serif text-4xl leading-[1.15] font-bold text-white md:text-[64px]">{{ slide.title }}</h2>
                            <p v-if="slide.subtitle" class="mt-6 text-lg">{{ slide.subtitle }}</p>
                            <div class="mt-12 flex flex-wrap justify-center gap-6">
                                <Link v-if="slide.buttonUrl" :href="slide.buttonUrl" class="btn-primary w-44">{{ slide.buttonText || 'Liên hệ' }}</Link>
                                <Link href="/dich-vu" class="btn-white w-44">Xem dịch vụ</Link>
                            </div>
                        </div>
                    </div>
                </div>
            </SwiperSlide>
        </Swiper>
        <template v-if="slides.length > 1">
            <button type="button" class="hero-prev absolute top-1/2 left-6 z-10 hidden size-[60px] -translate-y-1/2 place-items-center border border-dashed border-white/80 text-white hover:bg-brand-500 md:grid" aria-label="Ảnh trước">
                <Icon name="chevronRight" class="size-4 rotate-180" />
            </button>
            <button type="button" class="hero-next absolute top-1/2 right-6 z-10 hidden size-[60px] -translate-y-1/2 place-items-center border border-dashed border-white/80 text-white hover:bg-brand-500 md:grid" aria-label="Ảnh sau">
                <Icon name="chevronRight" class="size-4" />
            </button>
        </template>
        <div v-if="!slides.length" class="relative h-[520px] bg-[url('/images/hero-fallback.jpg')] bg-cover bg-center md:h-[700px]">
            <div class="absolute inset-0 bg-black/40" />
            <div class="container-site relative flex h-full items-center justify-center text-center">
                <h1 class="font-serif text-4xl font-bold text-white md:text-[64px]">Mang mảng xanh vào mọi công trình</h1>
            </div>
        </div>
    </section>

    <!-- ===== Welcome + services ===== -->
    <section class="container-site pt-20 md:pt-[90px]">
        <div class="grid gap-12 lg:grid-cols-2">
            <div>
                <h1 class="text-[34px] leading-tight font-bold md:text-[48px]">
                    Chào mừng đến <span class="title-leaf">{{ $page.props.site.site_name }}</span><br />Cây xanh &amp; cảnh quan
                </h1>
                <p class="mt-10 text-base leading-[30px] text-muted">
                    Chúng tôi tư vấn, thiết kế, cung cấp và thi công cây xanh cho văn phòng, biệt thự, khu đô thị, trường học, resort;
                    đồng thời cho thuê cây văn phòng – sự kiện và nhận chăm sóc cây xanh định kỳ với lịch rõ ràng, có báo cáo sau mỗi lượt.
                </p>
                <p class="mt-7 text-base leading-[30px] text-muted">
                    Bên cạnh đó là bộ sưu tập cây cảnh nghệ thuật độc bản – tùng, sanh, mai, linh sam… được nghệ nhân chăm sóc và tạo dáng nhiều năm,
                    sẵn sàng cho những không gian cần một điểm nhấn đặc biệt.
                </p>
                <Link href="/dich-vu" class="link-more mt-8">Xem tất cả dịch vụ <Icon name="chevronRight" class="size-3.5" /></Link>
            </div>
            <div class="grid gap-8 sm:grid-cols-2">
                <article v-for="service in mainServices" :key="service.id">
                    <Link :href="service.url" class="block aspect-[270/235] overflow-hidden bg-paper">
                        <Picture :image="service.image" :alt="service.name" img-class="size-full object-cover transition duration-500 hover:scale-105" />
                    </Link>
                    <h3 class="mt-6 text-2xl"><Link :href="service.url" class="hover:text-brand-500">{{ service.name }}</Link></h3>
                    <p class="mt-3 line-clamp-6 text-sm leading-6 text-muted">{{ service.summary }}</p>
                    <Link :href="service.url" class="link-more mt-5">Xem thêm <Icon name="chevronRight" class="size-3.5" /></Link>
                </article>
            </div>
        </div>
        <div v-if="otherServices.length" class="mt-16 grid gap-10 md:grid-cols-2 xl:grid-cols-3">
            <article v-for="service in otherServices" :key="service.id" class="flex gap-6">
                <Link :href="service.url" class="block h-[118px] w-[150px] shrink-0 overflow-hidden bg-paper">
                    <Picture :image="service.image" :alt="service.name" size="thumb" sizes="150px" />
                </Link>
                <div>
                    <h3 class="text-xl"><Link :href="service.url" class="hover:text-brand-500">{{ service.name }}</Link></h3>
                    <p class="mt-2 line-clamp-3 text-sm leading-6 text-muted">{{ service.summary }}</p>
                    <Link :href="service.url" class="link-more mt-2">Xem thêm <Icon name="chevronRight" class="size-3.5" /></Link>
                </div>
            </article>
        </div>
        <hr class="mt-16 border-gray-100" />

        <!-- Contained CTA band -->
        <div class="relative mt-14 mb-20 bg-[url('/images/theme/8.jpg')] bg-cover bg-center p-4 md:mb-[80px]">
            <div class="absolute inset-0 bg-brand-500/75" aria-hidden="true" />
            <div class="relative flex flex-col items-center justify-between gap-6 border border-dashed border-white/60 px-6 py-6 text-center md:flex-row md:px-6 md:text-left">
                <h2 class="font-serif text-2xl font-bold text-white md:text-[30px]">{{ $page.props.site.site_name }} – giải pháp cây xanh trọn gói cho công trình.</h2>
                <Link href="/bao-gia" class="btn-outline w-56 shrink-0">Khảo sát miễn phí</Link>
            </div>
        </div>
    </section>

    <!-- ===== Project gallery (dark) ===== -->
    <section v-if="featuredProjects.length" class="bg-[#1e1e1e] bg-[url('/images/theme/p-bg.jpg')] bg-center py-20 md:py-[80px]">
        <div class="container-site">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <SectionTitle title="Dự án" accent="tiêu biểu" small light />
                <ul v-if="projectTypes.length > 1" class="flex flex-wrap gap-x-9 gap-y-2 text-base font-bold">
                    <li><button type="button" :class="projectFilter === null ? 'text-brand-500' : 'text-white hover:text-brand-500'" @click="projectFilter = null">Tất cả dự án</button></li>
                    <li v-for="type in projectTypes" :key="type.slug">
                        <button type="button" :class="projectFilter === type.slug ? 'text-brand-500' : 'text-white hover:text-brand-500'" @click="projectFilter = type.slug">{{ type.name }}</button>
                    </li>
                </ul>
            </div>
            <div class="mt-14 grid gap-[30px] sm:grid-cols-2 lg:grid-cols-3">
                <Link v-for="project in visibleProjects" :key="project.id" :href="project.url" class="group relative block aspect-[370/260] overflow-hidden">
                    <Picture :image="project.image" :alt="project.name" />
                    <div class="absolute inset-3 flex flex-col items-center justify-center border border-dashed border-white/70 bg-brand-500/85 p-4 text-center text-white opacity-0 transition duration-300 group-hover:opacity-100">
                        <h3 class="font-serif text-xl font-bold text-white">{{ project.name }}</h3>
                        <p v-if="project.location" class="mt-2 text-sm">{{ project.location }}</p>
                    </div>
                </Link>
            </div>
            <div class="mt-12 text-center"><Link href="/du-an" class="btn-primary w-60">Xem tất cả dự án</Link></div>
        </div>
    </section>

    <!-- ===== Art trees ===== -->
    <section v-if="featuredArt.length" class="py-20 md:py-[90px]">
        <div class="container-site">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <SectionTitle title="Cây cảnh" accent="nghệ thuật" />
                <Link href="/cay-canh-nghe-thuat" class="link-more text-brand-500">Xem bộ sưu tập <Icon name="chevronRight" class="size-3.5" /></Link>
            </div>
            <Swiper :modules="[Navigation, Pagination]" :space-between="30" :slides-per-view="1.2" :pagination="{ clickable: true }"
                :breakpoints="{ 640: { slidesPerView: 2.2 }, 1024: { slidesPerView: 4 } }" class="mt-12 !pb-12">
                <SwiperSlide v-for="product in featuredArt" :key="product.id" class="!h-auto">
                    <ProductCard :product="product" class="h-full" />
                </SwiperSlide>
            </Swiper>
        </div>
    </section>

    <!-- ===== Why choose us ===== -->
    <section class="border-t border-gray-100 py-20 md:py-[90px]">
        <div class="container-site">
            <SectionTitle title="Vì sao chọn" :accent="$page.props.site.site_name" center />
            <div class="mt-16 grid gap-x-[70px] gap-y-10 lg:grid-cols-2">
                <div v-for="reason in reasons" :key="reason.title" class="relative ml-10 border border-dashed border-[#e6e6e6] py-6 pr-6 pl-[62px]">
                    <span class="absolute top-1/2 -left-10 grid size-20 -translate-y-1/2 place-items-center rounded-full bg-brand-500 text-white">
                        <span class="grid size-[68px] place-items-center rounded-full border border-dashed border-white/80"><Icon :name="reason.icon" class="size-7" /></span>
                    </span>
                    <h3 class="text-2xl">{{ reason.title }}</h3>
                    <p class="mt-2 text-sm leading-6 text-muted">{{ reason.text }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== Award + quote form ===== -->
    <section class="grid border-t border-gray-100 lg:grid-cols-2">
        <div class="relative bg-[url('/images/theme/15.jpg')] bg-cover bg-center px-6 py-20 lg:py-[90px]">
            <div class="absolute inset-0 bg-black/75" aria-hidden="true" />
            <div class="relative ml-auto max-w-[555px] lg:pr-14">
                <h2 class="text-[34px] leading-tight text-white md:text-[42px]">Đơn vị cây xanh <span class="title-leaf">uy tín</span><br />tại TP. Hồ Chí Minh</h2>
                <div class="mt-12">
                    <img src="/images/theme/badge.png" alt="" class="h-[126px]" loading="lazy" />
                    <h3 class="mt-6 text-[26px] leading-snug text-white">{{ awards[award].title }}</h3>
                    <p class="mt-4 text-sm leading-6 text-white/90">{{ awards[award].text }}</p>
                    <p class="mt-5 font-serif text-2xl font-bold text-white"><span class="text-brand-500">Từ :</span> {{ awards[award].year }}</p>
                    <div class="mt-8 flex gap-3">
                        <button v-for="(item, index) in awards" :key="index" type="button" class="size-3.5 rounded-full border border-dashed border-white"
                            :class="{ 'border-solid !border-brand-500 bg-brand-500': award === index }" :aria-label="'Mục ' + (index + 1)" @click="award = index" />
                    </div>
                </div>
            </div>
        </div>
        <div class="px-6 py-20 lg:py-[90px] lg:pl-16">
            <div class="max-w-[570px]">
                <SectionTitle title="Bắt đầu" accent="dự án" after="của bạn" />
                <QuoteForm class="mt-10" type="survey" variant="template" submit-label="Gửi yêu cầu" />
            </div>
        </div>
    </section>

    <!-- ===== Products ===== -->
    <section v-if="products.length" class="bg-paper py-20 md:py-[90px]">
        <div class="container-site">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <SectionTitle title="Mua cây" accent="online" />
                <Link href="/san-pham" class="link-more text-brand-500">Vào cửa hàng <Icon name="chevronRight" class="size-3.5" /></Link>
            </div>
            <div class="mt-12 grid grid-cols-2 gap-4 md:gap-[30px] lg:grid-cols-4">
                <ProductCard v-for="product in products" :key="product.id" :product="product" />
            </div>
        </div>
    </section>

    <!-- ===== News ===== -->
    <section v-if="posts.length" class="py-20 md:py-[90px]">
        <div class="container-site">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <SectionTitle title="Kiến thức" accent="cây xanh" />
                <Link href="/tin-tuc" class="link-more text-brand-500">Xem thêm tin tức <Icon name="chevronRight" class="size-3.5" /></Link>
            </div>
            <div class="mt-12 grid gap-[30px] md:grid-cols-3">
                <article v-for="post in posts" :key="post.id">
                    <Link :href="post.url" class="block aspect-[370/220] overflow-hidden bg-paper">
                        <Picture :image="post.image" :alt="post.title" img-class="size-full object-cover transition duration-500 hover:scale-105" />
                    </Link>
                    <h3 class="mt-6 text-2xl leading-snug"><Link :href="post.url" class="hover:text-brand-500">{{ post.title }}</Link></h3>
                    <ul class="mt-4 flex flex-wrap gap-x-6 bg-paper px-3 py-1.5 font-serif text-sm text-[#6b6b6b] italic">
                        <li v-if="post.date">{{ post.date }}</li>
                        <li v-if="post.category">{{ post.category.name }}</li>
                    </ul>
                    <p class="mt-4 line-clamp-4 text-sm leading-6 text-muted">{{ post.excerpt }}</p>
                </article>
            </div>
        </div>
    </section>

    <!-- ===== Parallax stats ===== -->
    <section class="relative bg-[url('/images/theme/19.jpg')] bg-cover bg-fixed bg-center py-20 text-center md:py-[90px]">
        <div class="absolute inset-0 bg-[#355a04]/80" aria-hidden="true" />
        <div class="container-site relative">
            <h2 class="text-[34px] text-white md:text-[42px]">Hơn <span class="text-[#e6ee28]">{{ stats?.projects ?? 300 }}+</span> công trình xanh đã bàn giao</h2>
            <p class="mx-auto mt-6 max-w-3xl text-base leading-7 text-white">
                {{ stats?.years ?? 10 }} năm đồng hành cùng chủ đầu tư, doanh nghiệp và gia đình; hiện chăm sóc định kỳ hơn
                {{ Number(stats?.trees ?? 5000).toLocaleString('vi-VN') }} cây xanh tại văn phòng, biệt thự và khu đô thị.
            </p>
            <p class="mt-8 font-serif text-2xl font-bold text-white">Bắt đầu dự án của bạn hôm nay – <Link href="/lien-he" class="text-[#e6ee28] underline">Liên hệ ngay</Link></p>
        </div>
    </section>

    <!-- ===== Testimonials ===== -->
    <section v-if="testimonials.length" class="py-20 md:py-[90px]">
        <div class="container-site">
            <div class="flex flex-col gap-6 md:flex-row md:items-center">
                <SectionTitle title="nói gì" accent="Khách hàng" accent-first small />
                <p class="max-w-md font-serif text-lg leading-[34px] text-muted md:ml-12">Cảm nhận của khách hàng về dịch vụ cây xanh của chúng tôi.</p>
                <div class="flex gap-5 md:ml-auto">
                    <button type="button" class="t-prev text-gray-300 hover:text-brand-500" aria-label="Trước"><Icon name="chevronRight" class="size-6 rotate-180" /></button>
                    <button type="button" class="t-next text-gray-300 hover:text-brand-500" aria-label="Sau"><Icon name="chevronRight" class="size-6" /></button>
                </div>
            </div>
            <Swiper :modules="[Autoplay, Navigation]" :space-between="30" :slides-per-view="1" :autoplay="{ delay: 7000 }"
                :navigation="{ prevEl: '.t-prev', nextEl: '.t-next' }" :breakpoints="{ 768: { slidesPerView: 2 } }" class="mt-12">
                <SwiperSlide v-for="item in testimonials" :key="item.id">
                    <figure class="flex gap-8">
                        <img src="/images/theme/qut.jpg" alt="" class="mt-1 h-[42px] w-[49px] shrink-0" loading="lazy" />
                        <div>
                            <blockquote class="text-base leading-[30px] text-muted">{{ item.content }}</blockquote>
                            <figcaption class="mt-6">
                                <span class="block font-serif text-2xl font-bold text-ink">{{ item.name }}</span>
                                <span v-if="item.role" class="block text-sm text-muted">{{ item.role }}</span>
                                <span class="mt-2 flex text-brand-500"><Icon v-for="n in item.rating" :key="n" name="star" class="size-5 fill-current" /></span>
                            </figcaption>
                        </div>
                    </figure>
                </SwiperSlide>
            </Swiper>
        </div>
    </section>

    <QuoteCta title="Cần cây xanh cho công trình? Nhận báo giá chỉ trong 24 giờ." button="Nhận báo giá" />

    <section v-if="$page.props.site.map_embed_url" class="h-[300px]">
        <iframe :src="$page.props.site.map_embed_url" class="size-full border-0" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Bản đồ" />
    </section>
</template>
