<?php

namespace Database\Seeders;

use App\Enums\ArtAvailability;
use App\Enums\CareFrequency;
use App\Enums\ProductKind;
use App\Enums\Role;
use App\Models\CareContract;
use App\Models\Category;
use App\Models\Faq;
use App\Models\Page;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Product;
use App\Models\Project;
use App\Models\ProjectType;
use App\Models\Service;
use App\Models\Slide;
use App\Models\Testimonial;
use App\Models\User;
use App\Settings\GeneralSettings;
use Illuminate\Database\Seeder;
use Spatie\MediaLibrary\HasMedia;

/**
 * Vietnamese demo content. Images come from the Gardener template (../gardener/images) and are placeholders only.
 */
class DemoSeeder extends Seeder
{
    private string $images;

    public function run(): void
    {
        $this->images = base_path('../gardener/images');

        $this->settings();
        $this->staff();
        $services = $this->services();
        $this->catalog();
        $this->projects($services);
        $this->posts();
        $this->extras($services);
    }

    private function settings(): void
    {
        $settings = app(GeneralSettings::class);
        $settings->site_name = 'Cây Cảnh Xanh';
        $settings->hotline = '0909 123 456';
        $settings->email = 'lienhe@caycanhxanh.vn';
        $settings->address = '268 Nguyễn Văn Linh, P. Tân Phong, Quận 7, TP. Hồ Chí Minh';
        $settings->working_hours = 'Thứ 2 – Chủ nhật: 7:30 – 18:00';
        $settings->zalo_phone = '0909123456';
        $settings->facebook_url = 'https://facebook.com';
        $settings->messenger_url = 'https://m.me/';
        $settings->tiktok_url = 'https://tiktok.com';
        $settings->youtube_url = 'https://youtube.com';
        $settings->map_embed_url = 'https://www.google.com/maps?q=Qu%E1%BA%ADn%207%2C%20H%E1%BB%93%20Ch%C3%AD%20Minh&output=embed';
        $settings->notify_emails = ['kinhdoanh@caycanhxanh.vn'];
        $settings->stats = ['years' => 12, 'projects' => 350, 'trees' => 8000];
        $settings->save();
    }

    private function staff(): void
    {
        foreach ([
            ['Biên tập viên', 'bientap@caycanh.test', Role::Editor],
            ['Nhân viên kinh doanh', 'kinhdoanh@caycanh.test', Role::Sales],
            ['Kỹ thuật viên Minh', 'kythuat@caycanh.test', Role::Technician],
        ] as [$name, $email, $role]) {
            User::firstOrCreate(['email' => $email], ['name' => $name, 'password' => 'password'])->assignRole($role->value);
        }
    }

