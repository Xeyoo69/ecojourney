<?php

namespace Tests\Unit;

use Tests\TestCase;

class ServiceWorkerSecurityTest extends TestCase
{
    public function test_service_worker_source_is_present(): void
    {
        $this->assertFileExists(base_path('public/sw.js'));
    }

    public function test_api_requests_are_excluded_before_any_cache_strategy_runs(): void
    {
        $source = $this->serviceWorkerSource();

        $fetchPosition = strpos($source, "addEventListener('fetch'");
        $apiGuardPosition = strpos($source, "pathname.startsWith('/api/')", (int) $fetchPosition);
        $cacheFirstPosition = strpos($source, 'event.respondWith(cacheFirst');

        $this->assertNotFalse($fetchPosition, 'fetch listener not found in sw.js');
        $this->assertNotFalse($apiGuardPosition, 'API guard missing from sw.js fetch listener');
        $this->assertNotFalse($cacheFirstPosition, 'cacheFirst strategy not found in sw.js');

        $this->assertGreaterThan($fetchPosition, $apiGuardPosition);
        $this->assertLessThan($cacheFirstPosition, $apiGuardPosition, '/api/ must be excluded before cacheFirst can run');
    }

    public function test_service_worker_script_itself_is_never_cached(): void
    {
        $source = $this->serviceWorkerSource();

        $this->assertStringContainsString("url.pathname === '/sw.js'", $source);
    }

    public function test_stale_api_entries_are_purged_on_activate(): void
    {
        $source = $this->serviceWorkerSource();

        $this->assertStringContainsString('purgeStrayApiEntries', $source);
        $this->assertStringContainsString('.then(() => purgeStrayApiEntries())', $source);
    }

    private function serviceWorkerSource(): string
    {
        $source = file_get_contents(base_path('public/sw.js'));

        $this->assertNotFalse($source);

        return $source;
    }
}
