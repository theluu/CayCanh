<?php

namespace App\Filament\Resources\Products\Tables;

use App\Enums\ArtAvailability;
use App\Enums\ProductKind;
use App\Models\Product;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table, ProductKind $kind = ProductKind::Standard): Table
    {
        $isArt = $kind === ProductKind::Art;

        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                SpatieMediaLibraryImageColumn::make('gallery')->label('Ảnh')->collection('gallery')->conversion('thumb')->limit(1),
                TextColumn::make('name')->label('Tên')->searchable()->sortable(),
                TextColumn::make('category.name')->label('Danh mục')->toggleable(),
                TextColumn::make('price')->label('Giá')
                    ->formatStateUsing(fn (Product $record) => $record->displayPrice())
                    ->sortable(),
                TextColumn::make('availability')->label('Tình trạng')->badge()
                    ->formatStateUsing(fn (ArtAvailability $state) => $state->label())
                    ->color(fn (ArtAvailability $state) => $state->color())
                    ->visible($isArt),
                IconColumn::make('in_stock')->label('Còn hàng')->boolean()->hidden($isArt),
                IconColumn::make('is_published')->label('Xuất bản')->boolean(),
                IconColumn::make('is_featured')->label('Nổi bật')->boolean(),
                TextColumn::make('updated_at')->label('Cập nhật')->since()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category_id')->label('Danh mục')->relationship('category', 'name', fn ($query) => $query->where('scope', $kind)),
                SelectFilter::make('availability')->label('Tình trạng')->options(ArtAvailability::options())->visible($isArt),
                TernaryFilter::make('is_published')->label('Xuất bản'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
