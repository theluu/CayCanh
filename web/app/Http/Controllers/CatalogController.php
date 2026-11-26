<?php

namespace App\Http\Controllers;

use App\Enums\ArtAvailability;
use App\Enums\ProductKind;
use App\Http\Resources\ProductCard;
use App\Models\Category;
use App\Models\Product;
use App\Support\JsonLd;
use App\Support\Money;
use App\Support\Seo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Listing and detail pages shared by standard products (/san-pham) and art trees (/cay-canh-nghe-thuat).
 */
class CatalogController extends Controller
{
    /** @var array<string, array{0: int, 1: int|null}> */
    public const PRICE_RANGES = [
        'duoi-500k' => [0, 499999],
        '500k-2tr' => [500000, 2000000],
        '2tr-10tr' => [2000001, 10000000],
        'tren-10tr' => [10000001, null],
    ];

    public function products(Request $request, ?string $categorySlug = null): Response
    {
        return $this->listing(ProductKind::Standard, $request, $categorySlug);
    }

    public function art(Request $request, ?string $categorySlug = null): Response
    {
        return $this->listing(ProductKind::Art, $request, $categorySlug);
    }

    public function showProduct(string $slug): Response
    {
        return $this->detail(ProductKind::Standard, $slug);
    }

    public function showArt(string $slug): Response
    {
        return $this->detail(ProductKind::Art, $slug);
    }

    private function listing(ProductKind $kind, Request $request, ?string $categorySlug): Response
    {
        $isArt = $kind === ProductKind::Art;
        $base = $isArt ? '/cay-canh-nghe-thuat' : '/san-pham';
        $category = $categorySlug ? Category::forKind($kind)->where('slug', $categorySlug)->firstOrFail() : null;

        $filters = [
            'gia' => array_key_exists($request->query('gia'), self::PRICE_RANGES) ? $request->query('gia') : null,
            'sap-xep' => in_array($request->query('sap-xep'), ['moi', 'gia-tang', 'gia-giam'], true) ? $request->query('sap-xep') : 'moi',
            'tinh-trang' => in_array($request->query('tinh-trang'), ['con-hang', 'da-ban'], true) ? $request->query('tinh-trang') : null,
            'q' => trim((string) $request->query('q')) ?: null,
        ];

        $products = Product::published()
            ->where('kind', $kind)
            ->with('media')
            ->when($category, fn (Builder $query) => $query->whereIn('category_id', $category->children()->pluck('id')->push($category->id)))
            ->when($filters['q'], fn (Builder $query, string $q) => $query->where('name', 'like', '%'.$q.'%'))
            ->when($filters['gia'], function (Builder $query, string $range) {
                [$min, $max] = self::PRICE_RANGES[$range];
                $query->where('price', '>=', $min)->when($max, fn (Builder $q) => $q->where('price', '<=', $max));
            })
            ->when($isArt && $filters['tinh-trang'] === 'da-ban', fn (Builder $query) => $query->where('availability', ArtAvailability::Sold))
            ->when($isArt && $filters['tinh-trang'] === 'con-hang', fn (Builder $query) => $query->where('availability', '!=', ArtAvailability::Sold))
            ->when($filters['sap-xep'] === 'gia-tang', fn (Builder $query) => $query->orderByRaw('price is null')->orderBy('price'))
            ->when($filters['sap-xep'] === 'gia-giam', fn (Builder $query) => $query->orderByDesc('price'))
            ->when($filters['sap-xep'] === 'moi', fn (Builder $query) => $query->orderByDesc('is_featured')->latest())
            ->paginate(12)
            ->withQueryString();

        $title = $category?->name ?? ($isArt ? 'Cây cảnh nghệ thuật' : 'Sản phẩm');
        $crumbs = $category ? [($isArt ? 'Cây cảnh nghệ thuật' : 'Sản phẩm') => $base, $category->name => null] : [$title => null];

        return Inertia::render($isArt ? 'Art/Index' : 'Products/Index', [
            'seo' => Seo::make(
                $title,
                $isArt ? 'Bộ sưu tập cây cảnh nghệ thuật độc bản: tùng, sanh, mai chiếu thủy, linh sam… liên hệ để xem cây và nhận báo giá.'
                    : 'Mua cây cảnh, cây văn phòng, chậu và vật tư trồng cây online, giao tận nơi.',
                jsonLd: [JsonLd::breadcrumbs($crumbs)],
            ),
            'products' => ProductCard::collection($products)->response()->getData(true),
            'category' => $category?->only(['name', 'slug', 'description']),
            'categories' => Category::forKind($kind)->visible()->whereNull('parent_id')
                ->withCount(['products' => fn ($query) => $query->published()])
                ->get()
                ->map(fn (Category $item) => ['name' => $item->name, 'slug' => $item->slug, 'url' => $base.'/danh-muc/'.$item->slug, 'count' => $item->products_count])
                ->all(),
            'priceRanges' => $isArt ? [] : [
                ['value' => 'duoi-500k', 'label' => 'Dưới 500.000 ₫'],
                ['value' => '500k-2tr', 'label' => '500.000 ₫ – 2.000.000 ₫'],
                ['value' => '2tr-10tr', 'label' => '2.000.000 ₫ – 10.000.000 ₫'],
                ['value' => 'tren-10tr', 'label' => 'Trên 10.000.000 ₫'],
            ],
            'filters' => $filters,
            'basePath' => $category ? $base.'/danh-muc/'.$category->slug : $base,
        ]);
    }

