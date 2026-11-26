<?php

namespace App\Support;

use App\Notifications\Channels\ZaloOaChannel;
use App\Settings\GeneralSettings;
use Illuminate\Notifications\AnonymousNotifiable;

/**
 * Builds the on-demand notifiable for the sales team from the admin settings.
 * A channel is only routed when it is fully configured.
 */
class StaffRecipients
{
    public static function route(): AnonymousNotifiable
    {
        $settings = app(GeneralSettings::class);
        $notifiable = new AnonymousNotifiable;

        $emails = array_values(array_filter($settings->notify_emails ?? [], fn ($email) => filter_var($email, FILTER_VALIDATE_EMAIL)));
        if ($emails !== []) {
            $notifiable->route('mail', $emails);
        }

        if (filled($settings->telegram_chat_id) && filled(config('services.telegram-bot-api.token'))) {
            $notifiable->route('telegram', $settings->telegram_chat_id);
        }

        return $notifiable;
    }

    /**
     * @return array<int, string>
     */
    public static function channelsFor(object $notifiable): array
    {
        $channels = $notifiable instanceof AnonymousNotifiable
            ? array_values(array_intersect(['mail', 'telegram'], array_keys($notifiable->routes)))
            : ['mail'];

        if (app(GeneralSettings::class)->zalo_oa_enabled) {
            $channels[] = ZaloOaChannel::class;
        }

        return $channels;
    }
}
