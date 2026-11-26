<?php

use App\Models\Product;
use App\Models\Service;
use App\Settings\GeneralSettings;
use App\Support\SiteCache;

it('serves cached values until site content changes', function () {
    $calls = 0;
    $read = function () use (&$calls) {
        return SiteCache::remember('demo', function () use (&$calls) {
            return ++$calls;
        });
    };

    expect($read())->toBe(1)->and($read())->toBe(1);

    SiteCache::flush();

    expect($read())->toBe(2);
});

it('refreshes the services menu right after a service is renamed', function () {
    $service = Service::factory()->create(['name' => 'Tên cũ']);
    $this->get('/')->assertInertia(fn ($page) => $page->where('menu.services.0.name', 'Tên cũ'));

    $service->update(['name' => 'Tên mới']);

    $this->get('/')->assertInertia(fn ($page) => $page->where('menu.services.0.name', 'Tên mới'));
});

it('refreshes home page products after a product is unpublished', function () {
    $product = Product::factory()->create();
    $this->get('/')->assertInertia(fn ($page) => $page->has('products', 1));

    $product->update(['is_published' => false]);

    $this->get('/')->assertInertia(fn ($page) => $page->has('products', 0));
});

it('refreshes shared site settings after they are saved', function () {
    $this->get('/');

    $settings = app(GeneralSettings::class);
    $settings->hotline = '0909000111';
    $settings->save();

    $this->get('/')->assertInertia(fn ($page) => $page->where('site.hotline', '0909000111'));
});
