# Website Cây Cảnh – Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Website quảng bá + bán hàng cho doanh nghiệp cây xanh công trình và cây cảnh nghệ thuật, có admin Filament, lead/đơn hàng có thông báo Telegram/Email, lịch chăm sóc nội bộ.

**Architecture:** Một app Laravel 13 duy nhất ở `web/`. Frontend công khai là Inertia + Vue 3 (SSR) + Tailwind v4. Admin Filament v5 ở `/admin`. Logic nghiệp vụ nằm trong `app/Actions` và `app/Services`. Controller mỏng. Notification chạy qua queue Redis.

**Tech Stack:** PHP 8.4, Laravel 13, Filament 5, Inertia 2 + Vue 3 + Vite, Tailwind 4, Swiper, GLightbox, MySQL 8.4, Redis, Pest, spatie/laravel-permission, spatie/laravel-medialibrary, spatie/laravel-settings, spatie/laravel-sitemap, spatie/laravel-backup, laravel-notification-channels/telegram.

**Spec:** `docs/superpowers/specs/2026-11-26-caycanh-website-design.md`

## Global Constraints

- Mọi chữ hiển thị là tiếng Việt. Tiền VND, format `1.250.000 ₫` qua helper `App\Support\Money::vnd(int $amount): string`. Giá lưu dạng `unsigned int` (đồng).
- Slug tiếng Việt không dấu: `Str::slug($name)` (Laravel đã chuyển đ→d). Slug unique theo bảng.
- Không có thanh toán online, không có đăng nhập cho khách. Chỉ `/admin` có auth.
- Lưu DB **trước**, thông báo sau qua queue. Lỗi thông báo không bao giờ làm request của khách thất bại.
- Server luôn tính lại giá từ DB khi đặt hàng. Không tin giá từ client.
- Sản phẩm `kind=art` không bao giờ vào giỏ hàng.
- Test chạy trên SQLite in-memory (`phpunit.xml`). Dev và prod dùng MySQL.
- Ảnh: conversions `thumb` 400, `medium` 800, `large` 1600, định dạng WebP, `->queued()`.
- Route công khai dùng đúng các path ở spec mục 5.9.

## Review Focus

1. Cây nghệ thuật bị POST thẳng vào `/gio-hang` bằng product_id → phải trả 422, giỏ không đổi (test ở Task 8).
2. Sản phẩm bị ẩn/unpublish hoặc đổi giá sau khi đã nằm trong giỏ → checkout dùng giá mới, bỏ sản phẩm đã ẩn và báo cho khách (test ở Task 8).
3. Telegram/SMTP lỗi hoặc chưa cấu hình → inquiry/order vẫn tạo được, khách thấy trang thành công (test ở Task 10).
4. Gửi form spam (honeypot có giá trị, hoặc quá 5 lần/phút/IP) → không tạo bản ghi (test ở Task 9).
5. SĐT Việt Nam nhập kiểu `0912 345 678`, `+84912345678` → được chấp nhận và chuẩn hoá thành `0912345678`. Chuỗi rác bị từ chối (test ở Task 9, rule `App\Rules\VietnamesePhone`).

---

### Task 1: Khung dự án

**Files:** `web/` (laravel new), `web/phpunit.xml`, `web/.env.example`, `web/vite.config.js`, `web/resources/js/app.js`, `web/resources/js/ssr.js`, `web/resources/views/app.blade.php`, `web/resources/css/app.css`, `web/tests/Feature/SmokeTest.php`, `.gitignore` (root)

- [ ] `git init` ở root. `composer create-project laravel/laravel web`, cài Pest.
- [ ] Cài `inertiajs/inertia-laravel`, `@inertiajs/vue3 vue @vitejs/plugin-vue tailwindcss @tailwindcss/vite swiper glightbox`. Middleware `HandleInertiaRequests`. Bật SSR (`resources/js/ssr.js`, `vite build --ssr`).
- [ ] `.env.example`: `DB_CONNECTION=mysql`, `CACHE_STORE=redis`, `SESSION_DRIVER=redis`, `QUEUE_CONNECTION=redis`, `APP_LOCALE=vi`, `APP_TIMEZONE=Asia/Ho_Chi_Minh`. `phpunit.xml` dùng sqlite `:memory:`, cache/session/queue `array`/`sync`.
- [ ] Test `it renders home via inertia`: `get('/')->assertOk()->assertInertia(fn($p)=>$p->component('Home'))`.
- [ ] `php artisan test` → PASS. `npm run build` → OK. Commit.

