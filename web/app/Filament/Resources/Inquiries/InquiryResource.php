<?php

namespace App\Filament\Resources\Inquiries;

use App\Enums\InquiryStatus;
use App\Enums\InquiryType;
use App\Enums\Role;
use App\Filament\Concerns\RestrictsToRoles;
use App\Filament\Resources\Inquiries\Pages\EditInquiry;
use App\Filament\Resources\Inquiries\Pages\ListInquiries;
use App\Models\Inquiry;
use App\Models\User;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class InquiryResource extends Resource
{
    use RestrictsToRoles;

    protected static ?string $model = Inquiry::class;

    protected static ?string $modelLabel = 'yêu cầu';

    protected static ?string $pluralModelLabel = 'Liên hệ / Báo giá';

    protected static string|UnitEnum|null $navigationGroup = 'Bán hàng';

    protected static ?int $navigationSort = 1;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInboxArrowDown;

    protected static function allowedRoles(): array
    {
        return [Role::Admin, Role::Sales];
    }

    public static function getNavigationBadge(): ?string
    {
        $count = Inquiry::where('status', InquiryStatus::New)->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->columns(3)->components([
            Group::make()->columnSpan(2)->schema([
                Section::make('Thông tin khách')->columns(2)->schema([
                    TextEntry::make('type')->label('Loại')->formatStateUsing(fn (InquiryType $state) => $state->label())->badge(),
                    TextEntry::make('created_at')->label('Gửi lúc')->dateTime('d/m/Y H:i'),
                    TextEntry::make('name')->label('Họ tên'),
                    TextEntry::make('phone')->label('Số điện thoại')->url(fn (Inquiry $record) => 'tel:'.$record->phone)->copyable(),
                    TextEntry::make('email')->label('Email')->placeholder('—'),
                    TextEntry::make('address')->label('Địa chỉ')->placeholder('—'),
                    TextEntry::make('subject')->label('Quan tâm')
                        ->state(fn (Inquiry $record) => $record->subjectLabel())
                        ->placeholder('—')
                        ->columnSpanFull(),
                    TextEntry::make('message')->label('Nội dung')->placeholder('—')->columnSpanFull(),
                    KeyValueEntry::make('meta')->label('Thông tin công trình')
                        ->keyLabel('Mục')->valueLabel('Giá trị')
                        ->visible(fn (Inquiry $record) => filled($record->meta))
                        ->columnSpanFull(),
                ]),
            ]),
            Section::make('Xử lý')->schema([
                Select::make('status')->label('Trạng thái')->options(InquiryStatus::options())->required(),
                Select::make('assigned_to')->label('Người phụ trách')
                    ->options(fn () => User::role([Role::Admin->value, Role::Sales->value])->pluck('name', 'id'))
                    ->searchable(),
                Textarea::make('internal_note')->label('Ghi chú nội bộ')->rows(6),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('id')->label('#')->sortable(),
                TextColumn::make('type')->label('Loại')->badge()->formatStateUsing(fn (InquiryType $state) => $state->label()),
                TextColumn::make('name')->label('Khách')->searchable()
                    ->description(fn (Inquiry $record) => $record->phone),
                TextColumn::make('phone')->label('SĐT')->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('subject_label')->label('Quan tâm')->state(fn (Inquiry $record) => $record->subjectLabel())->limit(40)->placeholder('—'),
                TextColumn::make('status')->label('Trạng thái')->badge()
                    ->formatStateUsing(fn (InquiryStatus $state) => $state->label())
                    ->color(fn (InquiryStatus $state) => $state->color()),
                TextColumn::make('assignee.name')->label('Phụ trách')->placeholder('—'),
                TextColumn::make('created_at')->label('Ngày gửi')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->label('Trạng thái')->options(InquiryStatus::options()),
                SelectFilter::make('type')->label('Loại')->options(InquiryType::options()),
            ])
            ->recordActions([EditAction::make()->label('Xử lý')]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInquiries::route('/'),
            'edit' => EditInquiry::route('/{record}/edit'),
        ];
    }
}
