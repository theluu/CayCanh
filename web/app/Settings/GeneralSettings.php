<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public string $site_name;

    public ?string $hotline;

    public ?string $email;

    public ?string $address;

    public ?string $working_hours;

    public ?string $map_embed_url;

    public ?string $zalo_phone;

    public ?string $facebook_url;

    public ?string $messenger_url;

    public ?string $tiktok_url;

    public ?string $youtube_url;

    /** @var array<int, string> */
    public array $notify_emails;

    public ?string $telegram_chat_id;

    public bool $zalo_oa_enabled;

    /** @var array<string, int> years, projects, trees */
    public array $stats;

    public static function group(): string
    {
        return 'general';
    }

    /** @return array<string, mixed> */
    public function publicData(): array
    {
        return collect($this->toArray())
            ->except(['notify_emails', 'telegram_chat_id', 'zalo_oa_enabled'])
            ->all();
    }
}