### Task 2: Admin, vai trò, cài đặt chung

**Files:** `app/Providers/Filament/AdminPanelProvider.php`, `app/Models/User.php`, `app/Enums/Role.php`, `database/seeders/RoleSeeder.php`, `app/Settings/GeneralSettings.php`, `app/Filament/Pages/ManageGeneralSettings.php`, `app/Filament/Resources/UserResource`, `tests/Feature/Admin/RoleAccessTest.php`, `tests/Feature/SettingsTest.php`

**Interfaces – Produces:**
- `enum Role: string { Admin='quan-tri-vien'; Editor='bien-tap-vien'; Sales='kinh-doanh'; Technician='ky-thuat-vien'; }` + `label(): string`.
- `User::canAccessPanel()` = có bất kỳ role nào.
- `GeneralSettings` (spatie/laravel-settings): `site_name, hotline, email, address, working_hours, map_embed_url, zalo_phone, facebook_url, messenger_url, tiktok_url, youtube_url, notify_emails: array, telegram_chat_id: ?string, zalo_oa_enabled: bool, stats: array` (năm kinh nghiệm, số công trình, số cây đang chăm sóc).
- Inertia shared prop `site` = settings công khai (không có notify_emails/telegram_chat_id).

- [ ] Test: user không có role → `/admin` 403. User Editor → 200. Chỉ Admin vào được `UserResource` và trang settings.
- [ ] Test: `site.hotline` có trong Inertia props của `/`, còn `telegram_chat_id` thì không.
- [ ] Cài Filament 5, spatie permission, settings. Filament UI tiếng Việt. Seeder tạo 4 role + user admin từ `ADMIN_EMAIL`/`ADMIN_PASSWORD` trong env.
- [ ] Test PASS. Commit.

### Task 3: Danh mục, sản phẩm, cây nghệ thuật (model + admin)

**Files:** `app/Models/{Category,Product}.php`, `app/Enums/{ProductKind,ArtAvailability}.php`, migrations, `app/Models/Concerns/HasSeo.php`, `app/Models/Concerns/HasSlug.php`, `app/Filament/Resources/{CategoryResource,ProductResource,ArtTreeResource}`, `tests/Feature/Catalog/ProductTest.php`

**Interfaces – Produces:**
- `ProductKind: standard|art`. `ArtAvailability: available|reserved|sold` + `label()`, `color()`.
- `products`: `category_id, kind, name, slug, sku?, price?:uint, sale_price?:uint, show_price:bool(default true cho standard, false cho art), availability, in_stock:bool, summary, description(longText), specs:json, video_url?, is_featured, is_published, published_at, reserved_inquiry_id?, seo_title?, seo_description?`.
- `Product::scopePublished()`, `scopeStandard()`, `scopeArt()`, `isPurchasable(): bool` (= standard && published && in_stock && price>0), `effectivePrice(): ?int`, `displayPrice(): string` ("Giá liên hệ" khi không hiện giá).
- Media collection `gallery` (nhiều ảnh) + conversions theo Global Constraints. `HasSeo::seoTitle()`, `seoDescription()` có fallback.
- `HasSlug`: tự tạo slug unique khi saving. Đổi slug thì ghi `redirects` (Task 13 tạo bảng; ở task này tạo luôn migration `redirects(from_path unique, to_path)` và model `Redirect`).
- `ProductResource` lọc kind=standard. `ArtTreeResource` lọc kind=art, có cột trạng thái và các action "Đặt giữ", "Đánh dấu đã bán", "Trả về còn hàng".

