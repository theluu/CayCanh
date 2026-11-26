# Thiết kế website Cây Cảnh (dựa trên mẫu Gardener)

Ngày: 2026-11-26 · Trạng thái: **đã duyệt**. Chưa có website hay tên miền cũ, nên không cần migrate nội dung hay redirect SEO.

## 1. Mục đích và lý do

Website cho một doanh nghiệp cây xanh. Mục đích chính là **quảng bá doanh nghiệp**, phụ là **bán hàng online**.
Hai mảng nội dung chủ lực:

1. **Dịch vụ cây xanh công trình**: tư vấn, cung cấp, mua bán, cho thuê, chăm sóc cây xanh cho công trình.
2. **Cây cảnh nghệ thuật**: cây độc bản, giá thương lượng.

Khách hàng đặt hàng hoặc gửi yêu cầu báo giá **ngay trên website**. Hệ thống lưu vào DB và thông báo cho kinh doanh/quản trị.
Các kênh Zalo, Facebook, TikTok chỉ là kênh liên hệ phụ (nút, link).

## 2. Mục tiêu / Ngoài phạm vi / Điều cần tránh

**Mục tiêu**
- Website tiếng Việt, chuẩn SEO, tải nhanh trên di động. Giao diện theo bố cục và nhịp của mẫu `gardener/`.
- Khách có thể: xem dịch vụ, dự án, cây nghệ thuật, tin tức; gửi liên hệ hoặc báo giá; mua sản phẩm giá cố định qua giỏ hàng và form đặt hàng.
- Nhân viên quản lý toàn bộ nội dung, đơn hàng, yêu cầu báo giá và lịch chăm sóc trong Filament, có phân quyền.
- Có đơn hoặc yêu cầu mới thì nhân viên được báo ngay qua Telegram và Email.

**Ngoài phạm vi**
- Thanh toán online. Chỉ ghi nhận phương thức COD hoặc chuyển khoản.
- Tài khoản khách hàng: bỏ trang login/register của mẫu. Chỉ nhân viên đăng nhập `/admin`.
- Đa ngôn ngữ, app mobile, mã giảm giá, tính phí vận chuyển tự động, quản lý kho chi tiết.

**Điều cần tránh (coi là thất bại dù vẫn chạy)**
- Mất lead: yêu cầu hoặc đơn phải được lưu DB **trước**, thông báo gửi sau qua queue, có retry. Nếu gửi thông báo lỗi, form vẫn báo thành công cho khách.
- Một cây nghệ thuật độc bản được bán hoặc hứa cho hai khách.
- Trang nặng ảnh tải chậm trên 4G. Mục tiêu LCP < 2.5s trên di động cho trang chủ và trang chi tiết.
- Giá trong đơn khác giá trong DB. Server luôn tính lại giá khi đặt.

## 3. Ràng buộc

- **Backend:** PHP 8.4, Laravel 13, MySQL 8, Redis (cache, session, queue).
- **Admin:** Filament v5. Phân quyền bằng Spatie Laravel Permission.
- **Frontend:** **Inertia.js + Vue 3 + SSR**, Tailwind CSS, Swiper.js, GLightbox. Build bằng Vite.
- **Media:** Spatie Media Library (thumbnail, WebP, responsive images).
- **SEO:** Spatie Laravel Sitemap, Schema.org (Product, Service, LocalBusiness, BreadcrumbList, Article).
- **Hạ tầng:** 1 VPS Ubuntu + Nginx + PHP-FPM + Supervisor (queue worker, Inertia SSR) + cron (scheduler, backup).
- Mẫu `gardener/` dùng Bootstrap 3, jQuery, Owl Carousel và Revolution Slider. **Không dùng lại CSS/JS này.** Viết lại bằng Tailwind và Vue, giữ bố cục từng khối. Ảnh trong mẫu chỉ dùng làm ảnh tạm khi phát triển.

## 4. Các phương án đã cân nhắc

Frontend (người dùng chọn Vue). Ba cách ghép Vue với Laravel:

| Phương án | Ưu | Nhược |
|---|---|---|
| **1. Inertia + Vue 3 + SSR (đã chọn)** | Một repo, một lần deploy, không cần API riêng, có HTML SSR cho SEO, Filament ở cùng app | Gắn với Inertia; không có sẵn REST API cho mobile |
| 2. Nuxt tách riêng + Laravel API | SEO và hiệu năng tốt nhất, API dùng lại được | Hai dự án, CORS, vận hành nặng trên 1 VPS |
| 3. Vue SPA thuần | Đơn giản | Google thấy trang trống, hỏng mục tiêu SEO. Loại |

