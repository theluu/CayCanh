<?php

namespace App\Filament\Resources\Projects;

use App\Enums\Role;
use App\Filament\Concerns\RestrictsToRoles;
use App\Filament\Resources\Projects\Pages\CreateProject;
use App\Filament\Resources\Projects\Pages\EditProject;
use App\Filament\Resources\Projects\Pages\ListProjects;
use App\Models\Project;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
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

class ProjectResource extends Resource
{
    use RestrictsToRoles;

    protected static ?string $model = Project::class;

    protected static ?string $modelLabel = 'dự án';

    protected static ?string $pluralModelLabel = 'Dự án';

    protected static string|UnitEnum|null $navigationGroup = 'Nội dung';

    protected static ?int $navigationSort = 3;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static function allowedRoles(): array
    {
        return [Role::Admin, Role::Editor];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            TextInput::make('name')->label('Tên công trình')->required(),
            TextInput::make('slug')->label('Slug')->helperText('Để trống để tự tạo.'),
            Select::make('project_type_id')->label('Loại công trình')->relationship('type', 'name')->preload()
                ->createOptionForm([TextInput::make('name')->label('Tên loại')->required()]),
            Select::make('services')->label('Dịch vụ đã thực hiện')->relationship('services', 'name')->multiple()->preload(),
            TextInput::make('client')->label('Chủ đầu tư / khách hàng'),
            TextInput::make('location')->label('Địa điểm'),
            TextInput::make('year')->label('Năm')->numeric(),
            TextInput::make('scale')->label('Quy mô'),
            Textarea::make('summary')->label('Mô tả ngắn')->rows(3)->columnSpanFull(),
            RichEditor::make('body')->label('Nội dung')->columnSpanFull(),
            SpatieMediaLibraryFileUpload::make('cover')->label('Ảnh đại diện')->collection('cover')->image(),
            SpatieMediaLibraryFileUpload::make('gallery')->label('Album ảnh')->collection('gallery')->multiple()->reorderable()->image(),
            TextInput::make('video_url')->label('Link video')->url(),
            Toggle::make('is_featured')->label('Nổi bật trên trang chủ'),
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
            ->defaultSort('created_at', 'desc')
            ->columns([
                SpatieMediaLibraryImageColumn::make('cover')->label('Ảnh')->collection('cover')->conversion('thumb'),
                TextColumn::make('name')->label('Tên')->searchable(),
                TextColumn::make('type.name')->label('Loại'),
                TextColumn::make('location')->label('Địa điểm'),
                TextColumn::make('year')->label('Năm')->sortable(),
                IconColumn::make('is_featured')->label('Nổi bật')->boolean(),
                IconColumn::make('is_published')->label('Xuất bản')->boolean(),
            ])
            ->filters([
                SelectFilter::make('project_type_id')->label('Loại')->relationship('type', 'name'),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProjects::route('/'),
            'create' => CreateProject::route('/create'),
            'edit' => EditProject::route('/{record}/edit'),
        ];
    }
}
