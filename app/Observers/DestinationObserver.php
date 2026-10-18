<?php

namespace App\Observers;

use App\Models\Destination;
use App\Support\CacheKeys;

class DestinationObserver
{
    public function saved(Destination $destination): void
    {
        CacheKeys::flushSummaries();
    }

    public function deleted(Destination $destination): void
    {
        CacheKeys::flushSummaries();
    }
}