Trước đó có cân nhắc Blade + Livewire. Người dùng chọn Vue nên không dùng.
**Lý do chọn 1:** giữ được SEO, gọn cho một VPS, giữ nguyên phần còn lại của stack.

## 5. Phần đã quyết

### 5.1 Hai loại sản phẩm
- **Sản phẩm thường** (cây thường, chậu, vật tư, cây công trình bán lẻ): có giá, thêm được vào giỏ.
- **Cây cảnh nghệ thuật** (độc bản): hiện "Giá liên hệ" và nút **Nhận báo giá**, **không** cho vào giỏ.
  Trạng thái: Còn hàng → Đang giữ (đã có khách thương lượng) → Đã bán. Cây "Đã bán" vẫn hiển thị để làm portfolio, có nhãn "Đã bán".
  Admin có thể bật hiển thị giá cho từng cây, nhưng cây vẫn chỉ nhận báo giá, không vào giỏ. *(Quyết định của Claude.)*
  **Chuyển trạng thái làm thủ công** *(quyết định của Claude)*: khi khách gửi báo giá, cây **không** tự chuyển sang "Đang giữ". Kinh doanh tự đặt "Đang giữ" và gắn với inquiry tương ứng, sau đó đặt "Đã bán" khi chốt. Hold không tự hết hạn. Nếu inquiry gắn với cây bị đóng ở trạng thái "Không thành công", Filament nhắc kinh doanh trả cây về "Còn hàng".
  Cây "Đang giữ" vẫn nhận báo giá mới. Khi đó Filament hiện cảnh báo "Cây đang giữ cho yêu cầu #…", còn thông báo Telegram ghi rõ cây đang giữ. Cây "Đã bán" ẩn nút báo giá và thay bằng "Xem cây tương tự".

### 5.2 Giỏ hàng và đặt hàng
- Giỏ lưu trong **session phía server** (Redis), khách không cần đăng nhập. *(Quyết định của Claude: một nguồn sự thật, tránh lệch giá.)*
- Trang giỏ: sửa số lượng, xoá sản phẩm.
- Trang "Đặt hàng" là form: họ tên*, SĐT*, email, tỉnh/thành, địa chỉ*, ghi chú, phương thức (COD / chuyển khoản).
- Khi gửi: tạo `Order` + `OrderItem` (lưu sẵn tên, giá, số lượng tại thời điểm đặt), mã đơn dạng `DH-YYMMDD-XXXX`. Xoá giỏ, chuyển tới trang cảm ơn hiển thị mã đơn. Bắn event để gửi thông báo.
- Phí vận chuyển: ghi "Nhân viên sẽ liên hệ xác nhận phí giao hàng". Không tính tự động.
- Trạng thái đơn: Mới → Đã liên hệ → Đã chốt → Đang giao → Hoàn thành / Huỷ.

### 5.3 Liên hệ / Báo giá (lead)
Một bảng `inquiries` chung, phân loại theo `type`:
- `contact`: form trang Liên hệ.
- `quote_product`: từ trang cây nghệ thuật hoặc sản phẩm.
- `quote_service`: từ trang dịch vụ hoặc gói ở bảng giá.
- `survey`: yêu cầu khảo sát công trình (form `/bao-gia`), có thêm loại công trình, địa điểm, diện tích, ngân sách dự kiến, thời gian mong muốn.

Mỗi yêu cầu liên kết polymorphic tới đối tượng được hỏi (product, service, package). Có thêm trạng thái (Mới / Đang xử lý / Đã báo giá / Thành công / Không thành công), người phụ trách và ghi chú nội bộ.
Chống spam: honeypot + rate limit theo IP. *(Quyết định của Claude.)*

### 5.4 Thông báo
- Event `InquiryCreated` và `OrderCreated` → Notification **chạy qua queue** gửi vào **nhóm Telegram** của kinh doanh và **Email** tới danh sách người nhận cấu hình trong admin.
- Nội dung có tên, SĐT, đối tượng hỏi hoặc tóm tắt đơn, và link vào bản ghi trong Filament.
- Có retry và backoff. Lỗi gửi được ghi log, không ảnh hưởng tới khách.
- **Zalo OA:** làm sẵn interface channel và cờ cấu hình, mặc định tắt. Chỉ bật khi doanh nghiệp có OA đã xác thực.
- Gửi email xác nhận cho khách nếu khách nhập email. *(Quyết định của Claude.)*

