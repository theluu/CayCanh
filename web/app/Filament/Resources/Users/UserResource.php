<?php

namespace App\Filament\Resources\Users;

use App\Enums\Role;
use App\Filament\Concerns\RestrictsToRoles;
use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\Tables\UsersTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class UserResource extends Resource
{
    use RestrictsToRoles;

    protected static ?string $model = User::class;

    protected static ?string $modelLabel = 'người dùng';

    protected static ?string $pluralModelLabel = 'Người dùng';

    protected static string|UnitEnum|null $navigationGroup = 'Hệ thống';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static function allowedRoles(): array
    {
        return [Role::Admin];
    }

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
