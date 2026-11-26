<?php

namespace App\Filament\Resources\Posts;

use App\Enums\Role;
use App\Filament\Concerns\RestrictsToRoles;
use App\Filament\Resources\Posts\Pages\CreatePost;
use App\Filament\Resources\Posts\Pages\EditPost;
use App\Filament\Resources\Posts\Pages\ListPosts;
use App\Models\Post;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class PostResource extends Resource
{
    use RestrictsToRoles;

    protected static ?string $model = Post::class;

    protected static ?string $modelLabel = 'bài viết';

    protected static ?string $pluralModelLabel = 'Tin tức';

    protected static string|UnitEnum|null $navigationGroup = 'Nội dung';

    protected static ?int $navigationSort = 5;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNewspaper;

    protected static function allowedRoles(): array
    {
        return [Role::Admin, Role::Editor];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            TextInput::make('title')->label('Tiêu đề')->required()->columnSpanFull(),
            TextInput::make('slug')->label('Slug')->helperText('Để trống để tự tạo.'),
            Select::make('post_category_id')->label('Chuyên mục')->relationship('category', 'name')->preload()
                ->createOptionForm([TextInput::make('name')->label('Tên chuyên mục')->required()]),
            Select::make('tags')->label('Thẻ')->relationship('tags', 'name')->multiple()->preload()
                ->createOptionForm([TextInput::make('name')->label('Tên thẻ')->required()]),
            DateTimePicker::make('published_at')->label('Ngày đăng')->default(now()),
            Textarea::make('excerpt')->label('Tóm tắt')->rows(3)->columnSpanFull(),
            RichEditor::make('body')->label('Nội dung')->columnSpanFull(),
            SpatieMediaLibraryFileUpload::make('cover')->label('Ảnh đại diện')->collection('cover')->image(),
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
            ->defaultSort('published_at', 'desc')
            ->columns([
                SpatieMediaLibraryImageColumn::make('cover')->label('Ảnh')->collection('cover')->conversion('thumb'),
                TextColumn::make('title')->label('Tiêu đề')->searchable()->limit(60),
                TextColumn::make('category.name')->label('Chuyên mục'),
                TextColumn::make('published_at')->label('Ngày đăng')->dateTime('d/m/Y H:i')->sortable(),
                IconColumn::make('is_published')->label('Xuất bản')->boolean(),
            ])
            ->filters([
                SelectFilter::make('post_category_id')->label('Chuyên mục')->relationship('category', 'name'),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPosts::route('/'),
            'create' => CreatePost::route('/create'),
            'edit' => EditPost::route('/{record}/edit'),
        ];
    }
}