    /** @return array<string, Service> */
    private function services(): array
    {
        $data = [
            'tu-van' => ['Tư vấn – thiết kế cảnh quan', 'Khảo sát hiện trạng, tư vấn chọn cây phù hợp ánh sáng, phong thủy và ngân sách; thiết kế phối cảnh 2D/3D cho sân vườn, văn phòng, công trình.', 'service/7.jpg', [
                ['Khảo sát & tư vấn', null, '', ['Khảo sát tận nơi', 'Tư vấn chọn cây', 'Đề xuất ngân sách'], false],
                ['Thiết kế 2D', 5000000, '/công trình', ['Mặt bằng bố trí cây', 'Danh mục cây & chậu', 'Dự toán chi tiết'], true],
                ['Thiết kế 3D trọn gói', 15000000, '/công trình', ['Phối cảnh 3D', 'Hồ sơ thi công', 'Giám sát tác giả'], false],
            ]],
            'cung-cap' => ['Cung cấp cây xanh công trình', 'Cung cấp cây bóng mát, cây bụi, thảm cỏ cho khu đô thị, nhà xưởng, trường học với nguồn cây khỏe, đúng quy cách, có bảo hành.', 'service/8.jpg', [
                ['Cây bụi – thảm cỏ', 50000, '/m²', ['Cỏ lá gừng, cỏ nhung', 'Cây bụi viền', 'Bảo hành 30 ngày'], false],
                ['Cây bóng mát đô thị', null, '', ['Sao đen, dầu, lim xẹt…', 'Đúng quy cách bầu', 'Vận chuyển cẩu hạ'], true],
                ['Trọn gói thi công', null, '', ['Cung cấp & trồng', 'Hệ thống tưới', 'Bàn giao chăm sóc'], false],
            ]],
            'mua-ban' => ['Mua bán cây xanh', 'Thu mua, ký gửi và mua bán cây cảnh, cây cổ thụ, cây công trình; định giá minh bạch, hỗ trợ vận chuyển và trồng lại.', 'service/9.jpg', []],
            'cho-thue' => ['Cho thuê cây xanh', 'Cho thuê cây văn phòng, sảnh, sự kiện theo tháng; thay cây định kỳ và chăm sóc miễn phí trong suốt thời gian thuê.', 'service/10.jpg', [
                ['Văn phòng nhỏ', 1500000, '/tháng', ['10 cây các loại', 'Chăm sóc 2 lần/tuần', 'Thay cây miễn phí'], false],
                ['Văn phòng vừa', 3900000, '/tháng', ['30 cây các loại', 'Chậu composite cao cấp', 'Thay cây miễn phí', 'Báo cáo hằng tháng'], true],
                ['Sự kiện – hội nghị', 2000000, '/ngày', ['Trang trí sân khấu, sảnh', 'Lắp đặt & thu hồi', 'Tùy chọn theo chủ đề'], false],
            ]],
            'cham-soc' => ['Chăm sóc cây xanh', 'Chăm sóc định kỳ cây xanh biệt thự, văn phòng, công trình: tưới, bón phân, cắt tỉa, phòng trừ sâu bệnh, có báo cáo ảnh sau mỗi lượt.', 'service/11.jpg', [
                ['Gói cơ bản', 800000, '/tháng', ['2 lượt/tháng', 'Cắt tỉa, làm cỏ', 'Báo cáo ảnh'], false],
                ['Biệt thự – sân vườn', 2500000, '/tháng', ['4 lượt/tháng', 'Bón phân, phun thuốc', 'Cắt tỉa tạo hình'], true],
                ['Công trình lớn', null, '', ['Đội chăm sóc riêng', 'Lịch theo hợp đồng', 'Báo cáo định kỳ'], false],
            ]],
        ];

        $services = [];
        $sort = 0;
        foreach ($data as $key => [$name, $summary, $image, $packages]) {
            $service = Service::create([
                'name' => $name,
                'summary' => $summary,
                'body' => '<p>'.$summary.'</p><h3>Quy trình thực hiện</h3><ol><li>Tiếp nhận yêu cầu và khảo sát hiện trạng.</li><li>Đề xuất phương án và báo giá chi tiết.</li><li>Thực hiện theo tiến độ, nghiệm thu từng hạng mục.</li><li>Bàn giao và hướng dẫn chăm sóc.</li></ol><h3>Cam kết</h3><ul><li>Cây khỏe, đúng chủng loại và quy cách.</li><li>Đội ngũ kỹ thuật có kinh nghiệm.</li><li>Bảo hành và hỗ trợ sau bàn giao.</li></ul>',
                'sort' => $sort++,
            ]);
            $this->attach($service, 'cover', [$image]);
            $this->attach($service, 'gallery', [$image, 'service/12.jpg', 'gallery/20.jpg']);

            foreach ($packages as $index => [$packageName, $price, $unit, $features, $highlighted]) {
                $service->packages()->create(['name' => $packageName, 'price_from' => $price, 'unit' => $unit, 'features' => $features, 'is_highlighted' => $highlighted, 'sort' => $index]);
            }

            $services[$key] = $service;
        }

        return $services;
    }

