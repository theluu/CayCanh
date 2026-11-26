<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostCard;
use App\Http\Resources\ProductCard;
use App\Http\Resources\ProjectCard;
use App\Http\Resources\ServiceCard;
use App\Http\Resources\SlideData;
use App\Http\Resources\TestimonialData;
use App\Models\Partner;
use App\Models\Post;
use App\Models\Product;
use App\Models\Project;
use App\Models\Service;
use App\Models\Slide;
use App\Models\Testimonial;
use App\Settings\GeneralSettings;
use App\Support\JsonLd;
use App\Support\Seo;
use App\Support\SiteCache;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function __invoke(GeneralSettings $settings): Response
    {
        return Inertia::render('Home', [
            'seo' => Seo::make(
                title: $settings->site_name.' – Cây xanh công trình và cây cảnh nghệ thuật',
                description: 'Tư vấn, cung cấp, mua bán, cho thuê và chăm sóc cây xanh công trình; cây cảnh nghệ thuật độc bản.',
                jsonLd: [JsonLd::localBusiness($settings)],
            ),
            'slides' => SiteCache::remember('home.slides', fn () => SlideData::collection(Slide::active()->with('media')->get())->resolve()),
            'stats' => $settings->stats,
            'services' => SiteCache::remember('home.services', fn () => ServiceCard::collection(Service::published()->ordered()->with('media')->limit(6)->get())->resolve()),
            'featuredArt' => SiteCache::remember('home.art', fn () => ProductCard::collection(
                Product::published()->art()->where('is_featured', true)->with('media')->latest()->limit(8)->get()
            )->resolve()),
            'featuredProjects' => SiteCache::remember('home.projects', fn () => ProjectCard::collection(
                Project::published()->where('is_featured', true)->with(['media', 'type'])->latest()->limit(6)->get()
            )->resolve()),
            'products' => SiteCache::remember('home.products', fn () => ProductCard::collection(
                Product::published()->standard()->with('media')->orderByDesc('is_featured')->latest()->limit(8)->get()
            )->resolve()),
            'testimonials' => SiteCache::remember('home.testimonials', fn () => TestimonialData::collection(Testimonial::published()->ordered()->with('media')->get())->resolve()),
            'posts' => SiteCache::remember('home.posts', fn () => PostCard::collection(Post::published()->with(['media', 'category'])->latest('published_at')->limit(3)->get())->resolve()),
            'partners' => SiteCache::remember('home.partners', fn () => Partner::query()->orderBy('sort')->with('media')->get()
                ->map(fn (Partner $partner) => ['name' => $partner->name, 'url' => $partner->url, 'logo' => $partner->imageUrls('logo')])
                ->all()),
        ]);
    }
}
