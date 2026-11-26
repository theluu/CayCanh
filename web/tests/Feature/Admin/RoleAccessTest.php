<?php

use App\Enums\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;

beforeEach(function () {
    $this->seed(RoleSeeder::class);
});

function userWithRole(?Role $role): User
{
    $user = User::factory()->create();

    if ($role) {
        $user->assignRole($role->value);
    }

    return $user;
}

it('forbids the admin panel to users without a role', function () {
    $this->actingAs(userWithRole(null))->get('/admin')->assertForbidden();
});

it('lets every staff role into the admin panel', function (Role $role) {
    $this->actingAs(userWithRole($role))->get('/admin')->assertOk();
})->with(Role::cases());

it('only lets admins manage users', function () {
    $this->actingAs(userWithRole(Role::Editor))->get('/admin/users')->assertForbidden();
    $this->actingAs(userWithRole(Role::Admin))->get('/admin/users')->assertOk();
});

it('only lets admins open the general settings page', function () {
    $this->actingAs(userWithRole(Role::Sales))->get('/admin/cai-dat-chung')->assertForbidden();
    $this->actingAs(userWithRole(Role::Admin))->get('/admin/cai-dat-chung')->assertOk();
});

it('creates the four roles and an admin user from env', function () {
    config(['app.admin_email' => 'boss@example.com', 'app.admin_password' => 'secret123']);
    $this->seed(RoleSeeder::class);

    expect(User::where('email', 'boss@example.com')->first()->hasRole(Role::Admin->value))->toBeTrue();
});
