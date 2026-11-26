<?php

namespace App\Filament\Resources\ArtTrees;

use App\Enums\ArtAvailability;
use App\Enums\ProductKind;
use App\Enums\Role;
use App\Filament\Concerns\RestrictsToRoles;
use App\Filament\Resources\ArtTrees\Pages\CreateArtTree;
use App\Filament\Resources\ArtTrees\Pages\EditArtTree;
use App\Filament\Resources\ArtTrees\Pages\ListArtTrees;
use App\Filament\Resources\Products\Schemas\ProductForm;
use App\Filament\Resources\Products\Tables\ProductsTable;
use App\Models\Product;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class ArtTreeResource extends Resource
{
    use RestrictsToRoles;

    protected static ?string $model = Product::class;

    protected static ?string $slug = 'cay-nghe-thuat';

    protected static ?string $modelLabel = 'cây nghệ thuật';

    protected static ?string $pluralModelLabel = 'Cây cảnh nghệ thuật';

    protected static string|UnitEnum|null $navigationGroup = 'Sản phẩm';

    protected static ?int $navigationSort = 1;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;

    protected static ?string $recordTitleAttribute = 'name';

    protected static function allowedRoles(): array
    {
        return [Role::Admin, Role::Editor, Role::Sales];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('kind', ProductKind::Art);
    }

    public static function getNavigationBadge(): ?string
    {
        $reserved = Product::query()->art()->where('availability', ArtAvailability::Reserved)->count();

        return $reserved > 0 ? (string) $reserved : null;
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Số cây đang giữ';
    }

    public static function form(Schema $schema): Schema
    {
        return ProductForm::configure($schema, ProductKind::Art);
    }

    public static function table(Table $table): Table
    {
        return ProductsTable::configure($table, ProductKind::Art);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListArtTrees::route('/'),
            'create' => CreateArtTree::route('/create'),
            'edit' => EditArtTree::route('/{record}/edit'),
        ];
    }
}
