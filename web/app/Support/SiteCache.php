<?php

namespace App\Support;

use Closure;
use Illuminate\Support\Facades\Cache;

/**
 * One cache namespace for everything rendered from site content.
 * Flushing bumps a version number, which works on every cache driver (tags are not required).
 */
class SiteCache
{
    private const VERSION_KEY = 'site-cache:version';

    private const TTL = 86400;

    /**
     * @template T
     *
     * @param  Closure(): T  $callback
     * @return T
     */
    public static function remember(string $key, Closure $callback): mixed
    {
        return Cache::remember(self::key($key), self::TTL, $callback);
    }

    public static function flush(): void
    {
        Cache::forever(self::VERSION_KEY, self::version() + 1);
    }

    private static function key(string $key): string
    {
        return 'site-cache:v'.self::version().':'.$key;
    }

    private static function version(): int
    {
        return (int) Cache::rememberForever(self::VERSION_KEY, fn () => 1);
    }
}
