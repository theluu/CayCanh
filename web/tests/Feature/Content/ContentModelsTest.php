<?php

use App\Enums\Role;
use App\Models\Post;
use App\Models\Project;
use App\Models\Redirect;
use App\Models\Service;
use App\Models\User;
use Database\Seeders\RoleSeeder;

it('orders published services by their sort value', function () {
    Service::factory()->create(['name' => 'B', 'sort' => 2]);
    Service::factory()->create(['name' => 'A', 'sort' => 1]);
    Service::factory()->create(['name' => 'Ẩn', 'sort' => 0, 'is_published' => false]);

    expect(Service::published()->ordered()->pluck('name')->all())->toBe(['A', 'B']);
});

it('keeps scheduled posts out of the published scope', function () {
    Post::factory()->create(['title' => 'Đã đăng', 'published_at' => now()->subDay()]);
    Post::factory()->create(['title' => 'Hẹn giờ', 'published_at' => now()->addDay()]);

    expect(Post::published()->pluck('title')->all())->toBe(['Đã đăng']);
});

it('records redirects under each public path prefix', function (string $class, string $prefix) {
    $model = $class::factory()->create(['slug' => 'cu']);
    $model->update(['slug' => 'moi']);

    expect(Redirect::where('from_path', "{$prefix}/cu")->value('to_path'))->toBe("{$prefix}/moi");
})->with([
    [Service::class, '/dich-vu'],
    [Project::class, '/du-an'],
    [Post::class, '/tin-tuc'],
]);

it('lets editors open every content resource', function (string $url) {
    $this->seed(RoleSeeder::class);
    $editor = tap(User::factory()->create())->assignRole(Role::Editor->value);

    $this->actingAs($editor)->get($url)->assertOk();
})->with([
    '/admin/services', '/admin/service-packages', '/admin/projects', '/admin/project-types', '/admin/posts',
    '/admin/post-categories', '/admin/testimonials', '/admin/faqs', '/admin/slides', '/admin/partners', '/admin/pages',
]);

it('renders the create form of every content resource', function (string $url) {
    $this->seed(RoleSeeder::class);
    $editor = tap(User::factory()->create())->assignRole(Role::Editor->value);

    $this->actingAs($editor)->get($url.'/create')->assertOk();
})->with([
    '/admin/services', '/admin/service-packages', '/admin/projects', '/admin/project-types', '/admin/posts',
    '/admin/post-categories', '/admin/testimonials', '/admin/faqs', '/admin/slides', '/admin/partners', '/admin/pages',
]);

it('renders the edit form of a project with relations', function () {
    $this->seed(RoleSeeder::class);
    $editor = tap(User::factory()->create())->assignRole(Role::Editor->value);
    $project = Project::factory()->create();
    $project->services()->attach(Service::factory()->create());

    $this->actingAs($editor)->get("/admin/projects/{$project->id}/edit")->assertOk();
});

it('hides content resources from technicians', function () {
    $this->seed(RoleSeeder::class);
    $technician = tap(User::factory()->create())->assignRole(Role::Technician->value);

    $this->actingAs($technician)->get('/admin/projects')->assertForbidden();
});
