<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SpaController extends Controller
{
    private const DEFAULT_TITLE = 'EcoJourney — Pariwisata Berkelanjutan Cerdas';

    private const DEFAULT_DESCRIPTION = 'Platform wisata berkelanjutan untuk menjelajahi destinasi eco-friendly di seluruh Indonesia — dari Sumatera hingga Papua.';

    private const DEFAULT_IMAGE = 'https://images.unsplash.com/photo-1559827260-dc66d52bef19?auto=format&fit=crop&w=1200&q=80';

    public function __invoke(Request $request): View|Response
    {
        $path = '/' . trim($request->path(), '/');

        if ($path === '//') {
            $path = '/';
        }

        if (str_starts_with($path, '/destinations/')) {
            return $this->destinationSeo($request, substr($path, strlen('/destinations/')));
        }

        [$title, $description, $robots] = match ($path) {
            '/' => [
                self::DEFAULT_TITLE,
                self::DEFAULT_DESCRIPTION,
                'index, follow',
            ],
            '/explore' => [
                'Jelajahi Destinasi Eco di Seluruh Indonesia | EcoJourney',
                'Temukan destinasi berkelanjutan berdasarkan wilayah, kategori, dan skor eco. Dari pantai, hutan, hingga situs budaya di 7 wilayah kepulauan.',
                'index, follow',
            ],
            '/national' => [
                'Peta Nasional Wisata Berkelanjutan | EcoJourney',
                'Peta interaktif 131+ destinasi eco di Indonesia lengkap dengan statistik wilayah, skor keberlanjutan, dan rekomendasi terbaik.',
                'index, follow',
            ],
            '/sustainability' => [
                'Keberlanjutan | EcoJourney',
                'Cara EcoJourney mengukur dampak pariwisata: skor eco, konservasi, komunitas lokal, transportasi ramah lingkungan, dan pengelolaan sampah.',
                'index, follow',
            ],
            '/about' => [
                'Tentang EcoJourney | Platform Wisata Bertanggung Jawab',
                'EcoJourney membantu wisatawan menemukan dan merencanakan perjalanan berkelanjutan di Indonesia dengan data transparan tentang destinasi eco.',
                'index, follow',
            ],
            '/contact' => [
                'Kontak | EcoJourney',
                'Hubungi tim EcoJourney untuk kolaborasi, pertanyaan, atau masukan tentang destinasi berkelanjutan di Indonesia.',
                'index, follow',
            ],
            '/planner' => [
                'Perencana Perjalanan | EcoJourney',
                'Rencanakan itinerary perjalanan berkelanjutan sesuai durasi dan preferensi kamu.',
                'noindex, nofollow',
            ],
            '/login' => [
                'Masuk | EcoJourney',
                'Masuk ke akun EcoJourney kamu.',
                'noindex, nofollow',
            ],
            '/register' => [
                'Daftar | EcoJourney',
                'Buat akun EcoJourney untuk menyimpan destinasi favorit dan merencanakan perjalanan.',
                'noindex, nofollow',
            ],
            default => [
                self::DEFAULT_TITLE,
                self::DEFAULT_DESCRIPTION,
                'noindex, follow',
            ],
        };

        if (preg_match('#^/planner/result/\d+$#', $path)) {
            $title = 'Hasil Perjalanan | EcoJourney';
            $description = 'Hasil itinerary perjalanan berkelanjutan kamu.';
            $robots = 'noindex, nofollow';
        }

        if ($path === '/favorites') {
            $robots = 'noindex, nofollow';
        }

        $seo = [
            'title' => $title,
            'description' => $description,
            'image' => self::DEFAULT_IMAGE,
            'canonical' => url($request->getRequestUri() === '/' ? '/' : $path),
            'robots' => $robots,
            'type' => 'website',
        ];

        $knownPaths = ['/', '/explore', '/national', '/sustainability', '/about', '/contact', '/login', '/register', '/favorites', '/planner'];
        $isKnown = in_array($path, $knownPaths, true) || preg_match('#^/planner/result/\d+$#', $path) === 1;

        if (! $isKnown) {
            return response()->view('spa', ['seo' => $seo], 404);
        }

        return view('spa', ['seo' => $seo]);
    }

    private function destinationSeo(Request $request, string $slug): View|Response
    {
        $destination = Destination::with('category')->where('slug', $slug)->first();

        if (! $destination) {
            return response()->view('spa', [
                'seo' => [
                    'title' => 'Halaman tidak ditemukan | EcoJourney',
                    'description' => self::DEFAULT_DESCRIPTION,
                    'image' => self::DEFAULT_IMAGE,
                    'canonical' => url('/destinations/' . $slug),
                    'robots' => 'noindex',
                    'type' => 'website',
                ],
            ], 404);
        }

        $description = Str::limit(strip_tags($destination->description), 155);
        $image = $destination->image_url;

        return response()->view('spa', [
            'seo' => [
                'title' => "{$destination->name} — {$destination->location} | EcoJourney",
                'description' => $description,
                'image' => $image,
                'canonical' => url('/destinations/' . $destination->slug),
                'robots' => 'index, follow',
                'type' => 'article',
                'jsonLd' => [
                    '@context' => 'https://schema.org',
                    '@type' => 'TouristDestination',
                    'name' => $destination->name,
                    'description' => $description,
                    'url' => url('/destinations/' . $destination->slug),
                    'image' => $image,
                    'touristType' => 'Eco-conscious travelers',
                    'geo' => [
                        '@type' => 'GeoCoordinates',
                        'latitude' => (float) $destination->latitude,
                        'longitude' => (float) $destination->longitude,
                    ],
                    'address' => [
                        '@type' => 'PostalAddress',
                        'addressLocality' => $destination->location,
                        'addressCountry' => 'ID',
                    ],
                    'aggregateRating' => [
                        '@type' => 'AggregateRating',
                        'ratingValue' => (int) $destination->sustainability_score,
                        'bestRating' => 100,
                        'worstRating' => 0,
                    ],
                ],
            ],
        ]);
    }
}
