<?php

namespace App\Filament\Resources\Categories\Tables;

use App\Enums\ProductKind;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort')
            ->reorderable('sort')
            ->columns([
                TextColumn::make('name')->label('Tên')->searchable(),
                TextColumn::make('scope')->label('Loại')->badge()->formatStateUsing(fn (ProductKind $state) => $state->label()),
                TextColumn::make('parent.name')->label('Danh mục cha'),
                TextColumn::make('products_count')->label('Số SP')->counts('products'),
                IconColumn::make('is_visible')->label('Hiển thị')->boolean(),
            ])
            ->filters([
                SelectFilter::make('scope')->label('Loại')->options(collect(ProductKind::cases())->mapWithKeys(fn (ProductKind $kind) => [$kind->value => $kind->label()])),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
