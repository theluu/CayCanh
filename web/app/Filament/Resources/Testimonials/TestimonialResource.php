<?php

namespace App\Filament\Resources\Testimonials;

use App\Enums\Role;
use App\Filament\Concerns\RestrictsToRoles;
use App\Filament\Resources\Testimonials\Pages\CreateTestimonial;
use App\Filament\Resources\Testimonials\Pages\EditTestimonial;
use App\Filament\Resources\Testimonials\Pages\ListTestimonials;
use App\Models\Testimonial;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
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

class TestimonialResource extends Resource
{
    use RestrictsToRoles;

    protected static ?string $model = Testimonial::class;

    protected static ?string $modelLabel = 'đánh giá';

    protected static ?string $pluralModelLabel = 'Đánh giá khách hàng';

    protected static string|UnitEnum|null $navigationGroup = 'Nội dung';

    protected static ?int $navigationSort = 7;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static function allowedRoles(): array
    {
        return [Role::Admin, Role::Editor];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            TextInput::make('name')->label('Tên khách hàng')->required(),
            TextInput::make('role')->label('Chức vụ / đơn vị'),
            Textarea::make('content')->label('Nội dung')->required()->rows(4)->columnSpanFull(),
            Select::make('rating')->label('Số sao')->options([5 => '5 sao', 4 => '4 sao', 3 => '3 sao'])->default(5),
            TextInput::make('sort')->label('Thứ tự')->numeric()->default(0),
            SpatieMediaLibraryFileUpload::make('avatar')->label('Ảnh đại diện')->collection('avatar')->image()->avatar(),
            Toggle::make('is_published')->label('Hiển thị')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort')
            ->reorderable('sort')
            ->columns([
                SpatieMediaLibraryImageColumn::make('avatar')->label('Ảnh')->collection('avatar')->circular(),
                TextColumn::make('name')->label('Tên')->searchable(),
                TextColumn::make('content')->label('Nội dung')->limit(60),
                TextColumn::make('rating')->label('Sao'),
                IconColumn::make('is_published')->label('Xuất bản')->boolean(),
            ])
            ->filters([

            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTestimonials::route('/'),
            'create' => CreateTestimonial::route('/create'),
            'edit' => EditTestimonial::route('/{record}/edit'),
        ];
    }
}
