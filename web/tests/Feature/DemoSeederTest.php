<?php

use App\Models\Post;
use App\Models\Product;
use App\Models\Project;
use App\Models\Service;
use Database\Seeders\DemoSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Support\Facades\Queue;

it('seeds a complete demo site that renders', function () {
    Queue::fake(); // image conversions are queued; generating them inline takes minutes
    $this->seed([RoleSeeder::class, DemoSeeder::class]);

    expect(Service::count())->toBe(5)
        ->and(Product::standard()->count())->toBe(16)
        ->and(Product::art()->count())->toBe(10)
        ->and(Project::count())->toBe(8)
        ->and(Post::count())->toBe(9);

    $this->get('/')->assertOk()->assertInertia(fn ($page) => $page->has('services', 5)->has('slides', 3));
    $this->get('/cay-canh-nghe-thuat/'.Product::art()->first()->slug)->assertOk();
    $this->get('/bang-gia')->assertOk()->assertInertia(fn ($page) => $page->has('services', 4));
});
