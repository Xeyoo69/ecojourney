<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#31583d">
    <script>
        (function () {
            try {
                var stored = localStorage.getItem('ecojourney_theme');
                var dark = stored ? stored === 'dark' : window.matchMedia('(prefers-color-scheme: dark)').matches;
                if (dark) document.documentElement.classList.add('dark');
            } catch (e) { /* storage unavailable — keep default theme */ }
        })();
    </script>
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
    <link rel="icon" type="image/png" href="{{ asset('images/logo-ecojourney.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('icons/apple-touch-icon.png') }}">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="EcoJourney">
    <title>{{ $seo['title'] ?? 'EcoJourney' }}</title>
    <meta name="description" content="{{ $seo['description'] ?? 'EcoJourney — Smart Sustainable Tourism Platform' }}">
    @isset($seo['robots'])
        <meta name="robots" content="{{ $seo['robots'] }}">
    @endisset
    @isset($seo['canonical'])
        <link rel="canonical" href="{{ $seo['canonical'] }}">
    @endisset
    <meta property="og:site_name" content="EcoJourney">
    <meta property="og:type" content="{{ $seo['type'] ?? 'website' }}">
    <meta property="og:title" content="{{ $seo['title'] ?? 'EcoJourney' }}">
    <meta property="og:description" content="{{ $seo['description'] ?? 'EcoJourney — Smart Sustainable Tourism Platform' }}">
    @isset($seo['canonical'])
        <meta property="og:url" content="{{ $seo['canonical'] }}">
    @endisset
    @isset($seo['image'])
        <meta property="og:image" content="{{ $seo['image'] }}">
    @endisset
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seo['title'] ?? 'EcoJourney' }}">
    <meta name="twitter:description" content="{{ $seo['description'] ?? 'EcoJourney — Smart Sustainable Tourism Platform' }}">
    @isset($seo['image'])
        <meta name="twitter:image" content="{{ $seo['image'] }}">
    @endisset
    @isset($seo['jsonLd'])
        <script type="application/ld+json">{!! json_encode($seo['jsonLd'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @endisset
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div id="app" data-locale="{{ app()->getLocale() }}"></div>
    <noscript>
        <div class="container-eco py-12 text-center">
            EcoJourney needs JavaScript enabled.
        </div>
    </noscript>
</body>
</html>