    private function catalog(): void
    {
        $standard = collect(['Cây nội thất', 'Cây văn phòng', 'Cây phong thủy', 'Cây ngoại thất', 'Chậu cảnh', 'Vật tư & phân bón'])
            ->mapWithKeys(fn (string $name, int $i) => [$name => Category::create(['name' => $name, 'scope' => ProductKind::Standard, 'sort' => $i])]);
        $art = collect(['Tùng', 'Sanh – Si – Đa', 'Mai', 'Linh sam', 'Cây cổ thụ'])
            ->mapWithKeys(fn (string $name, int $i) => [$name => Category::create(['name' => $name, 'scope' => ProductKind::Art, 'sort' => $i])]);

        $products = [
            ['Cây Kim Tiền chậu sứ', 'Cây nội thất', 450000, 390000, '60 – 70 cm'],
            ['Cây Lưỡi Hổ vàng', 'Cây nội thất', 280000, null, '50 cm'],
            ['Cây Trầu Bà Nam Mỹ', 'Cây nội thất', 650000, null, '1,2 m'],
            ['Cây Bàng Singapore', 'Cây văn phòng', 950000, 850000, '1,5 m'],
            ['Cây Phát Tài khúc', 'Cây văn phòng', 1200000, null, '1,6 m'],
            ['Cây Hạnh Phúc', 'Cây văn phòng', 1500000, null, '1,8 m'],
            ['Cây Thiết Mộc Lan', 'Cây phong thủy', 750000, null, '1,4 m'],
            ['Cây Ngọc Ngân để bàn', 'Cây phong thủy', 180000, null, '30 cm'],
            ['Cây Kim Ngân thắt bím', 'Cây phong thủy', 520000, null, '80 cm'],
            ['Cây Hoa Giấy ghép màu', 'Cây ngoại thất', 1800000, 1600000, '1,5 m'],
            ['Cây Cau Vua', 'Cây ngoại thất', 2400000, null, '2,5 m'],
            ['Cây Tùng La Hán bonsai mini', 'Cây ngoại thất', 3200000, null, '70 cm'],
            ['Chậu composite tròn 50cm', 'Chậu cảnh', 650000, null, 'Đường kính 50 cm'],
            ['Chậu xi măng giả đá', 'Chậu cảnh', 380000, null, '40 × 40 cm'],
            ['Phân bón hữu cơ vi sinh 5kg', 'Vật tư & phân bón', 120000, null, '5 kg'],
            ['Đất sạch trồng cây 20dm³', 'Vật tư & phân bón', 65000, null, '20 dm³'],
        ];

        foreach ($products as $i => [$name, $category, $price, $sale, $height]) {
            $product = Product::create([
                'kind' => ProductKind::Standard, 'category_id' => $standard[$category]->id, 'name' => $name, 'sku' => 'SP'.str_pad((string) ($i + 1), 4, '0', STR_PAD_LEFT),
                'price' => $price, 'sale_price' => $sale, 'summary' => $name.' khỏe đẹp, giao kèm chậu và hướng dẫn chăm sóc.',
                'description' => '<p>'.$name.' được tuyển chọn tại vườn, thuần khí hậu, phù hợp trang trí nhà ở và văn phòng.</p><ul><li>Ánh sáng: tán xạ hoặc nửa bóng.</li><li>Tưới nước: 2–3 lần/tuần.</li><li>Bón phân: 1 tháng/lần.</li></ul>',
                'specs' => ['Chiều cao' => $height, 'Xuất xứ' => 'Vườn ươm Củ Chi'], 'is_featured' => $i % 3 === 0,
            ]);
            $this->attach($product, 'gallery', ['shop/'.(($i % 12) + 1).'.jpg', 'shop/'.((($i + 3) % 12) + 1).'.jpg']);
        }

        $trees = [
            ['Tùng La Hán dáng trực', 'Tùng', 'Trực', '1,6 m', '1,1 m', '25 cm', '40 năm', ArtAvailability::Available],
            ['Tùng Cối Nhật dáng bay', 'Tùng', 'Bay', '1,2 m', '1,3 m', '18 cm', '30 năm', ArtAvailability::Available],
            ['Sanh cổ dáng văn nhân', 'Sanh – Si – Đa', 'Văn nhân', '1,4 m', '0,9 m', '30 cm', '50 năm', ArtAvailability::Reserved],
            ['Si Đài Loan dáng huyền', 'Sanh – Si – Đa', 'Huyền', '1,1 m', '1,2 m', '22 cm', '25 năm', ArtAvailability::Available],
            ['Đa búp đỏ ôm đá', 'Sanh – Si – Đa', 'Ôm đá', '0,9 m', '0,8 m', '15 cm', '20 năm', ArtAvailability::Available],
            ['Mai chiếu thủy dáng xiêu', 'Mai', 'Xiêu', '0,8 m', '0,7 m', '12 cm', '15 năm', ArtAvailability::Available],
            ['Mai vàng Yên Tử cổ thụ', 'Mai', 'Trực', '2,2 m', '1,8 m', '35 cm', '60 năm', ArtAvailability::Sold],
            ['Linh sam đá dáng thác đổ', 'Linh sam', 'Thác đổ', '0,7 m', '0,9 m', '10 cm', '20 năm', ArtAvailability::Available],
            ['Khế cổ tán tròn', 'Cây cổ thụ', 'Tán tròn', '2,5 m', '2,2 m', '40 cm', '70 năm', ArtAvailability::Available],
            ['Duối cổ dáng long', 'Cây cổ thụ', 'Long', '1,3 m', '1,0 m', '28 cm', '45 năm', ArtAvailability::Available],
        ];

        foreach ($trees as $i => [$name, $category, $shape, $height, $spread, $trunk, $age, $availability]) {
            $tree = Product::create([
                'kind' => ProductKind::Art, 'category_id' => $art[$category]->id, 'name' => $name, 'sku' => 'NT'.str_pad((string) ($i + 1), 3, '0', STR_PAD_LEFT),
                'price' => (50 + $i * 25) * 1000000, 'show_price' => false, 'availability' => $availability,
                'summary' => 'Tác phẩm độc bản, dáng '.mb_strtolower($shape).', bộ rễ và thân đã thuần, tán dày đều.',
                'description' => '<p>'.$name.' là tác phẩm được nghệ nhân chăm sóc và tạo dáng nhiều năm. Cây đã qua thuần dưỡng, sẵn sàng trưng bày.</p><p>Liên hệ để hẹn xem cây trực tiếp tại vườn hoặc nhận video chi tiết qua Zalo.</p>',
                'specs' => ['Dáng thế' => $shape, 'Chiều cao' => $height, 'Hoành tán' => $spread, 'Đường kính gốc' => $trunk, 'Tuổi cây' => $age, 'Xuất xứ' => 'Sưu tầm trong nước'],
                'is_featured' => $i < 6,
            ]);
            $this->attach($tree, 'gallery', ['gallery/'.(($i % 9) + 10).'.jpg', 'gallery/'.((($i + 4) % 9) + 10).'.jpg', 'gallery/'.((($i + 7) % 9) + 10).'.jpg']);
        }
    }