- [ ] Test: `isPurchasable` false với art, false khi unpublished, false khi price null. `displayPrice()` = `'Giá liên hệ'` cho art có show_price=false, = `'1.250.000 ₫'` khi price=1250000. Đổi slug tạo bản ghi Redirect `/san-pham/cu` → `/san-pham/moi`.
- [ ] Implement. Test PASS. Commit.

### Task 4: Dịch vụ, gói giá, dự án, tin tức, nội dung phụ (model + admin)

**Files:** `app/Models/{Service,ServicePackage,Project,ProjectType,Post,PostCategory,Tag,Testimonial,Faq,Slide,Partner,Page}.php`, migrations, Filament resources tương ứng, `tests/Feature/Content/ContentModelsTest.php`

**Interfaces – Produces:**
- `services`: `name, slug, icon?, summary, body, sort, is_published, seo_*`, media `cover`, `gallery`. Có quan hệ `packages()`, `faqs()` (faqs.service_id nullable).
- `service_packages`: `service_id, name, price_from?:uint, unit` (ví dụ "/tháng"), `features:json(list string), is_highlighted, sort`.
- `projects`: `project_type_id, name, slug, client?, location, year?, scale?, summary, body, video_url?, is_featured, is_published, seo_*`, media `cover`,`gallery`, `services()` n-n.
- `posts`: `post_category_id, title, slug, excerpt, body, is_published, published_at, seo_*`, media `cover`, `tags()` n-n.
- `testimonials(name, role?, content, rating 1-5, avatar media, sort)`, `faqs(question, answer, service_id?, sort)`, `slides(title, subtitle, button_text, button_url, sort, is_active, media image)`, `partners(name, url?, logo media)`, `pages(key unique: about, body, seo_*)`.
- Quyền resource: Editor và Admin quản lý tất cả. Sales, Technician không thấy.

- [ ] Test: `Service::published()->ordered()` sắp theo sort. Post chưa tới `published_at` không nằm trong `published()`. Tech không truy cập được `/admin/projects`.
- [ ] Implement. Test PASS. Commit.

### Task 5: Layout frontend + trang chủ

**Files:** `resources/css/app.css` (theme), `resources/js/Layouts/SiteLayout.vue`, `resources/js/Components/{SiteHeader,SiteFooter,FloatingContact,SeoHead,PageBanner,Breadcrumbs,SectionTitle,ProductCard,ProjectCard,PostCard,ServiceCard,QuoteCta,Lightbox}.vue`, `resources/js/Pages/Home.vue`, `app/Http/Controllers/HomeController.php`, `app/Support/Seo.php`, `tests/Feature/Pages/HomeTest.php`

**Interfaces – Produces:**
- `App\Support\Seo` (value object): `title, description, canonical, image?, jsonLd: array`. Controller truyền prop `seo`. `SeoHead.vue` render `<Head>` + `<script type="application/ld+json">`.
- Shared props: `site`, `menu.services` (name, slug), `cart.count`, `flash`.
- Theme Tailwind: `--color-brand-*` (xanh lá theo mẫu Gardener, gốc `#6fb83f`, đậm `#3d7a1c`), `--color-ink`, font **Be Vietnam Pro** (heading) + **Inter** (body), self-host qua `@fontsource`.
- Thứ tự các khối trang chủ theo spec mục 5.9.

- [ ] Test: `/` có props `slides`, `services` (5), `featuredArt`, `featuredProjects`, `products`, `testimonials`, `posts` (3), `seo.jsonLd['@type']==='LocalBusiness'`. Chỉ nội dung đã xuất bản.
- [ ] Implement, đối chiếu `gardener/index.html` cho bố cục. Swiper cho slides, art và testimonials.
- [ ] Test PASS. `npm run build` OK. Commit.

### Task 6: Trang nội dung công khai

