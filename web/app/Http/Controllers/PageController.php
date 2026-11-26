<?php

namespace App\Http\Controllers;

use App\Http\Resources\TestimonialData;
use App\Models\Faq;
use App\Models\Page;
use App\Models\Partner;
use App\Models\Testimonial;
use App\Settings\GeneralSettings;
use App\Support\JsonLd;
use App\Support\Seo;
use Inertia\Inertia;
use Inertia\Response;

class PageController extends Controller
{
    public function about(GeneralSettings $settings): Response
    {
        $page = Page::with('media')->firstWhere('key', 'about');

        return Inertia::render('About', [
            'seo' => Seo::make($page?->seoTitle() ?? 'Giới thiệu', $page?->seoDescription() ?? '', jsonLd: [
                JsonLd::localBusiness($settings),
                JsonLd::breadcrumbs(['Giới thiệu' => null]),
            ]),
            'page' => $page ? ['title' => $page->title, 'body' => $page->body, 'image' => $page->imageUrls('cover')] : null,
            'stats' => $settings->stats,
            'testimonials' => TestimonialData::collection(Testimonial::published()->ordered()->with('media')->limit(4)->get())->resolve(),
            'partners' => Partner::query()->orderBy('sort')->with('media')->get()
                ->map(fn (Partner $partner) => ['name' => $partner->name, 'url' => $partner->url, 'logo' => $partner->imageUrls('logo')])->all(),
        ]);
    }

    public function testimonials(): Response
    {
        return Inertia::render('Testimonials', [
            'seo' => Seo::make('Đánh giá của khách hàng', 'Khách hàng nói gì về dịch vụ cây xanh của chúng tôi.', jsonLd: [
                JsonLd::breadcrumbs(['Đánh giá khách hàng' => null]),
            ]),
            'testimonials' => TestimonialData::collection(Testimonial::published()->ordered()->with('media')->get())->resolve(),
        ]);
    }

    public function faq(): Response
    {
        $faqs = Faq::published()->ordered()->with('service:id,name')->get();

        return Inertia::render('Faq', [
            'seo' => Seo::make('Câu hỏi thường gặp', 'Giải đáp thắc mắc về mua, thuê và chăm sóc cây xanh.', jsonLd: [
                JsonLd::faqPage($faqs),
                JsonLd::breadcrumbs(['Câu hỏi thường gặp' => null]),
            ]),
            'faqs' => $faqs->map(fn (Faq $faq) => [
                'id' => $faq->id,
                'question' => $faq->question,
                'answer' => $faq->answer,
                'group' => $faq->service?->name ?? 'Câu hỏi chung',
            ])->all(),
        ]);
    }

    public function contact(GeneralSettings $settings): Response
    {
        return Inertia::render('Contact', [
            'seo' => Seo::make('Liên hệ', 'Liên hệ tư vấn, báo giá cây xanh công trình và cây cảnh nghệ thuật.', jsonLd: [
                JsonLd::localBusiness($settings),
                JsonLd::breadcrumbs(['Liên hệ' => null]),
            ]),
            'mapEmbedUrl' => $settings->map_embed_url,
        ]);
    }
}
