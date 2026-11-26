<?php

namespace App\Filament\Resources\Orders;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\Role;
use App\Filament\Concerns\RestrictsToRoles;
use App\Filament\Resources\Orders\Pages\EditOrder;
use App\Filament\Resources\Orders\Pages\ListOrders;
use App\Models\Order;
use App\Support\Money;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\RepeatableEntry\TableColumn;
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

class OrderResource extends Resource
{
    use RestrictsToRoles;

    protected static ?string $model = Order::class;

    protected static ?string $modelLabel = 'đơn hàng';

    protected static ?string $pluralModelLabel = 'Đơn hàng';

    protected static string|UnitEnum|null $navigationGroup = 'Bán hàng';

    protected static ?int $navigationSort = 2;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingCart;

    protected static ?string $recordTitleAttribute = 'code';

    protected static function allowedRoles(): array
    {
        return [Role::Admin, Role::Sales];
    }

    public static function getNavigationBadge(): ?string
    {
        $count = Order::where('status', OrderStatus::New)->count();

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
                Section::make('Khách hàng')->columns(2)->schema([
                    TextEntry::make('code')->label('Mã đơn')->weight('bold')->copyable(),
                    TextEntry::make('created_at')->label('Đặt lúc')->dateTime('d/m/Y H:i'),
                    TextEntry::make('customer_name')->label('Họ tên'),
                    TextEntry::make('phone')->label('Số điện thoại')->url(fn (Order $record) => 'tel:'.$record->phone)->copyable(),
                    TextEntry::make('email')->label('Email')->placeholder('—'),
                    TextEntry::make('payment_method')->label('Thanh toán')->formatStateUsing(fn (PaymentMethod $state) => $state->label()),
                    TextEntry::make('full_address')->label('Địa chỉ giao')->state(fn (Order $record) => $record->address.', '.$record->province)->columnSpanFull(),
                    TextEntry::make('note')->label('Ghi chú của khách')->placeholder('—')->columnSpanFull(),
                ]),
                Section::make('Sản phẩm')->schema([
                    RepeatableEntry::make('items')->label('')
                        ->table([
                            TableColumn::make('Sản phẩm'),
                            TableColumn::make('Đơn giá'),
                            TableColumn::make('SL'),
                            TableColumn::make('Thành tiền'),
                        ])
                        ->schema([
                            TextEntry::make('product_name'),
                            TextEntry::make('unit_price')->formatStateUsing(fn (int $state) => Money::vnd($state)),
                            TextEntry::make('qty'),
                            TextEntry::make('line_total')->formatStateUsing(fn (int $state) => Money::vnd($state)),
                        ]),
                    TextEntry::make('subtotal')->label('Tổng tạm tính')->formatStateUsing(fn (int $state) => Money::vnd($state))->weight('bold'),
                ]),
            ]),
            Section::make('Xử lý đơn')->schema([
                Select::make('status')->label('Trạng thái')->options(OrderStatus::options())->required(),
                Textarea::make('admin_note')->label('Ghi chú nội bộ')->rows(6),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('code')->label('Mã đơn')->searchable()->weight('bold'),
                TextColumn::make('customer_name')->label('Khách')->searchable()->description(fn (Order $record) => $record->phone),
                TextColumn::make('phone')->label('SĐT')->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('province')->label('Tỉnh/TP')->toggleable(),
                TextColumn::make('subtotal')->label('Tạm tính')->formatStateUsing(fn (int $state) => Money::vnd($state))->sortable(),
                TextColumn::make('status')->label('Trạng thái')->badge()
                    ->formatStateUsing(fn (OrderStatus $state) => $state->label())
                    ->color(fn (OrderStatus $state) => $state->color()),
                TextColumn::make('created_at')->label('Ngày đặt')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->label('Trạng thái')->options(OrderStatus::options()),
            ])
            ->recordActions([EditAction::make()->label('Xử lý')]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrders::route('/'),
            'edit' => EditOrder::route('/{record}/edit'),
        ];
    }
}