**Files:** Controllers `{ServiceController,PriceController,ProjectController,PostController,PageController,ContactPageController}`, Pages `Services/{Index,Show}.vue, Price.vue, Projects/{Index,Show}.vue, Posts/{Index,Show}.vue, About.vue, Testimonials.vue, Faq.vue, Contact.vue, Error.vue`, `routes/web.php`, `tests/Feature/Pages/ContentPagesTest.php`

- [ ] Test cho mỗi route ở spec mục 5.9 (trừ sản phẩm và giỏ hàng): 200 với nội dung đã xuất bản, 404 với nội dung nháp. `/du-an?loai={slug}` lọc theo project type. `/tin-tuc` phân trang 9 bài. JSON-LD: Service, Article, FAQPage, BreadcrumbList.
- [ ] 404/500 render `Error.vue` qua `bootstrap/app.php` withExceptions.
- [ ] Middleware `HandleRedirects`: nếu path có trong `redirects` thì trả 301. Test.
- [ ] Implement. Test PASS. Commit.

### Task 7: Sản phẩm và cây nghệ thuật (frontend)

**Files:** `app/Http/Controllers/{ProductController,ArtTreeController}.php`, Pages `Products/{Index,Show}.vue, Art/{Index,Show}.vue`, `Components/{CatalogFilters,Gallery}.vue`, `tests/Feature/Pages/CatalogPagesTest.php`

- [ ] Test: `/san-pham` chỉ có standard đã xuất bản. Có filter `danh-muc`, `gia` (khoảng giá: `duoi-500k|500k-2tr|2tr-10tr|tren-10tr`), `sap-xep` (`moi|gia-tang|gia-giam`), phân trang 12. `/cay-canh-nghe-thuat` chỉ có art, filter `tinh-trang` (`con-hang|da-ban`). Art đã bán vẫn 200 nhưng prop `canQuote=false`. Show product trả `canAddToCart` = `isPurchasable()`. Art Show không có `offers` trong JSON-LD khi show_price=false.
- [ ] Gallery dùng GLightbox (ảnh `large`, thumbnail `thumb`, video_url).
- [ ] Implement. Test PASS. Commit.

### Task 8: Giỏ hàng và đặt hàng

**Files:** `app/Services/Cart.php`, `app/Actions/PlaceOrder.php`, `app/Models/{Order,OrderItem}.php`, `app/Enums/{OrderStatus,PaymentMethod}.php`, migrations, `app/Http/Controllers/{CartController,CheckoutController}.php`, `app/Http/Requests/CheckoutRequest.php`, Pages `Cart.vue, Checkout.vue, OrderThanks.vue`, `tests/Feature/Shop/{CartTest,CheckoutTest}.php`

**Interfaces – Produces:**
- `Cart` (singleton, session key `cart` = `[product_id => qty]`): `add(Product $p, int $qty=1): void` (throws `App\Exceptions\NotPurchasable`), `update(int $productId, int $qty): void` (qty≤0 → xoá; tối đa 99), `remove(int $productId)`, `clear()`, `count(): int`, `lines(): Collection<CartLine{product, qty, unitPrice, lineTotal}>` (đọc lại DB, bỏ dòng không còn purchasable), `total(): int`, `pruned(): array<string>` (tên các sản phẩm vừa bị bỏ).
- `OrderStatus: new|contacted|confirmed|shipping|completed|cancelled` + `label()`. `PaymentMethod: cod|bank_transfer`.
- `orders`: `code unique, customer_name, phone, email?, province, address, note?, payment_method, status, subtotal:uint, admin_note?`. `order_items`: `order_id, product_id?, product_name, unit_price, qty, line_total`.
- `PlaceOrder::handle(array $data, Cart $cart): Order`: chạy trong transaction, sinh code `DH-YYMMDD-XXXX` (XXXX = 4 ký tự in hoa ngẫu nhiên A-Z0-9, retry khi trùng), dispatch `OrderPlaced` event, clear cart.
- Routes: `GET /gio-hang`, `POST /gio-hang` (product_id, qty), `PATCH /gio-hang/{product}`, `DELETE /gio-hang/{product}`, `GET /dat-hang`, `POST /dat-hang`, `GET /dat-hang/thanh-cong/{code}` (chỉ xem được khi code nằm trong session `placed_orders`).

