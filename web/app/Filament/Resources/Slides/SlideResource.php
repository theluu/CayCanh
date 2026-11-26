<?php

namespace App\Filament\Resources\Slides;

use App\Enums\Role;
use App\Filament\Concerns\RestrictsToRoles;
use App\Filament\Resources\Slides\Pages\CreateSlide;
use App\Filament\Resources\Slides\Pages\EditSlide;
use App\Filament\Resources\Slides\Pages\ListSlides;
use App\Models\Slide;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class SlideResource extends Resource
{
    use RestrictsToRoles;

    protected static ?string $model = Slide::class;

    protected static ?string $modelLabel = 'banner';

    protected static ?string $pluralModelLabel = 'Banner trang chủ';

    protected static string|UnitEnum|null $navigationGroup = 'Nội dung';

    protected static ?int $navigationSort = 9;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static function allowedRoles(): array
    {
        return [Role::Admin, Role::Editor];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            TextInput::make('title')->label('Tiêu đề')->required()->columnSpanFull(),
            TextInput::make('subtitle')->label('Mô tả')->columnSpanFull(),
            TextInput::make('button_text')->label('Chữ trên nút'),
            TextInput::make('button_url')->label('Link nút'),
            SpatieMediaLibraryFileUpload::make('image')->label('Ảnh nền (1920×800)')->collection('image')->image()->required(),
            TextInput::make('sort')->label('Thứ tự')->numeric()->default(0),
            Toggle::make('is_active')->label('Đang hiển thị')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort')
            ->reorderable('sort')
            ->columns([
                SpatieMediaLibraryImageColumn::make('image')->label('Ảnh')->collection('image')->conversion('thumb'),
                TextColumn::make('title')->label('Tiêu đề')->limit(50),
                IconColumn::make('is_active')->label('Hiển thị')->boolean(),
            ])
            ->filters([

            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSlides::route('/'),
            'create' => CreateSlide::route('/create'),
            'edit' => EditSlide::route('/{record}/edit'),
        ];
    }
}
