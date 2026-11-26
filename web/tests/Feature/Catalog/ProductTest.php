<?php

use App\Enums\ArtAvailability;
use App\Enums\ProductKind;
use App\Models\Product;
use App\Models\Redirect;
use App\Support\Money;

it('formats VND amounts', function () {
    expect(Money::vnd(1250000))->toBe('1.250.000 ₫');
});

it('never treats art trees as purchasable', function () {
    $art = Product::factory()->art()->create(['price' => 5000000]);

    expect($art->isPurchasable())->toBeFalse();
});

it('does not sell unpublished or unpriced products', function () {
    expect(Product::factory()->create(['is_published' => false])->isPurchasable())->toBeFalse()
        ->and(Product::factory()->create(['price' => null])->isPurchasable())->toBeFalse()
        ->and(Product::factory()->create(['in_stock' => false])->isPurchasable())->toBeFalse()
        ->and(Product::factory()->create(['price' => 300000])->isPurchasable())->toBeTrue();
});

it('uses the sale price when it is lower', function () {
    $product = Product::factory()->create(['price' => 500000, 'sale_price' => 400000]);

    expect($product->effectivePrice())->toBe(400000);
});

it('shows "Giá liên hệ" for art trees with hidden prices', function () {
    $art = Product::factory()->art()->create(['price' => 9000000, 'show_price' => false]);

    expect($art->displayPrice())->toBe('Giá liên hệ');
});

it('shows the formatted price when it is visible', function () {
    $product = Product::factory()->create(['price' => 1250000, 'sale_price' => null]);

    expect($product->displayPrice())->toBe('1.250.000 ₫');
});

it('generates unique slugs from vietnamese names', function () {
    $first = Product::factory()->create(['name' => 'Cây Bàng Đài Loan']);
    $second = Product::factory()->create(['name' => 'Cây Bàng Đài Loan']);

    expect($first->slug)->toBe('cay-bang-dai-loan')
        ->and($second->slug)->toBe('cay-bang-dai-loan-2');
});

it('records a 301 redirect when a product slug changes', function () {
    $product = Product::factory()->create(['name' => 'Cây cũ']);

    $product->update(['slug' => 'cay-moi']);

    expect(Redirect::where('from_path', '/san-pham/cay-cu')->value('to_path'))->toBe('/san-pham/cay-moi');
});

it('records art redirects under the art path', function () {
    $art = Product::factory()->art()->create(['name' => 'Tùng La Hán']);

    $art->update(['slug' => 'tung-la-han-dang-truc']);

    expect(Redirect::where('from_path', '/cay-canh-nghe-thuat/tung-la-han')->value('to_path'))
        ->toBe('/cay-canh-nghe-thuat/tung-la-han-dang-truc');
});

it('defaults art trees to available with hidden price', function () {
    $art = Product::factory()->art()->create();

    expect($art->kind)->toBe(ProductKind::Art)
        ->and($art->availability)->toBe(ArtAvailability::Available)
        ->and($art->show_price)->toBeFalse();
});

it('falls back to the name for the SEO title', function () {
    $product = Product::factory()->create(['name' => 'Chậu gốm', 'seo_title' => null]);

    expect($product->seoTitle())->toBe('Chậu gốm');
});
