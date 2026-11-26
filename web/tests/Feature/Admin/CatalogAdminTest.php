<?php

use App\Enums\Role;
use App\Filament\Resources\ArtTrees\Pages\ListArtTrees;
use App\Filament\Resources\Products\Pages\ListProducts;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(RoleSeeder::class);
});

function staff(Role $role): User
{
    return tap(User::factory()->create())->assignRole($role->value);
}

it('lets editors manage products, art trees and categories', function (string $url) {
    $this->actingAs(staff(Role::Editor))->get($url)->assertOk();
})->with(['/admin/products', '/admin/cay-nghe-thuat', '/admin/categories', '/admin/products/create', '/admin/cay-nghe-thuat/create']);

it('lets sales open art trees but not edit the product catalog', function () {
    $sales = staff(Role::Sales);

    $this->actingAs($sales)->get('/admin/cay-nghe-thuat')->assertOk();
    $this->actingAs($sales)->get('/admin/categories')->assertForbidden();
});

it('hides the catalog from technicians', function () {
    $this->actingAs(staff(Role::Technician))->get('/admin/products')->assertForbidden();
});

it('lists only standard products in the products resource', function () {
    $this->actingAs(staff(Role::Editor));
    $standard = Product::factory()->create();
    $art = Product::factory()->art()->create();

    Livewire::test(ListProducts::class)
        ->assertCanSeeTableRecords([$standard])
        ->assertCanNotSeeTableRecords([$art]);

    Livewire::test(ListArtTrees::class)
        ->assertCanSeeTableRecords([$art])
        ->assertCanNotSeeTableRecords([$standard]);
});
