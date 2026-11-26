<?php

namespace App\Support;

use App\Settings\GeneralSettings;

class JsonLd
{
    /** @return array<string, mixed> */
    public static function localBusiness(GeneralSettings $settings): array
    {
        return array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'LocalBusiness',
            'name' => $settings->site_name,
            'url' => url('/'),
            'telephone' => $settings->hotline,
            'email' => $settings->email,
            'address' => $settings->address ? [
                '@type' => 'PostalAddress',
                'streetAddress' => $settings->address,
                'addressCountry' => 'VN',
            ] : null,
            'openingHours' => $settings->working_hours,
            'sameAs' => array_values(array_filter([$settings->facebook_url, $settings->tiktok_url, $settings->youtube_url])),
        ], fn ($value) => $value !== null && $value !== []);
    }
}
