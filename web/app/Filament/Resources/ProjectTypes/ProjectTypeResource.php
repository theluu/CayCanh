<?php

namespace App\Filament\Resources\ProjectTypes;

use App\Enums\Role;
use App\Filament\Concerns\RestrictsToRoles;
use App\Filament\Resources\ProjectTypes\Pages\CreateProjectType;
use App\Filament\Resources\ProjectTypes\Pages\EditProjectType;
use App\Filament\Resources\ProjectTypes\Pages\ListProjectTypes;
use App\Models\ProjectType;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class ProjectTypeResource extends Resource
{
    use RestrictsToRoles;

    protected static ?string $model = ProjectType::class;

    protected static ?string $modelLabel = 'loại công trình';

    protected static ?string $pluralModelLabel = 'Loại công trình';

    protected static string|UnitEnum|null $navigationGroup = 'Nội dung';

    protected static ?int $navigationSort = 4;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static function allowedRoles(): array
    {
        return [Role::Admin, Role::Editor];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            TextInput::make('name')->label('Tên')->required(),
            TextInput::make('slug')->label('Slug')->helperText('Để trống để tự tạo.'),
            TextInput::make('sort')->label('Thứ tự')->numeric()->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort')
            ->reorderable('sort')
            ->columns([
                TextColumn::make('name')->label('Tên')->searchable(),
                TextColumn::make('projects_count')->label('Số dự án')->counts('projects'),
            ])
            ->filters([

            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProjectTypes::route('/'),
            'create' => CreateProjectType::route('/create'),
            'edit' => EditProjectType::route('/{record}/edit'),
        ];
    }
}
