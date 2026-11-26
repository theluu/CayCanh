<?php

use App\Models\Faq;
use App\Models\Page;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Project;
use App\Models\ProjectType;
use App\Models\Redirect;
use App\Models\Service;
use App\Models\ServicePackage;
use App\Models\Testimonial;

it('renders the about page from the "about" page record', function () {
    Page::factory()->create(['key' => 'about', 'title' => 'Về chúng tôi', 'body' => '<p>Xin chào</p>']);

    $this->get('/gioi-thieu')->assertOk()->assertInertia(fn ($page) => $page
        ->component('About')
        ->where('page.title', 'Về chúng tôi')
        ->where('page.body', '<p>Xin chào</p>'));
});

it('lists published services', function () {
    Service::factory()->count(2)->create();
    Service::factory()->create(['is_published' => false]);

    $this->get('/dich-vu')->assertOk()->assertInertia(fn ($page) => $page->component('Services/Index')->has('services', 2));
});

it('shows a service with its packages, faqs and json-ld', function () {
    $service = Service::factory()->create(['name' => 'Cho thuê cây xanh']);
    ServicePackage::factory()->count(2)->for($service)->create();
    Faq::factory()->create(['service_id' => $service->id]);

    $this->get('/dich-vu/'.$service->slug)->assertOk()->assertInertia(fn ($page) => $page
        ->component('Services/Show')
        ->where('service.name', 'Cho thuê cây xanh')
        ->has('service.packages', 2)
        ->has('service.faqs', 1)
        ->where('seo.jsonLd.0.@type', 'Service')
        ->where('seo.jsonLd.1.@type', 'BreadcrumbList'));
});

it('returns 404 for unpublished content', function (string $class, string $prefix) {
    $model = $class::factory()->create(['is_published' => false]);

    $this->get($prefix.'/'.$model->slug)->assertNotFound();
})->with([[Service::class, '/dich-vu'], [Project::class, '/du-an'], [Post::class, '/tin-tuc']]);

it('renders the price page grouped by service', function () {
    $service = Service::factory()->create();
    ServicePackage::factory()->count(3)->for($service)->create();

    $this->get('/bang-gia')->assertOk()->assertInertia(fn ($page) => $page
        ->component('Price')
        ->has('services', 1)
        ->has('services.0.packages', 3));
});

it('filters projects by type', function () {
    $villa = ProjectType::factory()->create(['name' => 'Biệt thự']);
    Project::factory()->count(2)->create(['project_type_id' => $villa->id]);
    Project::factory()->create();

    $this->get('/du-an?loai='.$villa->slug)->assertOk()->assertInertia(fn ($page) => $page
        ->component('Projects/Index')
        ->has('projects.data', 2)
        ->where('filters.loai', $villa->slug)
        ->has('types', 2));
});

it('shows a project with its gallery and related services', function () {
    $project = Project::factory()->create();
    $project->services()->attach(Service::factory()->create());

    $this->get('/du-an/'.$project->slug)->assertOk()->assertInertia(fn ($page) => $page
        ->component('Projects/Show')
        ->where('project.name', $project->name)
        ->has('project.services', 1)
        ->has('project.gallery'));
});

it('paginates posts by nine', function () {
    Post::factory()->count(11)->create();

    $this->get('/tin-tuc')->assertOk()->assertInertia(fn ($page) => $page
        ->component('Posts/Index')
        ->has('posts.data', 9)
        ->where('posts.meta.last_page', 2));
});

it('filters posts by category', function () {
    $category = PostCategory::factory()->create();
    Post::factory()->count(2)->for($category, 'category')->create();
    Post::factory()->create();

    $this->get('/tin-tuc/danh-muc/'.$category->slug)->assertOk()->assertInertia(fn ($page) => $page
        ->component('Posts/Index')
        ->has('posts.data', 2)
        ->where('category.slug', $category->slug));
});

it('shows a post with article json-ld', function () {
    $post = Post::factory()->create();

    $this->get('/tin-tuc/'.$post->slug)->assertOk()->assertInertia(fn ($page) => $page
        ->component('Posts/Show')
        ->where('post.title', $post->title)
        ->where('seo.jsonLd.0.@type', 'Article')
        ->has('related'));
});

it('renders testimonials, faq and contact pages', function () {
    Testimonial::factory()->count(2)->create();
    Faq::factory()->count(3)->create();

    $this->get('/danh-gia-khach-hang')->assertOk()->assertInertia(fn ($page) => $page->component('Testimonials')->has('testimonials', 2));
    $this->get('/cau-hoi-thuong-gap')->assertOk()->assertInertia(fn ($page) => $page
        ->component('Faq')->has('faqs', 3)->where('seo.jsonLd.0.@type', 'FAQPage'));
    $this->get('/lien-he')->assertOk()->assertInertia(fn ($page) => $page->component('Contact'));
});

it('renders the inertia error page for unknown urls', function () {
    $this->get('/khong-ton-tai')->assertNotFound()->assertInertia(fn ($page) => $page
        ->component('Error')
        ->where('status', 404)
        ->has('site.site_name')
        ->has('menu.services'));
});

it('follows recorded 301 redirects', function () {
    Redirect::create(['from_path' => '/du-an/cu', 'to_path' => '/du-an/moi']);

    $this->get('/du-an/cu')->assertRedirect('/du-an/moi')->assertStatus(301);
});
