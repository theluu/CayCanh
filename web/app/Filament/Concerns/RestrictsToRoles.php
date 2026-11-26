<?php

namespace App\Filament\Concerns;

use App\Enums\Role;
use App\Models\User;

trait RestrictsToRoles
{
    /** @return array<int, Role> */
    abstract protected static function allowedRoles(): array;

    public static function canAccess(array $parameters = []): bool
    {
        $user = auth()->user();

        return $user instanceof User && $user->hasAnyStaffRole(...static::allowedRoles());
    }
}
