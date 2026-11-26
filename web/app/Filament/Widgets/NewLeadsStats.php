<?php

namespace App\Filament\Widgets;

use App\Enums\ArtAvailability;
use App\Enums\InquiryStatus;
use App\Enums\OrderStatus;
use App\Enums\Role;
use App\Models\Inquiry;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class NewLeadsStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $pollingInterval = '60s';

    public static function canView(): bool
    {
        $user = auth()->user();

        return $user instanceof User && $user->hasAnyStaffRole(Role::Admin, Role::Sales);
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Yêu cầu mới', (string) Inquiry::where('status', InquiryStatus::New)->count())
                ->description('Liên hệ / báo giá chưa xử lý')
                ->color('danger')
                ->url('/admin/inquiries?filters[status][value]=new'),
            Stat::make('Đơn hàng mới', (string) Order::where('status', OrderStatus::New)->count())
                ->description('Chưa gọi xác nhận')
                ->color('warning')
                ->url('/admin/orders?filters[status][value]=new'),
            Stat::make('Cây đang giữ', (string) Product::art()->where('availability', ArtAvailability::Reserved)->count())
                ->description('Cây nghệ thuật đang thương lượng')
                ->color('info'),
        ];
    }
}
