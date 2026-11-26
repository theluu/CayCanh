<?php

namespace App\Support;

use App\Models\Faq;
use App\Models\Post;
use App\Models\Product;
use App\Models\Service;
use App\Settings\GeneralSettings;
use Illuminate\Support\Collection;

class JsonLd
{
    /** @return array<string, mixed> */
    public static function localBusiness(GeneralSettings $settings): array
    {
        return self::clean([
            '@context' => 'https://schema.org',
            '@type' => 'LocalBusiness',
            'name' => $settings->site_name,
            'url' => url('/'),
            'telephone' => $settings->hotline,
            'email' => $settings->email,
            'address' => $settings->address ? [
                '@type' => 'PostalAddress',
                'streetAddress' => $settings->address,
                'addressCountry' => 'VN',
            ] : null,
            'openingHours' => $settings->working_hours,
            'sameAs' => array_values(array_filter([$settings->facebook_url, $settings->tiktok_url, $settings->youtube_url])),
        ]);
    }

    /** @return array<string, mixed> */
    public static function service(Service $service, GeneralSettings $settings): array
    {
        return self::clean([
            '@context' => 'https://schema.org',
            '@type' => 'Service',
            'name' => $service->name,
            'description' => $service->seoDescription(),
            'url' => url($service->publicPath()),
            'image' => $service->imageUrls('cover')['large'] ?? null,
            'areaServed' => 'VN',
            'provider' => ['@type' => 'LocalBusiness', 'name' => $settings->site_name, 'telephone' => $settings->hotline],
            'offers' => $service->packages->filter(fn ($package) => $package->price_from)->map(fn ($package) => [
                '@type' => 'Offer',
                'name' => $package->name,
                'price' => $package->price_from,
                'priceCurrency' => 'VND',
            ])->values()->all(),
        ]);
    }

    /** @return array<string, mixed> */
    public static function product(Product $product): array
    {
        $price = $product->effectivePrice();
        $showOffer = $product->show_price && $price;

        return self::clean([
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product->name,
            'description' => $product->seoDescription(),
            'sku' => $product->sku,
            'url' => url($product->publicPath()),
            'image' => array_column($product->galleryUrls(), 'large'),
            'category' => $product->category?->name,
            'offers' => $showOffer ? [
                '@type' => 'Offer',
                'price' => $price,
                'priceCurrency' => 'VND',
                'availability' => $product->isArt()
                    ? ($product->availability->value === 'sold' ? 'https://schema.org/SoldOut' : 'https://schema.org/InStock')
                    : ($product->in_stock ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock'),
                'url' => url($product->publicPath()),
            ] : null,
        ]);
    }

    /** @return array<string, mixed> */
    public static function article(Post $post, GeneralSettings $settings): array
    {
        return self::clean([
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $post->title,
            'description' => $post->seoDescription(),
            'image' => $post->imageUrls('cover')['large'] ?? null,
            'datePublished' => $post->published_at?->toIso8601String(),
            'dateModified' => $post->updated_at?->toIso8601String(),
            'mainEntityOfPage' => url($post->publicPath()),
            'author' => ['@type' => 'Organization', 'name' => $settings->site_name],
            'publisher' => ['@type' => 'Organization', 'name' => $settings->site_name],
        ]);
    }

    /**
     * @param  Collection<int, Faq>  $faqs
     * @return array<string, mixed>
     */
    public static function faqPage(Collection $faqs): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $faqs->map(fn ($faq) => [
                '@type' => 'Question',
                'name' => $faq->question,
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => strip_tags($faq->answer)],
            ])->values()->all(),
        ];
    }

    /**
     * @param  array<string, string|null>  $crumbs  label => path (null for current page)
     * @return array<string, mixed>
     */
    public static function breadcrumbs(array $crumbs): array
    {
        $items = [];
        $position = 1;

        foreach (['Trang chủ' => '/'] + $crumbs as $label => $path) {
            $items[] = self::clean([
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => $label,
                'item' => $path ? url($path) : url()->current(),
            ]);
        }

        return ['@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => $items];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private static function clean(array $data): array
    {
        return array_filter($data, fn ($value) => $value !== null && $value !== [] && $value !== '');
    }
}