    /** @param  array<string, Service>  $services */
    private function projects(array $services): void
    {
        $types = collect(['Biệt thự', 'Văn phòng', 'Khu đô thị', 'Resort – khách sạn', 'Trường học'])
            ->mapWithKeys(fn (string $name, int $i) => [$name => ProjectType::create(['name' => $name, 'sort' => $i])]);

        $projects = [
            ['Sân vườn biệt thự Thảo Điền', 'Biệt thự', 'Quận 2, TP.HCM', 2025, '650 m²', ['tu-van', 'cung-cap', 'cham-soc']],
            ['Cây xanh văn phòng tòa nhà Landmark', 'Văn phòng', 'Bình Thạnh, TP.HCM', 2025, '3 tầng, 220 cây', ['cho-thue', 'cham-soc']],
            ['Cảnh quan khu đô thị Sala', 'Khu đô thị', 'Thủ Đức, TP.HCM', 2024, '12.000 m²', ['tu-van', 'cung-cap']],
            ['Resort ven biển Hồ Tràm', 'Resort – khách sạn', 'Bà Rịa – Vũng Tàu', 2024, '4,5 ha', ['tu-van', 'cung-cap', 'cham-soc']],
            ['Trường quốc tế Nam Sài Gòn', 'Trường học', 'Quận 7, TP.HCM', 2023, '8.000 m²', ['cung-cap', 'cham-soc']],
            ['Biệt thự vườn Phú Mỹ Hưng', 'Biệt thự', 'Quận 7, TP.HCM', 2023, '420 m²', ['tu-van', 'mua-ban']],
            ['Sảnh khách sạn 5 sao Quận 1', 'Resort – khách sạn', 'Quận 1, TP.HCM', 2025, 'Sảnh 600 m²', ['cho-thue']],
            ['Văn phòng startup Quận 3', 'Văn phòng', 'Quận 3, TP.HCM', 2026, '1 tầng, 60 cây', ['cho-thue', 'tu-van']],
        ];

        foreach ($projects as $i => [$name, $type, $location, $year, $scale, $serviceKeys]) {
            $project = Project::create([
                'project_type_id' => $types[$type]->id, 'name' => $name, 'client' => 'Chủ đầu tư '.($i + 1), 'location' => $location,
                'year' => $year, 'scale' => $scale, 'is_featured' => $i < 6,
                'summary' => 'Thi công và chăm sóc cây xanh cho '.mb_strtolower($type).' quy mô '.$scale.'.',
                'body' => '<p>Dự án '.$name.' được thực hiện trọn gói từ khảo sát, thiết kế tới cung cấp cây và chăm sóc định kỳ.</p><h3>Hạng mục</h3><ul><li>Bố trí cây theo ánh sáng từng khu vực.</li><li>Hệ thống tưới tự động.</li><li>Bàn giao kèm lịch chăm sóc.</li></ul>',
            ]);
            $project->services()->attach(collect($serviceKeys)->map(fn (string $key) => $services[$key]->id));
            $this->attach($project, 'cover', ['gallery/'.(($i % 9) + 20).'.jpg']);
            $this->attach($project, 'gallery', ['gallery/'.(($i % 9) + 20).'.jpg', 'gallery/'.((($i + 3) % 9) + 20).'.jpg', 'gallery/'.(($i % 6) + 1).'.jpg']);
        }
    }

