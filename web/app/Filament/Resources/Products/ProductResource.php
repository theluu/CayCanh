<?php

namespace App\Filament\Resources\Products;

use App\Enums\ProductKind;
use App\Enums\Role;
use App\Filament\Concerns\RestrictsToRoles;
use App\Filament\Resources\Products\Pages\CreateProduct;
use App\Filament\Resources\Products\Pages\EditProduct;
use App\Filament\Resources\Products\Pages\ListProducts;
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

class ProductResource extends Resource
{
    use RestrictsToRoles;

    protected static ?string $model = Product::class;

    protected static ?string $modelLabel = 'sản phẩm';

    protected static ?string $pluralModelLabel = 'Sản phẩm';

    protected static string|UnitEnum|null $navigationGroup = 'Sản phẩm';

    protected static ?int $navigationSort = 2;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingBag;

    protected static ?string $recordTitleAttribute = 'name';

    protected static function allowedRoles(): array
    {
        return [Role::Admin, Role::Editor];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('kind', ProductKind::Standard);
    }

    public static function form(Schema $schema): Schema
    {
        return ProductForm::configure($schema, ProductKind::Standard);
    }

    public static function table(Table $table): Table
    {
        return ProductsTable::configure($table, ProductKind::Standard);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProducts::route('/'),
            'create' => CreateProduct::route('/create'),
            'edit' => EditProduct::route('/{record}/edit'),
        ];
    }
}
