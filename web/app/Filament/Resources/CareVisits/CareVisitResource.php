<?php

namespace App\Filament\Resources\CareVisits;

use App\Enums\CareVisitStatus;
use App\Enums\Role;
use App\Filament\Concerns\RestrictsToRoles;
use App\Filament\Resources\CareVisits\Pages\EditCareVisit;
use App\Filament\Resources\CareVisits\Pages\ListCareVisits;
use App\Models\CareVisit;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class CareVisitResource extends Resource
{
    use RestrictsToRoles;

    protected static ?string $model = CareVisit::class;

    protected static ?string $modelLabel = 'lượt chăm sóc';

    protected static ?string $pluralModelLabel = 'Lịch chăm sóc';

    protected static string|UnitEnum|null $navigationGroup = 'Chăm sóc';

    protected static ?int $navigationSort = 1;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static function allowedRoles(): array
    {
        return [Role::Admin, Role::Sales, Role::Technician];
    }

    public static function canCreate(): bool
    {
        return static::isManager();
    }

    /** Technicians only ever see the visits assigned to them. */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['contract', 'technician']);

        return static::isManager() ? $query : $query->where('technician_id', auth()->id());
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            Section::make('Công trình')->columns(2)->columnSpanFull()->schema([
                TextEntry::make('contract.customer_name')->label('Khách hàng'),
                TextEntry::make('contract.phone')->label('Số điện thoại')->placeholder('—'),
                TextEntry::make('contract.address')->label('Địa chỉ')->columnSpanFull(),
                TextEntry::make('contract.note')->label('Ghi chú hợp đồng')->placeholder('—')->columnSpanFull(),
            ]),
            DatePicker::make('scheduled_on')->label('Ngày chăm sóc')->required()->disabled(fn () => ! static::isManager()),
            Select::make('technician_id')->label('Kỹ thuật viên')
                ->options(fn () => User::role(Role::Technician->value)->pluck('name', 'id'))
                ->searchable()
                ->disabled(fn () => ! static::isManager()),
            Select::make('status')->label('Trạng thái')->options(CareVisitStatus::options())->required(),
            Textarea::make('note')->label('Ghi chú / báo cáo')->rows(4)->columnSpanFull(),
            SpatieMediaLibraryFileUpload::make('before')->label('Ảnh trước')->collection('before')->multiple()->image(),
            SpatieMediaLibraryFileUpload::make('after')->label('Ảnh sau')->collection('after')->multiple()->image(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('scheduled_on')
            ->defaultGroup(Group::make('scheduled_on')->label('Ngày')->date())
            ->columns([
                TextColumn::make('scheduled_on')->label('Ngày')->date('D d/m/Y')->sortable(),
                TextColumn::make('contract.customer_name')->label('Công trình')->searchable()
                    ->description(fn (CareVisit $record) => $record->contract?->address),
                TextColumn::make('contract.phone')->label('SĐT')->placeholder('—'),
                TextColumn::make('technician.name')->label('Kỹ thuật viên')->placeholder('Chưa phân công'),
                TextColumn::make('status')->label('Trạng thái')->badge()
                    ->formatStateUsing(fn (CareVisitStatus $state) => $state->label())
                    ->color(fn (CareVisitStatus $state) => $state->color()),
            ])
            ->filters([
                Filter::make('this_week')->label('Tuần này')
                    ->query(fn (Builder $query) => $query->whereBetween('scheduled_on', [now()->startOfWeek(), now()->endOfWeek()])),
                Filter::make('upcoming')->label('Từ hôm nay')->default()
                    ->query(fn (Builder $query) => $query->whereDate('scheduled_on', '>=', today())),
                SelectFilter::make('status')->label('Trạng thái')->options(CareVisitStatus::options()),
                SelectFilter::make('technician_id')->label('Kỹ thuật viên')
                    ->options(fn () => User::role(Role::Technician->value)->pluck('name', 'id'))
                    ->visible(fn () => static::isManager()),
            ])
            ->recordActions([
                Action::make('done')->label('Hoàn thành')->icon('heroicon-o-check')->color('success')
                    ->visible(fn (CareVisit $record) => $record->status !== CareVisitStatus::Done)
                    ->action(fn (CareVisit $record) => $record->update(['status' => CareVisitStatus::Done])),
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCareVisits::route('/'),
            'edit' => EditCareVisit::route('/{record}/edit'),
        ];
    }

    private static function isManager(): bool
    {
        $user = auth()->user();

        return $user instanceof User && $user->hasAnyStaffRole(Role::Admin, Role::Sales);
    }
}
