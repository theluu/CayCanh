<?php

namespace App\Filament\Resources\CareContracts;

use App\Enums\CareFrequency;
use App\Enums\InquiryStatus;
use App\Enums\Role;
use App\Filament\Concerns\RestrictsToRoles;
use App\Filament\Resources\CareContracts\Pages\CreateCareContract;
use App\Filament\Resources\CareContracts\Pages\EditCareContract;
use App\Filament\Resources\CareContracts\Pages\ListCareContracts;
use App\Models\CareContract;
use App\Models\Inquiry;
use App\Models\User;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use UnitEnum;

class CareContractResource extends Resource
{
    use RestrictsToRoles;

    protected static ?string $model = CareContract::class;

    protected static ?string $modelLabel = 'hợp đồng chăm sóc';

    protected static ?string $pluralModelLabel = 'Hợp đồng chăm sóc';

    protected static string|UnitEnum|null $navigationGroup = 'Chăm sóc';

    protected static ?int $navigationSort = 2;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentCheck;

    protected static function allowedRoles(): array
    {
        return [Role::Admin, Role::Sales];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            Select::make('inquiry_id')->label('Từ yêu cầu (tuỳ chọn)')
                ->options(fn () => Inquiry::where('status', InquiryStatus::Won)->latest()->limit(100)->get()
                    ->mapWithKeys(fn (Inquiry $inquiry) => [$inquiry->id => '#'.$inquiry->id.' – '.$inquiry->name.' ('.$inquiry->phone.')']))
                ->searchable()
                ->live()
                ->afterStateUpdated(function (?string $state, Set $set) {
                    if ($inquiry = Inquiry::find($state)) {
                        $set('customer_name', $inquiry->name);
                        $set('phone', $inquiry->phone);
                        $set('address', $inquiry->address ?? $inquiry->meta['location'] ?? null);
                    }
                })
                ->columnSpanFull(),
            TextInput::make('customer_name')->label('Khách hàng / công trình')->required(),
            TextInput::make('phone')->label('Số điện thoại')->tel(),
            TextInput::make('address')->label('Địa chỉ công trình')->required()->columnSpanFull(),
            Select::make('service_package_id')->label('Gói dịch vụ')->relationship('package', 'name')->preload(),
            Select::make('frequency')->label('Tần suất')->options(CareFrequency::options())->default(CareFrequency::Weekly->value)->required(),
            DatePicker::make('starts_on')->label('Ngày bắt đầu')->required()->default(today()),
            DatePicker::make('ends_on')->label('Ngày kết thúc')->afterOrEqual('starts_on'),
            Select::make('default_technician_id')->label('Kỹ thuật viên mặc định')
                ->options(fn () => User::role(Role::Technician->value)->pluck('name', 'id'))
                ->searchable(),
            Toggle::make('is_active')->label('Đang hoạt động')->default(true),
            Textarea::make('note')->label('Ghi chú')->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('customer_name')->label('Khách hàng')->searchable()->description(fn (CareContract $record) => $record->address),
                TextColumn::make('frequency')->label('Tần suất')->formatStateUsing(fn (CareFrequency $state) => $state->label()),
                TextColumn::make('starts_on')->label('Bắt đầu')->date('d/m/Y'),
                TextColumn::make('ends_on')->label('Kết thúc')->date('d/m/Y')->placeholder('Không thời hạn'),
                TextColumn::make('defaultTechnician.name')->label('Kỹ thuật viên')->placeholder('—'),
                TextColumn::make('visits_count')->label('Số lượt')->counts('visits'),
                IconColumn::make('is_active')->label('Hoạt động')->boolean(),
            ])
            ->filters([TernaryFilter::make('is_active')->label('Đang hoạt động')])
            ->recordActions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCareContracts::route('/'),
            'create' => CreateCareContract::route('/create'),
            'edit' => EditCareContract::route('/{record}/edit'),
        ];
    }
}
