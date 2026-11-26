<?php

namespace App\Filament\Resources\Categories\Schemas;

use App\Enums\ProductKind;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            TextInput::make('name')->label('Tên danh mục')->required(),
            TextInput::make('slug')->label('Slug')->helperText('Để trống để tự tạo.'),
            Select::make('scope')->label('Loại')
                ->options(collect(ProductKind::cases())->mapWithKeys(fn (ProductKind $kind) => [$kind->value => $kind->label()]))
                ->default(ProductKind::Standard->value)
                ->required(),
            Select::make('parent_id')->label('Danh mục cha')->relationship('parent', 'name')->searchable()->preload(),
            TextInput::make('sort')->label('Thứ tự')->numeric()->default(0),
            Toggle::make('is_visible')->label('Hiển thị')->default(true),
            Textarea::make('description')->label('Mô tả')->columnSpanFull(),
        ]);
    }
}