    private function posts(): void
    {
        $categories = collect(['Kiến thức chăm cây', 'Phong thủy cây cảnh', 'Tin công trình'])
            ->mapWithKeys(fn (string $name, int $i) => [$name => PostCategory::create(['name' => $name, 'sort' => $i])]);

        $posts = [
            ['7 loại cây văn phòng dễ chăm, ít cần ánh sáng', 'Kiến thức chăm cây'],
            ['Cách tưới cây nội thất đúng cách mùa mưa', 'Kiến thức chăm cây'],
            ['Bón phân cho cây cảnh: bao nhiêu là đủ?', 'Kiến thức chăm cây'],
            ['Chọn cây phong thủy theo mệnh cho bàn làm việc', 'Phong thủy cây cảnh'],
            ['Vì sao Kim Tiền là cây được tặng nhiều nhất dịp khai trương', 'Phong thủy cây cảnh'],
            ['Thuê cây văn phòng hay mua đứt: bài toán chi phí', 'Kiến thức chăm cây'],
            ['Hoàn thành cảnh quan khu đô thị Sala giai đoạn 2', 'Tin công trình'],
            ['Bàn giao 220 cây xanh cho tòa nhà Landmark', 'Tin công trình'],
            ['Kinh nghiệm chơi tùng La Hán cho người mới', 'Kiến thức chăm cây'],
        ];

        foreach ($posts as $i => [$title, $category]) {
            $post = Post::create([
                'post_category_id' => $categories[$category]->id, 'title' => $title, 'published_at' => now()->subDays($i * 5 + 1),
                'excerpt' => 'Những lưu ý thực tế từ đội ngũ kỹ thuật giúp cây của bạn luôn xanh tốt và bền đẹp.',
                'body' => '<p>Bài viết chia sẻ kinh nghiệm thực tế của đội ngũ kỹ thuật sau nhiều năm chăm sóc cây cho văn phòng, biệt thự và công trình.</p><h2>Những điều cần biết</h2><p>Mỗi loại cây có nhu cầu ánh sáng, nước và dinh dưỡng khác nhau. Quan sát lá và đất là cách đơn giản nhất để biết cây cần gì.</p><h2>Lời khuyên</h2><ul><li>Không tưới quá nhiều – úng rễ là nguyên nhân hàng đầu khiến cây chết.</li><li>Lau lá định kỳ để cây quang hợp tốt.</li><li>Xoay chậu mỗi tuần để cây phát triển đều.</li></ul>',
            ]);
            $this->attach($post, 'cover', ['blog/'.(($i % 4) + 7).'.jpg']);
        }
    }

