<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role as RoleModel;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (Role::cases() as $role) {
            RoleModel::findOrCreate($role->value, 'web');
        }

        $email = config('app.admin_email');
        $password = config('app.admin_password');

        if (! $email || ! $password) {
            return;
        }

        $admin = User::firstOrCreate(
            ['email' => $email],
            ['name' => 'Quản trị viên', 'password' => $password],
        );

        $admin->assignRole(Role::Admin->value);
    }
}
