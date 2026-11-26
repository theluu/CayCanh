<?php

namespace App\Filament\Resources\Services;

use App\Enums\Role;
use App\Filament\Concerns\RestrictsToRoles;
use App\Filament\Resources\Services\Pages\CreateService;
use App\Filament\Resources\Services\Pages\EditService;
use App\Filament\Resources\Services\Pages\ListServices;
use App\Models\Service;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class ServiceResource extends Resource
{
    use RestrictsToRoles;

    protected static ?string $model = Service::class;

    protected static ?string $modelLabel = 'dịch vụ';

    protected static ?string $pluralModelLabel = 'Dịch vụ';

    protected static string|UnitEnum|null $navigationGroup = 'Nội dung';

    protected static ?int $navigationSort = 1;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWrenchScrewdriver;

    protected static function allowedRoles(): array
    {
        return [Role::Admin, Role::Editor];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            TextInput::make('name')->label('Tên dịch vụ')->required(),
            TextInput::make('slug')->label('Slug')->helperText('Để trống để tự tạo.'),
            TextInput::make('icon')->label('Icon (tên heroicon hoặc emoji)'),
            TextInput::make('sort')->label('Thứ tự')->numeric()->default(0),
            Textarea::make('summary')->label('Mô tả ngắn')->rows(3)->columnSpanFull(),
            RichEditor::make('body')->label('Nội dung')->columnSpanFull(),
            SpatieMediaLibraryFileUpload::make('cover')->label('Ảnh đại diện')->collection('cover')->image(),
            SpatieMediaLibraryFileUpload::make('gallery')->label('Album ảnh')->collection('gallery')->multiple()->reorderable()->image(),
            Toggle::make('is_published')->label('Xuất bản')->default(true),
            Section::make('SEO')->collapsed()->columnSpanFull()->schema([
                TextInput::make('seo_title')->label('Tiêu đề SEO'),
                Textarea::make('seo_description')->label('Mô tả SEO')->maxLength(300)->rows(2),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort')
            ->reorderable('sort')
            ->columns([
                SpatieMediaLibraryImageColumn::make('cover')->label('Ảnh')->collection('cover')->conversion('thumb'),
                TextColumn::make('name')->label('Tên')->searchable(),
                TextColumn::make('packages_count')->label('Số gói')->counts('packages'),
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
            'index' => ListServices::route('/'),
            'create' => CreateService::route('/create'),
            'edit' => EditService::route('/{record}/edit'),
        ];
    }
}
