<?php

namespace App\Observers;

use App\Support\SiteCache;

/**
 * Attached to every model that feeds public pages; any write invalidates the site cache.
 */
class FlushSiteCache
{
    public function saved(): void
    {
        SiteCache::flush();
    }

    public function deleted(): void
    {
        SiteCache::flush();
    }
}
