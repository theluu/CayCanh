<?php

namespace App\Filament\Resources\Faqs;

use App\Enums\Role;
use App\Filament\Concerns\RestrictsToRoles;
use App\Filament\Resources\Faqs\Pages\CreateFaq;
use App\Filament\Resources\Faqs\Pages\EditFaq;
use App\Filament\Resources\Faqs\Pages\ListFaqs;
use App\Models\Faq;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class FaqResource extends Resource
{
    use RestrictsToRoles;

    protected static ?string $model = Faq::class;

    protected static ?string $modelLabel = 'câu hỏi';

    protected static ?string $pluralModelLabel = 'Câu hỏi thường gặp';

    protected static string|UnitEnum|null $navigationGroup = 'Nội dung';

    protected static ?int $navigationSort = 8;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQuestionMarkCircle;

    protected static function allowedRoles(): array
    {
        return [Role::Admin, Role::Editor];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            TextInput::make('question')->label('Câu hỏi')->required()->columnSpanFull(),
            RichEditor::make('answer')->label('Trả lời')->required()->columnSpanFull(),
            Select::make('service_id')->label('Thuộc dịch vụ (tuỳ chọn)')->relationship('service', 'name')->preload(),
            TextInput::make('sort')->label('Thứ tự')->numeric()->default(0),
            Toggle::make('is_published')->label('Hiển thị')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort')
            ->reorderable('sort')
            ->columns([
                TextColumn::make('question')->label('Câu hỏi')->searchable()->limit(70),
                TextColumn::make('service.name')->label('Dịch vụ'),
                IconColumn::make('is_published')->label('Xuất bản')->boolean(),
            ])
            ->filters([
                SelectFilter::make('service_id')->label('Dịch vụ')->relationship('service', 'name'),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFaqs::route('/'),
            'create' => CreateFaq::route('/create'),
            'edit' => EditFaq::route('/{record}/edit'),
        ];
    }
}
