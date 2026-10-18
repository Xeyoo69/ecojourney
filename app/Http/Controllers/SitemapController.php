<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use App\Support\CacheKeys;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $xml = Cache::remember(CacheKeys::SITEMAP_XML, now()->addHour(), fn (): string => $this->buildXml());

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
        ]);
    }

    public function robots(): Response
    {
        $content = "User-agent: *\nDisallow:\n\nSitemap: " . url('/sitemap.xml') . "\n";

        return response($content, 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }

    private function buildXml(): string
    {
        $pages = [
            ['loc' => url('/'), 'priority' => '1.0'],
            ['loc' => url('/explore'), 'priority' => '0.9'],
            ['loc' => url('/national'), 'priority' => '0.9'],
            ['loc' => url('/sustainability'), 'priority' => '0.6'],
            ['loc' => url('/about'), 'priority' => '0.5'],
            ['loc' => url('/contact'), 'priority' => '0.5'],
        ];

        $destinations = Destination::query()
            ->orderByDesc('sustainability_score')
            ->orderBy('name')
            ->get(['slug', 'updated_at']);

        $rows = '';
        foreach ($pages as $page) {
            $rows .= $this->urlEntry($page['loc'], null, $page['priority']);
        }

        foreach ($destinations as $destination) {
            $rows .= $this->urlEntry(
                url('/destinations/' . $destination->slug),
                $destination->updated_at?->toIso8601String(),
                '0.8'
            );
        }

        return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
{$rows}</urlset>
XML;
    }

    private function urlEntry(string $loc, ?string $lastmod, string $priority): string
    {
        $lastmodTag = $lastmod !== null ? "\n    <lastmod>{$lastmod}</lastmod>" : '';

        return <<<XML
  <url>
    <loc>{$loc}</loc>{$lastmodTag}
    <priority>{$priority}</priority>
  </url>

XML;
    }
}
