<?php

use App\Jobs\RegenerateSitemap;
use App\Models\Post;
use App\Models\Product;
use App\Models\Project;
use App\Models\Service;
use Illuminate\Support\Facades\Queue;

beforeEach(function () {
    $this->sitemap = sys_get_temp_dir().'/sitemap-test.xml';
    $this->robots = sys_get_temp_dir().'/robots-test.txt';
    @unlink($this->sitemap);
    @unlink($this->robots);
});

it('lists static pages and published records only', function () {
    Queue::fake();
    $product = Product::factory()->create();
    $draft = Product::factory()->unpublished()->create();
    $art = Product::factory()->art()->create();
    $service = Service::factory()->create();
    $project = Project::factory()->create();
    $post = Post::factory()->create();
    $scheduled = Post::factory()->create(['published_at' => now()->addWeek()]);

    $this->artisan('sitemap:generate', ['--path' => $this->sitemap, '--robots' => $this->robots])->assertSuccessful();

    $xml = file_get_contents($this->sitemap);
    expect($xml)
        ->toContain(url('/'))
        ->toContain(url('/bang-gia'))
        ->toContain(url('/san-pham/'.$product->slug))
        ->toContain(url('/cay-canh-nghe-thuat/'.$art->slug))
        ->toContain(url('/dich-vu/'.$service->slug))
        ->toContain(url('/du-an/'.$project->slug))
        ->toContain(url('/tin-tuc/'.$post->slug))
        ->not->toContain('/san-pham/'.$draft->slug)
        ->not->toContain('/tin-tuc/'.$scheduled->slug);
});

it('writes a robots.txt that points to the sitemap and hides private paths', function () {
    $this->artisan('sitemap:generate', ['--path' => $this->sitemap, '--robots' => $this->robots])->assertSuccessful();

    expect(file_get_contents($this->robots))
        ->toContain('Sitemap: '.url('/sitemap.xml'))
        ->toContain('Disallow: /admin')
        ->toContain('Disallow: /gio-hang');
});

it('queues a sitemap rebuild when public content is published', function () {
    Queue::fake();

    Product::factory()->create();
    Post::factory()->create(['is_published' => false]);

    Queue::assertPushed(RegenerateSitemap::class, 1);
});