- [ ] Tests: thêm standard → count 2 khi qty=2. **POST art → 422 và giỏ rỗng.** **Đổi giá sau khi thêm → order dùng giá mới. Unpublish sau khi thêm → dòng bị bỏ, `pruned` có tên, checkout với giỏ rỗng redirect về `/gio-hang` kèm lỗi.** Checkout hợp lệ tạo Order + items với snapshot tên và giá, giỏ trống, `Event::assertDispatched(OrderPlaced::class)`. Trang thanks của code lạ → 404.
- [ ] Implement. Test PASS. Commit.

### Task 9: Liên hệ / báo giá (inquiries)

**Files:** `app/Models/Inquiry.php`, `app/Enums/{InquiryType,InquiryStatus}.php`, migration, `app/Actions/SubmitInquiry.php`, `app/Http/Controllers/InquiryController.php`, `app/Http/Requests/InquiryRequest.php`, `app/Rules/VietnamesePhone.php`, `app/Http/Middleware/Honeypot.php`, `resources/js/Components/QuoteForm.vue`, `resources/js/Pages/Quote.vue`, `tests/Feature/Leads/InquiryTest.php`, `tests/Unit/VietnamesePhoneTest.php`

**Interfaces – Produces:**
- `InquiryType: contact|quote_product|quote_service|survey`. `InquiryStatus: new|processing|quoted|won|lost`.
- `inquiries`: `type, status, name, phone, email?, address?, message?, subject_type?, subject_id?` (morph: Product | Service | ServicePackage), `meta:json` (survey: `project_kind, location, area, budget, timeline`), `assigned_to?` (users), `internal_note?`, `ip?`.
- `VietnamesePhone`: chấp nhận sau khi bỏ khoảng trắng, dấu chấm, gạch: `^(0|\+84|84)(3|5|7|8|9)\d{8}$`. Static `normalize(string): string` → dạng `0xxxxxxxxx`.
- `POST /lien-he` (InquiryController@store), field ẩn honeypot `website`, `throttle:5,1`. Redirect back kèm flash `success`. `SubmitInquiry::handle(array): Inquiry` dispatch `InquirySubmitted`.
- `QuoteForm.vue` props: `type`, `subjectType?`, `subjectId?`, `subjectLabel?`. Dùng trên Contact, Art/Show, Products/Show, Services/Show, Price (theo gói), Quote.

- [ ] Tests: phone `0912 345 678` và `+84912345678` đều lưu thành `0912345678`; `12345` bị lỗi. **Honeypot có giá trị → không tạo bản ghi nhưng vẫn trả redirect thành công.** **Request thứ 6 trong 1 phút → 429.** quote_product với art lưu morph đúng. Survey lưu `meta`.
- [ ] Implement. Test PASS. Commit.

### Task 10: Thông báo

**Files:** `app/Events/{OrderPlaced,InquirySubmitted}.php`, `app/Listeners/NotifyStaff.php`, `app/Notifications/{NewOrderNotification,NewInquiryNotification,OrderReceivedCustomer,InquiryReceivedCustomer}.php`, `app/Notifications/Channels/ZaloOaChannel.php`, `app/Support/StaffRecipients.php`, `config/services.php`, `tests/Feature/Notifications/StaffNotificationTest.php`

**Interfaces – Produces:**
- `StaffRecipients::route(): AnonymousNotifiable` lấy từ `GeneralSettings`: `mail` → notify_emails, `telegram` → telegram_chat_id. Bot token lấy từ `TELEGRAM_BOT_TOKEN` env. Kênh nào thiếu cấu hình thì bỏ qua kênh đó.
- Notification nhân viên `implements ShouldQueue`, `$tries=5`, `backoff=[10,60,300]`. Nội dung có loại, tên, SĐT, đối tượng hoặc tóm tắt đơn (mã, tổng, số dòng), **cảnh báo "⚠️ Cây đang giữ cho yêu cầu #N"** khi subject là art đang reserved, và URL tới Filament record.
- `ZaloOaChannel::send()`: no-op khi `zalo_oa_enabled=false`. Khi bật thì gọi API theo `ZALO_OA_ACCESS_TOKEN` (chỉ khung, ghi log).
- Customer notification chỉ gửi khi có email.