### 5.5 Kênh liên hệ ngoài
- Nút nổi (mọi trang): Gọi điện, Zalo (`zalo.me/<sđt>`), Messenger.
- Footer và trang Liên hệ: icon Facebook, TikTok, YouTube, kèm bản đồ Google Maps nhúng.
- Tất cả link, hotline, địa chỉ và giờ làm việc sửa được trong Filament → **Cài đặt chung**.

### 5.6 Lịch chăm sóc (nội bộ)
Phục vụ dịch vụ cho thuê và chăm sóc cây công trình. **Chỉ dùng trong admin**, không hiển thị ra frontend.
- `care_contracts`: khách hàng, địa chỉ công trình, gói dịch vụ, ngày bắt đầu và kết thúc, tần suất (tuần / 2 tuần / tháng), ghi chú. Có thể tạo từ một inquiry đã thành công.
- `care_visits`: sinh tự động theo tần suất. Mỗi lượt có ngày, kỹ thuật viên phụ trách, trạng thái (Đã lên lịch / Hoàn thành / Huỷ / Dời lịch), ghi chú và ảnh trước/sau.
- Kỹ thuật viên chỉ thấy lượt của mình, dạng danh sách và lịch. Mỗi sáng gửi Telegram/Email nhắc các lượt trong ngày. *(Quyết định của Claude.)*

### 5.7 Phân quyền (Spatie Permission)
| Vai trò | Quyền |
|---|---|
| Quản trị viên | Toàn bộ, gồm người dùng, vai trò, cài đặt |
| Biên tập viên | Sản phẩm, cây nghệ thuật, danh mục, dịch vụ, gói giá, dự án, tin tức, FAQ, đánh giá, banner, trang tĩnh |
| Kinh doanh | Đơn hàng, yêu cầu liên hệ/báo giá, hợp đồng chăm sóc; xem sản phẩm; đổi trạng thái cây nghệ thuật |
| Kỹ thuật viên | Lượt chăm sóc được giao (xem, cập nhật, upload ảnh); xem hợp đồng liên quan |

### 5.8 Mô hình dữ liệu (mức khái niệm)
- `categories` (cây phân cấp, `scope`: product | art)
- `products` (`kind`: standard | art; tên, slug, SKU, giá, giá khuyến mãi, `show_price`, `availability`, mô tả, thông số JSON: chiều cao, hoành tán, đường kính gốc, dáng thế, tuổi cây, xuất xứ; nổi bật; xuất bản; SEO; media: gallery + video URL)
- `services` (5 dịch vụ: Tư vấn – thiết kế, Cung cấp cây công trình, Mua bán cây, Cho thuê cây, Chăm sóc cây; nội dung, ảnh, FAQ riêng, SEO)
- `service_packages` (thuộc service; tên, giá từ, đơn vị, danh sách tính năng, nổi bật) → trang Bảng giá
- `projects` (tên, loại công trình, địa điểm, năm, quy mô, dịch vụ đã dùng (n-n với services), mô tả, gallery, video, SEO)
- `posts`, `post_categories`, `tags`
- `testimonials`, `faqs`, `slides` (banner trang chủ), `partners` (logo khách hàng, tuỳ chọn)
- `orders`, `order_items`, `inquiries`, `care_contracts`, `care_visits`
- `settings` (key–value hoặc spatie/laravel-settings)
- `users` + bảng của Spatie Permission

### 5.9 Trang frontend và ánh xạ từ mẫu
| Route | Trang | Lấy bố cục từ mẫu |
|---|---|---|
| `/` | Trang chủ | `index.html` |
| `/gioi-thieu` | Giới thiệu | `about-gardener.html` |
| `/dich-vu`, `/dich-vu/{slug}` | Dịch vụ | `all-services.html`, `design-and-planning.html` (và 5 trang dịch vụ khác) |
| `/bang-gia` | Bảng giá gói thuê/chăm sóc | `price.html` |
| `/cay-canh-nghe-thuat`, `/cay-canh-nghe-thuat/{slug}` | Cây nghệ thuật | `shop-with-sidebar.html`, `single-shop.html` + GLightbox |
| `/san-pham`, `/san-pham/danh-muc/{slug}`, `/san-pham/{slug}` | Sản phẩm | `shop.html`, `shop-with-sidebar.html`, `single-shop.html` |
| `/du-an`, `/du-an/{slug}` | Dự án | `masanory_gallery.html` (lọc theo loại), `fullwidth-detail-page.html` |
| `/tin-tuc`, `/tin-tuc/danh-muc/{slug}`, `/tin-tuc/{slug}` | Tin tức / kiến thức | `blog-list-view.html`, `blog-single-post.html` |
| `/danh-gia-khach-hang` | Đánh giá | `client-testimonals.html` |
| `/cau-hoi-thuong-gap` | FAQ | `faq.html` |
| `/lien-he` | Liên hệ | `contact-us.html` |
| `/bao-gia` | Yêu cầu khảo sát / báo giá công trình | form mới, cùng phong cách mẫu |
| `/gio-hang`, `/dat-hang`, `/dat-hang/thanh-cong/{code}` | Giỏ, đặt hàng, cảm ơn | `cart.html`, `checkout-page.html` |
| 404 | | `404.html` |
| `/sitemap.xml` | | |

