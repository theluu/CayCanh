<?php

namespace App\Models\Concerns;

use App\Jobs\RegenerateSitemap;

trait TriggersSitemap
{
    public static function bootTriggersSitemap(): void
    {
        static::saved(function (self $model) {
            if ($model->is_published && ($model->wasRecentlyCreated || $model->wasChanged(['is_published', 'slug', 'published_at']))) {
                RegenerateSitemap::dispatch();
            }

            if (! $model->is_published && $model->wasChanged('is_published')) {
                RegenerateSitemap::dispatch();
            }
        });

        static::deleted(fn () => RegenerateSitemap::dispatch());
    }
}
