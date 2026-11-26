<?php

use App\Settings\GeneralSettings;

it('shares public site settings with every inertia page', function () {
    $settings = app(GeneralSettings::class);
    $settings->hotline = '0912345678';
    $settings->telegram_chat_id = '-100123';
    $settings->save();

    $this->get('/')->assertInertia(fn ($page) => $page
        ->where('site.hotline', '0912345678')
        ->missing('site.telegram_chat_id')
        ->missing('site.notify_emails'));
});
