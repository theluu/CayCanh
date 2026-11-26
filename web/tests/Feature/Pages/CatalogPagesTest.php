<?php

use App\Enums\ArtAvailability;
use App\Models\Category;
use App\Models\Product;

it('lists only published standard products', function () {
    Product::factory()->count(2)->create();
    Product::factory()->unpublished()->create();
    Product::factory()->art()->create();

    $this->get('/san-pham')->assertOk()->assertInertia(fn ($page) => $page
        ->component('Products/Index')
        ->has('products.data', 2));
});

it('paginates products by twelve', function () {
    Product::factory()->count(14)->create();

    $this->get('/san-pham')->assertInertia(fn ($page) => $page->has('products.data', 12)->where('products.meta.last_page', 2));
});

it('filters products by category slug', function () {
    $category = Category::factory()->create();
    Product::factory()->count(2)->create(['category_id' => $category->id]);
    Product::factory()->create();

    $this->get('/san-pham/danh-muc/'.$category->slug)->assertOk()->assertInertia(fn ($page) => $page
        ->component('Products/Index')
        ->has('products.data', 2)
        ->where('category.slug', $category->slug));
});

it('filters products by price range', function () {
    Product::factory()->create(['price' => 300000]);
    Product::factory()->create(['price' => 1500000]);
    Product::factory()->create(['price' => 15000000]);

    $this->get('/san-pham?gia=500k-2tr')->assertInertia(fn ($page) => $page->has('products.data', 1)->where('filters.gia', '500k-2tr'));
    $this->get('/san-pham?gia=tren-10tr')->assertInertia(fn ($page) => $page->has('products.data', 1));
});

it('sorts products by price', function () {
    Product::factory()->create(['name' => 'Rẻ', 'price' => 100000]);
    Product::factory()->create(['name' => 'Đắt', 'price' => 900000]);

    $this->get('/san-pham?sap-xep=gia-giam')->assertInertia(fn ($page) => $page->where('products.data.0.name', 'Đắt'));
    $this->get('/san-pham?sap-xep=gia-tang')->assertInertia(fn ($page) => $page->where('products.data.0.name', 'Rẻ'));
});

it('lists only art trees and filters them by availability', function () {
    Product::factory()->art()->count(2)->create();
    Product::factory()->art()->create(['availability' => ArtAvailability::Sold]);
    Product::factory()->create();

    $this->get('/cay-canh-nghe-thuat')->assertOk()->assertInertia(fn ($page) => $page->component('Art/Index')->has('products.data', 3));
    $this->get('/cay-canh-nghe-thuat?tinh-trang=da-ban')->assertInertia(fn ($page) => $page->has('products.data', 1));
    $this->get('/cay-canh-nghe-thuat?tinh-trang=con-hang')->assertInertia(fn ($page) => $page->has('products.data', 2));
});

it('shows a product that can be added to the cart', function () {
    $product = Product::factory()->create(['price' => 450000]);

    $this->get('/san-pham/'.$product->slug)->assertOk()->assertInertia(fn ($page) => $page
        ->component('Products/Show')
        ->where('product.canAddToCart', true)
        ->where('product.price', '450.000 ₫')
        ->where('seo.jsonLd.0.@type', 'Product')
        ->where('seo.jsonLd.0.offers.price', 450000)
        ->has('related'));
});

it('does not serve art trees under the products path or vice versa', function () {
    $art = Product::factory()->art()->create();
    $product = Product::factory()->create();

    $this->get('/san-pham/'.$art->slug)->assertNotFound();
    $this->get('/cay-canh-nghe-thuat/'.$product->slug)->assertNotFound();
});

it('shows an art tree with quote form and no offer when price is hidden', function () {
    $art = Product::factory()->art()->create(['price' => 50000000, 'show_price' => false]);

    $this->get('/cay-canh-nghe-thuat/'.$art->slug)->assertOk()->assertInertia(fn ($page) => $page
        ->component('Art/Show')
        ->where('product.canQuote', true)
        ->where('product.canAddToCart', false)
        ->where('product.price', 'Giá liên hệ')
        ->missing('seo.jsonLd.0.offers'));
});

it('keeps sold art trees visible but closes quoting', function () {
    $art = Product::factory()->art()->create(['availability' => ArtAvailability::Sold]);

    $this->get('/cay-canh-nghe-thuat/'.$art->slug)->assertOk()->assertInertia(fn ($page) => $page
        ->where('product.canQuote', false)
        ->where('product.availability.label', 'Đã bán'));
});