- [ ] Tests: `Notification::fake()`, tạo inquiry → `NewInquiryNotification` gửi tới anonymous route với `telegram` và `mail`. Thiếu telegram_chat_id → `via()` chỉ có mail. **Không fake, cấu hình Telegram token sai và queue sync → POST `/lien-he` vẫn redirect thành công, Inquiry tồn tại** (listener bắt Throwable và ghi log; thực tế chạy queue nên không ảnh hưởng). Art reserved → message có chuỗi cảnh báo.
- [ ] Implement. Test PASS. Commit.

### Task 11: Admin đơn hàng, yêu cầu và quy trình cây độc bản

**Files:** `app/Filament/Resources/{OrderResource,InquiryResource}`, `app/Filament/Widgets/{NewLeadsStats,LatestInquiries}.php`, `app/Actions/{ReserveArtTree,MarkArtSold,ReleaseArtTree}.php`, `app/Policies/*`, `tests/Feature/Admin/{OrderAdminTest,ArtWorkflowTest}.php`

- [ ] `ReserveArtTree::handle(Product $p, Inquiry $i)`: chỉ khi available. Set reserved + reserved_inquiry_id. `MarkArtSold::handle(Product $p)`. `ReleaseArtTree::handle(Product $p)`: về available, xoá liên kết.
- [ ] InquiryResource: badge số lượng "Mới" ở navigation. Action "Giữ cây này" khi subject là art. Khi đổi status sang `lost` mà inquiry đang giữ cây thì hiện notification Filament gợi ý "Trả cây về còn hàng" kèm action.
- [ ] OrderResource: xem items, đổi status, admin_note. Sales và Admin có quyền. Editor và Tech không.
- [ ] Tests: reserve khi đã reserved → exception `ArtNotAvailable`. Sales vào được `/admin/orders`, Editor → 403. Dashboard widget đếm inquiries `new`.
- [ ] Implement. Test PASS. Commit.

### Task 12: Lịch chăm sóc

**Files:** `app/Models/{CareContract,CareVisit}.php`, `app/Enums/{CareFrequency,CareVisitStatus}.php`, migrations, `app/Actions/GenerateCareVisits.php`, `app/Console/Commands/SendCareReminders.php`, `app/Notifications/CareVisitsToday.php`, `app/Filament/Resources/{CareContractResource,CareVisitResource}`, `tests/Feature/Care/CareScheduleTest.php`

**Interfaces – Produces:**
- `CareFrequency: weekly|biweekly|monthly` + `interval(): CarbonInterval`. `CareVisitStatus: scheduled|done|cancelled|rescheduled`.
- `care_contracts`: `customer_name, phone, address, service_package_id?, inquiry_id?, starts_on, ends_on?, frequency, default_technician_id?, note, is_active`.
- `care_visits`: `care_contract_id, scheduled_on, technician_id?, status, note?`, media `before`, `after`.
- `GenerateCareVisits::handle(CareContract $c, CarbonImmutable $until): int`: idempotent (unique `[care_contract_id, scheduled_on]`), sinh từ starts_on theo interval tới `min(until, ends_on)`. Scheduler chạy hằng ngày với until = hôm nay + 60 ngày.
- `care:remind` lúc 06:30: mỗi kỹ thuật viên có lượt hôm nay nhận một `CareVisitsToday` (mail + telegram nhóm).
- Technician chỉ thấy lượt có `technician_id = auth id` (query scope trong resource). Có view lịch dạng bảng theo tuần.

- [ ] Tests: weekly từ 2026-12-01 tới 2026-12-31 → 5 lượt (01,08,15,22,29). Chạy lại → vẫn 5. ends_on 2026-12-15 → 3. Tech A không thấy lượt của Tech B (Livewire test `assertCanNotSeeTableRecords`). Remind gửi đúng cho tech có lượt.
- [ ] Implement. Test PASS. Commit.

