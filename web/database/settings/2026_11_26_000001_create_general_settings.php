<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.site_name', 'Cây Cảnh');
        $this->migrator->add('general.hotline', null);
        $this->migrator->add('general.email', null);
        $this->migrator->add('general.address', null);
        $this->migrator->add('general.working_hours', 'Thứ 2 – Chủ nhật: 7:30 – 18:00');
        $this->migrator->add('general.map_embed_url', null);
        $this->migrator->add('general.zalo_phone', null);
        $this->migrator->add('general.facebook_url', null);
        $this->migrator->add('general.messenger_url', null);
        $this->migrator->add('general.tiktok_url', null);
        $this->migrator->add('general.youtube_url', null);
        $this->migrator->add('general.notify_emails', []);
        $this->migrator->add('general.telegram_chat_id', null);
        $this->migrator->add('general.zalo_oa_enabled', false);
        $this->migrator->add('general.stats', ['years' => 10, 'projects' => 200, 'trees' => 5000]);
    }
};
