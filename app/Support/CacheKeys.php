<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

class CacheKeys
{
    public const NATIONAL_PAYLOAD = 'national.payload';

    public const HOME_PAYLOAD = 'home.payload';

    public const EXPLORE_FILTERS = 'explore.filters';

    public const SITEMAP_XML = 'sitemap.xml';

    public static function flushSummaries(): void
    {
        Cache::forget(self::NATIONAL_PAYLOAD);
        Cache::forget(self::HOME_PAYLOAD);
        Cache::forget(self::EXPLORE_FILTERS);
        Cache::forget(self::SITEMAP_XML);
    }
}