    private function detail(ProductKind $kind, string $slug): Response
    {
        $product = Product::published()->where('kind', $kind)->where('slug', $slug)->with(['media', 'category'])->firstOrFail();
        $isArt = $kind === ProductKind::Art;
        $base = $isArt ? '/cay-canh-nghe-thuat' : '/san-pham';
        $price = $product->effectivePrice();

        $crumbs = [($isArt ? 'Cây cảnh nghệ thuật' : 'Sản phẩm') => $base];
        if ($product->category) {
            $crumbs[$product->category->name] = $base.'/danh-muc/'.$product->category->slug;
        }
        $crumbs[$product->name] = null;

        $gallery = $product->galleryUrls();

        return Inertia::render($isArt ? 'Art/Show' : 'Products/Show', [
            'seo' => Seo::make($product->seoTitle(), $product->seoDescription(), $gallery[0]['large'] ?? null, [
                JsonLd::product($product),
                JsonLd::breadcrumbs($crumbs),
            ], 'product'),
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'url' => $product->publicPath(),
                'sku' => $product->sku,
                'price' => $product->displayPrice(),
                'originalPrice' => $product->show_price && $price !== null && $price < (int) $product->price ? Money::vnd((int) $product->price) : null,
                'canAddToCart' => $product->isPurchasable(),
                'canQuote' => $isArt ? $product->availability !== ArtAvailability::Sold : ! $product->isPurchasable(),
                'inStock' => $product->in_stock,
                'availability' => $isArt ? ['value' => $product->availability->value, 'label' => $product->availability->label()] : null,
                'summary' => $product->summary,
                'description' => $product->description,
                'specs' => collect($product->specs ?? [])->filter(fn ($value) => filled($value))->map(fn ($value, $label) => ['label' => $label, 'value' => $value])->values()->all(),
                'gallery' => $gallery,
                'videoUrl' => $product->video_url,
                'category' => $product->category ? ['name' => $product->category->name, 'url' => $base.'/danh-muc/'.$product->category->slug] : null,
            ],
            'related' => ProductCard::collection(
                Product::published()->where('kind', $kind)->whereKeyNot($product->id)
                    ->when($product->category_id, fn (Builder $query) => $query->where('category_id', $product->category_id))
                    ->with('media')->inRandomOrder()->limit(4)->get()
            )->resolve(),
        ]);
    }
}
