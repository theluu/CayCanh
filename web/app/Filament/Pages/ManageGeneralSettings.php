<?php

namespace App\Filament\Pages;

use App\Enums\Role;
use App\Filament\Concerns\RestrictsToRoles;
use App\Settings\GeneralSettings;
use BackedEnum;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ManageGeneralSettings extends SettingsPage
{
    use RestrictsToRoles;

    protected static string $settings = GeneralSettings::class;

    protected static ?string $slug = 'cai-dat-chung';

    protected static ?string $title = 'Cài đặt chung';

    protected static ?string $navigationLabel = 'Cài đặt chung';

    protected static string|UnitEnum|null $navigationGroup = 'Hệ thống';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static function allowedRoles(): array
    {
        return [Role::Admin];
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Thông tin doanh nghiệp')->columns(2)->schema([
                TextInput::make('site_name')->label('Tên website')->required(),
                TextInput::make('hotline')->label('Hotline')->tel(),
                TextInput::make('email')->label('Email')->email(),
                TextInput::make('working_hours')->label('Giờ làm việc'),
                Textarea::make('address')->label('Địa chỉ')->columnSpanFull(),
                TextInput::make('map_embed_url')->label('Link nhúng Google Maps')->url()->columnSpanFull(),
            ]),
            Section::make('Kênh liên hệ ngoài')->columns(2)->schema([
                TextInput::make('zalo_phone')->label('SĐT Zalo'),
                TextInput::make('messenger_url')->label('Link Messenger')->url(),
                TextInput::make('facebook_url')->label('Facebook')->url(),
                TextInput::make('tiktok_url')->label('TikTok')->url(),
                TextInput::make('youtube_url')->label('YouTube')->url(),
            ]),
            Section::make('Số liệu trang chủ')->columns(3)->schema([
                TextInput::make('stats.years')->label('Năm kinh nghiệm')->numeric(),
                TextInput::make('stats.projects')->label('Công trình đã thực hiện')->numeric(),
                TextInput::make('stats.trees')->label('Cây đang chăm sóc')->numeric(),
            ]),
            Section::make('Thông báo cho nhân viên')->schema([
                TagsInput::make('notify_emails')->label('Email nhận thông báo')->placeholder('Nhập email rồi Enter'),
                TextInput::make('telegram_chat_id')->label('Telegram chat ID (nhóm kinh doanh)')
                    ->helperText('Bot token cấu hình trong biến môi trường TELEGRAM_BOT_TOKEN.'),
                Toggle::make('zalo_oa_enabled')->label('Bật thông báo Zalo OA')
                    ->helperText('Chỉ bật khi đã có Zalo OA được xác thực và ZALO_OA_ACCESS_TOKEN.'),
            ]),
        ]);
    }
}