### Task 13: SEO, sitemap

**Files:** `app/Console/Commands/GenerateSitemap.php`, `app/Support/JsonLd.php`, `routes/console.php` (schedule), `public/robots.txt`, `tests/Feature/Seo/SitemapTest.php`

- [ ] `JsonLd`: `localBusiness(GeneralSettings)`, `product(Product)`, `service(Service)`, `article(Post)`, `faqPage(Collection)`, `breadcrumbs(array)`. Thay các JSON-LD đã viết tạm ở Task 5–7 bằng các hàm này.
- [ ] `sitemap:generate` ghi `public/sitemap.xml` gồm trang tĩnh + mọi record đã xuất bản (lastmod = updated_at). Chạy hằng ngày, và chạy qua queue khi Product/Project/Post/Service được publish.
- [ ] Test: sitemap chứa URL sản phẩm đã xuất bản, không có bản nháp. robots.txt trỏ sitemap.
- [ ] Commit.

### Task 14: Cache và hiệu năng

**Files:** `app/Support/SiteCache.php`, `app/Observers/FlushSiteCache.php`, cập nhật controllers, `tests/Feature/CacheTest.php`

- [ ] `SiteCache::remember(string $key, Closure $cb, array $tags=['site'])` có TTL 1 ngày. Khi driver không hỗ trợ tag thì fallback `Cache::remember` + version key. Dùng cho settings, menu, các khối trang chủ, danh mục.
- [ ] Observer trên các model nội dung flush tag `site` khi saved hoặc deleted.
- [ ] Ảnh: `<img srcset>` từ responsive images. `loading="lazy"` trừ slide đầu (`fetchpriority="high"`).
- [ ] Test: sửa Service → menu prop đổi ngay ở request kế tiếp.
- [ ] Commit.

### Task 15: Dữ liệu mẫu

**Files:** `database/seeders/{DemoSeeder,…}.php`, `database/factories/*`

- [ ] Factory cho mọi model. `DemoSeeder`: 5 dịch vụ (Tư vấn – thiết kế, Cung cấp cây công trình, Mua bán cây xanh, Cho thuê cây xanh, Chăm sóc cây xanh), 2–3 gói mỗi dịch vụ, 6 danh mục, 16 sản phẩm, 10 cây nghệ thuật, 8 dự án, 9 bài viết, 6 đánh giá, 8 FAQ, 3 slide. Ảnh copy từ `../gardener/images`. Nội dung tiếng Việt.
- [ ] `php artisan migrate:fresh --seed` chạy OK. Mở mọi trang thật bằng trình duyệt (Playwright) để kiểm tra, chụp màn hình trang chủ.
- [ ] Commit.

### Task 16: Triển khai và backup

**Files:** `deploy/nginx.conf`, `deploy/supervisor/{queue,ssr}.conf`, `deploy/deploy.sh`, `deploy/README.md`, `config/backup.php`

- [ ] spatie/laravel-backup: DB hằng ngày lúc 02:00, toàn bộ `storage/app/public` vào chủ nhật lúc 03:00. Cleanup giữ 7 bản ngày và 4 bản tuần. Có thêm disk `backup_remote` (S3-compatible, mặc định tắt qua env). Thông báo mail khi backup lỗi.
- [ ] `deploy.sh`: `git pull` → `composer install --no-dev -o` → `npm ci && npm run build` (client + ssr) → `php artisan migrate --force` → `optimize` → `filament:optimize` → `queue:restart` → `supervisorctl restart caycanh-ssr`.
- [ ] README tiếng Việt: cài Ubuntu (PHP 8.4-FPM, MySQL, Redis, Node, certbot), cron `* * * * * php artisan schedule:run`, biến môi trường cần điền (Telegram, SMTP, admin).
- [ ] Full `php artisan test` PASS, `npm run build` OK. Commit.
