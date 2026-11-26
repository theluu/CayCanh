<?php

use App\Models\Post;
use App\Models\Product;
use App\Models\Project;
use App\Models\Service;
use App\Models\Slide;
use App\Models\Testimonial;

it('renders the home page with every section', function () {
    Slide::factory()->count(2)->create();
    Slide::factory()->create(['is_active' => false]);
    Service::factory()->count(5)->create();
    Service::factory()->create(['is_published' => false]);
    Product::factory()->art()->featured()->count(2)->create();
    Product::factory()->art()->featured()->unpublished()->create();
    Product::factory()->count(3)->create();
    Project::factory()->count(2)->create(['is_featured' => true]);
    Testimonial::factory()->count(2)->create();
    Post::factory()->count(4)->create();
    Post::factory()->create(['published_at' => now()->addWeek()]);

    $this->get('/')->assertOk()->assertInertia(fn ($page) => $page
        ->component('Home')
        ->has('slides', 2)
        ->has('services', 5)
        ->has('featuredArt', 2)
        ->has('featuredProjects', 2)
        ->has('products', 3)
        ->has('testimonials', 2)
        ->has('posts', 3)
        ->has('stats')
        ->where('seo.jsonLd.0.@type', 'LocalBusiness')
        ->where('featuredArt.0.price', 'Giá liên hệ'));
});

it('prints JSON-LD into the server-rendered html', function () {
    $this->get('/')->assertSee('<script type="application/ld+json">', false)
        ->assertSee('"@type":"LocalBusiness"', false);
});

it('shares the services menu and cart count with every page', function () {
    Service::factory()->create(['name' => 'Cho thuê cây xanh']);

    $this->get('/')->assertInertia(fn ($page) => $page
        ->where('menu.services.0.name', 'Cho thuê cây xanh')
        ->where('cart.count', 0));
});