    /** @param  array<string, Service>  $services */
    private function extras(array $services): void
    {
        foreach ([
            ['Mang mảng xanh vào mọi công trình', 'Tư vấn – cung cấp – cho thuê – chăm sóc cây xanh trọn gói.', 'home/1.jpg'],
            ['Cây cảnh nghệ thuật độc bản', 'Bộ sưu tập tùng, sanh, mai, linh sam được tuyển chọn kỹ lưỡng.', 'home/2.jpg'],
            ['Cho thuê cây văn phòng từ 1,5 triệu/tháng', 'Thay cây miễn phí, chăm sóc định kỳ, không lo cây héo.', 'home/3.jpg'],
        ] as $i => [$title, $subtitle, $image]) {
            $slide = Slide::create(['title' => $title, 'subtitle' => $subtitle, 'button_text' => 'Nhận báo giá', 'button_url' => '/bao-gia', 'sort' => $i]);
            $this->attach($slide, 'image', [$image]);
        }

        foreach ([
            ['Anh Hoàng Nam', 'Giám đốc công ty phần mềm', 'Văn phòng xanh hơn hẳn, nhân viên thích lắm. Cây được thay định kỳ nên lúc nào cũng tươi.'],
            ['Chị Thu Hà', 'Chủ biệt thự Thảo Điền', 'Đội thiết kế rất có gu, sân vườn nhà tôi giờ là nơi cả nhà thích nhất.'],
            ['Anh Quốc Bảo', 'Người chơi cây cảnh', 'Cây tùng mua ở đây dáng đẹp, được tư vấn kỹ cách chăm. Rất hài lòng.'],
            ['Chị Mai Phương', 'Quản lý tòa nhà', 'Thi công đúng tiến độ, chăm sóc có báo cáo ảnh rõ ràng sau mỗi lượt.'],
            ['Anh Đức Trí', 'Chủ đầu tư resort', 'Phương án cảnh quan hợp khí hậu biển, cây sống tốt sau mùa gió.'],
            ['Chị Lan Anh', 'Chủ quán cà phê', 'Thuê cây cho quán rất tiện, giá hợp lý, nhân viên dễ thương.'],
        ] as $i => [$name, $role, $content]) {
            $testimonial = Testimonial::create(['name' => $name, 'role' => $role, 'content' => $content, 'rating' => 5, 'sort' => $i]);
            $this->attach($testimonial, 'avatar', ['about/'.(($i % 6) + 4).'.jpg']);
        }

        foreach ([
            ['Có khảo sát và báo giá miễn phí không?', 'Có. Chúng tôi khảo sát tận nơi và báo giá miễn phí trong khu vực TP.HCM và các tỉnh lân cận.', null],
            ['Cây thuê bị héo thì sao?', 'Trong gói thuê, cây kém chất lượng sẽ được thay miễn phí trong vòng 48 giờ.', 'cho-thue'],
            ['Thời gian thuê tối thiểu là bao lâu?', 'Tối thiểu 6 tháng cho văn phòng; sự kiện tính theo ngày.', 'cho-thue'],
            ['Chăm sóc định kỳ gồm những công việc gì?', 'Tưới, bón phân, cắt tỉa, vệ sinh lá, phòng trừ sâu bệnh và báo cáo ảnh sau mỗi lượt.', 'cham-soc'],
            ['Cây nghệ thuật có được xem trực tiếp không?', 'Có. Bạn có thể hẹn xem cây tại vườn hoặc nhận video chi tiết qua Zalo.', null],
            ['Website có thanh toán online không?', 'Không. Sau khi đặt hàng, nhân viên sẽ gọi xác nhận; bạn thanh toán khi nhận hàng hoặc chuyển khoản.', null],
            ['Thiết kế cảnh quan mất bao lâu?', 'Thông thường 5–10 ngày làm việc tùy quy mô công trình.', 'tu-van'],
            ['Có cung cấp cây số lượng lớn cho dự án không?', 'Có. Chúng tôi cung cấp cây công trình theo đúng quy cách hồ sơ thiết kế, kèm chứng từ.', 'cung-cap'],
        ] as $i => [$question, $answer, $serviceKey]) {
            Faq::create(['question' => $question, 'answer' => '<p>'.$answer.'</p>', 'service_id' => $serviceKey ? $services[$serviceKey]->id : null, 'sort' => $i]);
        }

        $about = Page::create([
            'key' => 'about',
            'title' => 'Về Cây Cảnh Xanh',
            'body' => '<p>Thành lập từ năm 2014, <strong>Cây Cảnh Xanh</strong> là đơn vị chuyên tư vấn, cung cấp, mua bán, cho thuê và chăm sóc cây xanh cho công trình, đồng thời sưu tầm những tác phẩm cây cảnh nghệ thuật độc bản.</p><h2>Sứ mệnh</h2><p>Mang mảng xanh bền vững vào từng không gian sống và làm việc.</p><h2>Năng lực</h2><ul><li>Vườn ươm 3 ha tại Củ Chi.</li><li>Đội ngũ 40 kỹ sư, kỹ thuật viên.</li><li>Hơn 350 công trình đã thực hiện.</li></ul>',
        ]);
        $this->attach($about, 'cover', ['about/3.jpg']);

        $technician = User::where('email', 'kythuat@caycanh.test')->first();
        CareContract::create([
            'customer_name' => 'Tòa nhà Landmark', 'phone' => '0281234567', 'address' => '208 Nguyễn Hữu Cảnh, Bình Thạnh',
            'starts_on' => today()->subWeeks(2), 'frequency' => CareFrequency::Weekly, 'default_technician_id' => $technician?->id,
        ]);
        CareContract::create([
            'customer_name' => 'Biệt thự Thảo Điền', 'phone' => '0909888777', 'address' => '15 Đường số 10, Thảo Điền, TP. Thủ Đức',
            'starts_on' => today(), 'frequency' => CareFrequency::Biweekly, 'default_technician_id' => $technician?->id,
        ]);
    }

    /** @param  array<int, string>  $files  paths relative to the template image folder */
    private function attach(HasMedia $model, string $collection, array $files): void
    {
        foreach ($files as $file) {
            $path = $this->images.'/'.$file;

            if (is_file($path)) {
                $model->addMedia($path)->preservingOriginal()->toMediaCollection($collection);
            }
        }
    }
}