Không dùng: `login.html`. Chỉ chọn một kiểu cho mỗi loại trang (gallery, blog), không làm nhiều biến thể như mẫu.

**Trang chủ** (theo thứ tự): Slider banner (Swiper, quản lý trong admin) → Giới thiệu ngắn + số liệu (counter) → 5 dịch vụ → Cây nghệ thuật nổi bật (Swiper) → Dự án tiêu biểu (lưới, lọc) → Sản phẩm bán online → Đánh giá khách hàng (Swiper) → Tin tức mới → Băng CTA "Nhận báo giá" → Footer.

### 5.10 SEO
- Inertia SSR. Mỗi trang có title, description, canonical, Open Graph. Các model có trường SEO riêng, nếu trống thì tự sinh.
- JSON-LD: `LocalBusiness` (trang chủ, liên hệ), `Service` (trang dịch vụ), `Product` (sản phẩm; cây nghệ thuật không có `offers` khi giấu giá), `Article` (tin), `BreadcrumbList` (mọi trang con), `FAQPage` (FAQ).
- Sitemap sinh lại hằng ngày bằng scheduler và khi xuất bản nội dung.
- Slug tiếng Việt không dấu. Đổi slug thì tự tạo redirect 301. *(Quyết định của Claude.)*

### 5.11 Media và hiệu năng
- Media Library conversions: `thumb` (400), `medium` (800), `large` (1600), đều **WebP**, kèm responsive srcset. Conversions chạy qua queue.
- Video: nhập URL YouTube/TikTok (embed). Cho upload MP4 ngắn (≤ 50MB).
- Redis cache: settings, menu, khối trang chủ, danh sách danh mục, các trang chi tiết đã xuất bản. Xoá cache theo tag khi model được lưu.
- Ảnh lazy-load. Chỉ preload ảnh banner đầu tiên.

### 5.12 Hạ tầng và vận hành
- Ubuntu LTS, Nginx, PHP 8.4-FPM, MySQL 8, Redis, Node LTS (cho SSR), SSL Let's Encrypt.
- Supervisor: `queue:work` (Redis) và `inertia:start-ssr`.
- Cron: `schedule:run` mỗi phút (sitemap, sinh lượt chăm sóc, nhắc lịch, backup).
- Backup: spatie/laravel-backup sao lưu DB hằng ngày và thư mục media hằng tuần. Giữ 7 bản ngày và 4 bản tuần trên máy, cộng một bản ngoài VPS (đích lưu trữ do người dùng chọn). Có thông báo khi backup lỗi.
- Script deploy (git pull → composer → npm build → migrate → cache → restart worker/SSR) và tài liệu cài đặt server.

### 5.13 Giao diện
- Theo bố cục, nhịp và tông xanh lá của mẫu Gardener. Viết lại bằng Tailwind (theme token: màu, font hỗ trợ tiếng Việt).
- Ưu tiên mobile. Header có menu: Giới thiệu, Dịch vụ (dropdown 5 mục), Cây nghệ thuật, Sản phẩm, Dự án, Tin tức, Liên hệ, cùng icon giỏ hàng và nút "Nhận báo giá".
- Toàn bộ text tiếng Việt, tiền tệ VND.

### 5.14 Kiểm thử
- Pest: feature test cho giỏ hàng, đặt hàng (tính lại giá, không cho cây nghệ thuật vào giỏ), tạo inquiry, dispatch thông báo (fake), quyền truy cập theo vai trò trong Filament, sinh lượt chăm sóc.

## 6. Để người xây dựng quyết định
- Cấu trúc thư mục, cách chia component Vue, tên cột cụ thể và index.
- Gói cụ thể cho Telegram notification và settings, miễn đáp ứng mục 5.
- Chi tiết theme Tailwind và hiệu ứng, miễn giữ nhịp của mẫu.
- Dữ liệu mẫu (seeder) cho phát triển và demo.
- Thứ tự triển khai các phần, với điều kiện đi theo từng giai đoạn chạy được: nền tảng + admin → nội dung + trang công khai → giỏ/đặt hàng + lead + thông báo → lịch chăm sóc → SEO + hiệu năng → triển khai.
