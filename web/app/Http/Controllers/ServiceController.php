<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProjectCard;
use App\Http\Resources\ServiceCard;
use App\Models\Faq;
use App\Models\Service;
use App\Models\ServicePackage;
use App\Settings\GeneralSettings;
use App\Support\JsonLd;
use App\Support\Seo;
use Inertia\Inertia;
use Inertia\Response;

class ServiceController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Services/Index', [
            'seo' => Seo::make('Dịch vụ cây xanh công trình', 'Tư vấn thiết kế, cung cấp, mua bán, cho thuê và chăm sóc cây xanh cho công trình.', jsonLd: [
                JsonLd::breadcrumbs(['Dịch vụ' => null]),
            ]),
            'services' => ServiceCard::collection(Service::published()->ordered()->with('media')->get())->resolve(),
        ]);
    }

    public function show(string $slug, GeneralSettings $settings): Response
    {
        $service = Service::published()->where('slug', $slug)
            ->with(['media', 'packages', 'faqs' => fn ($query) => $query->published()->ordered(), 'projects' => fn ($query) => $query->published()->with(['media', 'type'])->latest()->limit(6)])
            ->firstOrFail();

        return Inertia::render('Services/Show', [
            'seo' => Seo::make($service->seoTitle(), $service->seoDescription(), $service->imageUrls('cover')['large'] ?? null, [
                JsonLd::service($service, $settings),
                JsonLd::breadcrumbs(['Dịch vụ' => '/dich-vu', $service->name => null]),
            ]),
            'service' => [
                'id' => $service->id,
                'name' => $service->name,
                'summary' => $service->summary,
                'body' => $service->body,
                'image' => $service->imageUrls('cover'),
                'gallery' => $service->galleryUrls(),
                'packages' => $service->packages->map(fn (ServicePackage $package) => [
                    'id' => $package->id,
                    'name' => $package->name,
                    'price' => $package->displayPrice(),
                    'unit' => $package->unit,
                    'features' => $package->features ?? [],
                    'highlighted' => $package->is_highlighted,
                ])->all(),
                'faqs' => $service->faqs->map(fn (Faq $faq) => $faq->only(['id', 'question', 'answer']))->all(),
                'projects' => ProjectCard::collection($service->projects)->resolve(),
            ],
            'otherServices' => Service::published()->ordered()->whereKeyNot($service->id)->get(['name', 'slug'])
                ->map(fn (Service $other) => ['name' => $other->name, 'url' => $other->publicPath()])->all(),
        ]);
    }
}
