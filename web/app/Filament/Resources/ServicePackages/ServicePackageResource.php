<?php

namespace App\Filament\Resources\ServicePackages;

use App\Enums\Role;
use App\Filament\Concerns\RestrictsToRoles;
use App\Filament\Resources\ServicePackages\Pages\CreateServicePackage;
use App\Filament\Resources\ServicePackages\Pages\EditServicePackage;
use App\Filament\Resources\ServicePackages\Pages\ListServicePackages;
use App\Models\ServicePackage;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
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

class ServicePackageResource extends Resource
{
    use RestrictsToRoles;

    protected static ?string $model = ServicePackage::class;

    protected static ?string $modelLabel = 'gói dịch vụ';

    protected static ?string $pluralModelLabel = 'Gói dịch vụ / Bảng giá';

    protected static string|UnitEnum|null $navigationGroup = 'Nội dung';

    protected static ?int $navigationSort = 2;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCurrencyDollar;

    protected static function allowedRoles(): array
    {
        return [Role::Admin, Role::Editor];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            Select::make('service_id')->label('Dịch vụ')->relationship('service', 'name')->required()->preload(),
            TextInput::make('name')->label('Tên gói')->required(),
            TextInput::make('price_from')->label('Giá từ (đ)')->numeric()->minValue(0),
            TextInput::make('unit')->label('Đơn vị')->placeholder('/tháng'),
            TagsInput::make('features')->label('Quyền lợi')->columnSpanFull(),
            Toggle::make('is_highlighted')->label('Gói nổi bật'),
            TextInput::make('sort')->label('Thứ tự')->numeric()->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort')
            ->reorderable('sort')
            ->columns([
                TextColumn::make('service.name')->label('Dịch vụ')->sortable(),
                TextColumn::make('name')->label('Tên gói')->searchable(),
                TextColumn::make('price_from')->label('Giá từ')->money('VND', locale: 'vi'),
                TextColumn::make('unit')->label('Đơn vị'),
                IconColumn::make('is_highlighted')->label('Nổi bật')->boolean(),
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
            'index' => ListServicePackages::route('/'),
            'create' => CreateServicePackage::route('/create'),
            'edit' => EditServicePackage::route('/{record}/edit'),
        ];
    }
}
