<?php

namespace App\Notifications\Channels;

use App\Settings\GeneralSettings;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Sends a plain-text Zalo OA message to the configured staff user ids.
 * Disabled until the business has a verified OA (settings.zalo_oa_enabled + ZALO_OA_ACCESS_TOKEN).
 */
class ZaloOaChannel
{
    private const ENDPOINT = 'https://openapi.zalo.me/v3.0/oa/message/cs';

    public function send(object $notifiable, Notification $notification): void
    {
        $token = config('services.zalo_oa.access_token');
        $recipients = (array) config('services.zalo_oa.recipients', []);

        if (! app(GeneralSettings::class)->zalo_oa_enabled || blank($token) || $recipients === [] || ! method_exists($notification, 'toZaloOa')) {
            return;
        }

        $text = $notification->toZaloOa($notifiable);

        foreach ($recipients as $userId) {
            $response = Http::withHeaders(['access_token' => $token])
                ->timeout(10)
                ->post(self::ENDPOINT, [
                    'recipient' => ['user_id' => $userId],
                    'message' => ['text' => $text],
                ]);

            if ($response->failed() || (int) $response->json('error', 0) !== 0) {
                Log::warning('Zalo OA message failed', ['user_id' => $userId, 'body' => $response->body()]);
            }
        }
    }
}
