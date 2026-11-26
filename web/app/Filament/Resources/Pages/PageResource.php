<?php

namespace App\Filament\Resources\Pages;

use App\Enums\Role;
use App\Filament\Concerns\RestrictsToRoles;
use App\Filament\Resources\Pages\Pages\CreatePage;
use App\Filament\Resources\Pages\Pages\EditPage;
use App\Filament\Resources\Pages\Pages\ListPages;
use App\Models\Page;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class PageResource extends Resource
{
    use RestrictsToRoles;

    protected static ?string $model = Page::class;

    protected static ?string $modelLabel = 'trang tĩnh';

    protected static ?string $pluralModelLabel = 'Trang tĩnh';

    protected static string|UnitEnum|null $navigationGroup = 'Nội dung';

    protected static ?int $navigationSort = 11;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static function allowedRoles(): array
    {
        return [Role::Admin, Role::Editor];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            TextInput::make('title')->label('Tiêu đề')->required(),
            TextInput::make('key')->label('Mã trang')->required()->unique(ignoreRecord: true)->helperText('Ví dụ: about (trang Giới thiệu).'),
            RichEditor::make('body')->label('Nội dung')->columnSpanFull(),
            SpatieMediaLibraryFileUpload::make('cover')->label('Ảnh đầu trang')->collection('cover')->image(),
            Section::make('SEO')->collapsed()->columnSpanFull()->schema([
                TextInput::make('seo_title')->label('Tiêu đề SEO'),
                Textarea::make('seo_description')->label('Mô tả SEO')->maxLength(300)->rows(2),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('title')->label('Tiêu đề')->searchable(),
                TextColumn::make('key')->label('Mã trang'),
            ])
            ->filters([

            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPages::route('/'),
            'create' => CreatePage::route('/create'),
            'edit' => EditPage::route('/{record}/edit'),
        ];
    }
}
