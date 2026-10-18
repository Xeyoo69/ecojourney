<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Admin — EcoJourney')</title>

    <meta name="robots" content="noindex, nofollow">
    <meta name="description" content="EcoJourney administration panel.">

    <script>
        (function () {
            var stored = null;
            try { stored = localStorage.getItem('ecojourney_theme'); } catch (e) { /* ignore */ }
            var dark = stored === 'dark' || (!stored && window.matchMedia('(prefers-color-scheme: dark)').matches);
            document.documentElement.classList.toggle('dark', dark);
        })();
    </script>

    @vite(['resources/css/app.css'])
</head>

<body class="admin-shell">

    @php
        $adminTabs = [
            ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'match' => 'admin.dashboard', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />'],
            ['label' => 'Destinations', 'route' => 'admin.destinations.index', 'match' => 'admin.destinations.*', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />'],
            ['label' => 'Hero Banner', 'route' => 'admin.hero.edit', 'match' => 'admin.hero.*', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 19.5h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5z" />'],
        ];

        $user = auth()->user();
        $initials = mb_strtoupper(mb_substr((string) $user->name, 0, 1));
    @endphp

    {{-- TOPBAR --}}
    <header class="admin-topbar">
        <div class="admin-topbar-inner">

            <a href="{{ route('admin.dashboard') }}" class="admin-brand" aria-label="EcoJourney admin">
                <span class="admin-brand-mark">
                    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21c4.5-2.2 7-5.6 7-10.5C19 6 15.8 3 12 3S5 6 5 10.5C5 15.4 7.5 18.8 12 21Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21V9" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 14c-2.2-1.4-3.7-3.1-4.5-5" />
                    </svg>
                </span>
                <span class="admin-brand-name">EcoJourney</span>
                <span class="admin-brand-badge">Admin</span>
            </a>

            <nav class="admin-tabs" aria-label="Admin navigation">
                @foreach ($adminTabs as $tab)
                    <a href="{{ route($tab['route']) }}" class="admin-tab {{ request()->routeIs($tab['match']) ? 'is-active' : '' }}">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7" aria-hidden="true">{!! $tab['icon'] !!}</svg>
                        {{ $tab['label'] }}
                    </a>
                @endforeach
            </nav>

            <div class="admin-actions">
                <button
                    type="button"
                    class="admin-icon-btn"
                    data-admin-theme
                    aria-label="Toggle colour theme"
                    title="Toggle colour theme"
                >
                    <svg data-theme-icon fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg>
                </button>

                <div class="admin-lang">
                    <a href="{{ route('lang.switch', 'id') }}" class="{{ app()->getLocale() === 'id' ? 'is-active' : '' }}">ID</a>
                    <a href="{{ route('lang.switch', 'en') }}" class="{{ app()->getLocale() === 'en' ? 'is-active' : '' }}">EN</a>
                </div>

                <a href="{{ route('home') }}" class="admin-ghost" target="_blank" rel="noopener">
                    View site
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                    </svg>
                </a>

                <div class="admin-user">
                    <span class="admin-avatar">{{ $initials }}</span>
                    <span class="admin-user-meta">
                        <span class="admin-user-name">{{ $user->name }}</span>
                        <span class="admin-user-role">{{ $user->role }}</span>
                    </span>
                </div>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="admin-icon-btn" aria-label="Sign out" title="Sign out">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                        </svg>
                    </button>
                </form>
            </div>

            <button
                type="button"
                class="admin-burger"
                id="admin-menu-btn"
                aria-expanded="false"
                aria-controls="admin-menu"
                aria-label="Open admin menu"
            >
                <svg width="19" height="19" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" aria-hidden="true">
                    <path d="M4 7h16M4 12h16M4 17h16" />
                </svg>
            </button>
        </div>

        {{-- MOBILE MENU --}}
        <div id="admin-menu" class="hidden">
            <div class="admin-menu-inner">
                @foreach ($adminTabs as $tab)
                    <a href="{{ route($tab['route']) }}" class="admin-tab {{ request()->routeIs($tab['match']) ? 'is-active' : '' }}">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7" aria-hidden="true">{!! $tab['icon'] !!}</svg>
                        {{ $tab['label'] }}
                    </a>
                @endforeach

                <div class="admin-menu-tools">
                    <div class="admin-lang">
                        <a href="{{ route('lang.switch', 'id') }}" class="{{ app()->getLocale() === 'id' ? 'is-active' : '' }}">ID</a>
                        <a href="{{ route('lang.switch', 'en') }}" class="{{ app()->getLocale() === 'en' ? 'is-active' : '' }}">EN</a>
                    </div>

                    <a href="{{ route('home') }}" class="admin-ghost" target="_blank" rel="noopener">View site</a>

                    <button type="button" class="admin-ghost" data-admin-theme>
                        <svg data-theme-icon width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg>
                        Theme
                    </button>

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="admin-ghost">Sign out</button>
                    </form>
                </div>
            </div>
        </div>
    </header>


    {{-- CONTENT --}}
    <main class="admin-main">
        @include('admin.partials.flash')
        @yield('content')
    </main>


    {{-- FOOTER --}}
    <footer class="admin-footer">
        <div class="admin-footer-inner">
            <p>&copy; {{ date('Y') }} EcoJourney — Admin panel</p>
            <p class="flex flex-wrap items-center gap-4">
                <span>Signed in as <strong class="admin-strong">{{ $user->email }}</strong></span>
                <a href="{{ route('home') }}">Back to site</a>
            </p>
        </div>
    </footer>


    <script>
        (function () {
            var btn = document.getElementById('admin-menu-btn');
            var menu = document.getElementById('admin-menu');

            if (btn && menu) {
                btn.addEventListener('click', function (event) {
                    event.stopPropagation();
                    var open = menu.classList.toggle('hidden') === false;
                    btn.setAttribute('aria-expanded', String(open));
                });

                document.addEventListener('click', function (event) {
                    if (menu.classList.contains('hidden')) { return; }
                    if (menu.contains(event.target) || btn.contains(event.target)) { return; }
                    menu.classList.add('hidden');
                    btn.setAttribute('aria-expanded', 'false');
                });

                document.addEventListener('keydown', function (event) {
                    if (event.key !== 'Escape' || menu.classList.contains('hidden')) { return; }
                    menu.classList.add('hidden');
                    btn.setAttribute('aria-expanded', 'false');
                    btn.focus();
                });
            }

            var MOON = '<path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>';
            var SUN = '<circle cx="12" cy="12" r="4"/><path d="M12 2v2m0 16v2M4.93 4.93l1.41 1.41m11.32 11.32 1.41 1.41M2 12h2m16 0h2M4.93 19.07l1.41-1.41m11.32-11.32 1.41-1.41"/>';

            function paintThemeIcons() {
                var dark = document.documentElement.classList.contains('dark');
                document.querySelectorAll('[data-theme-icon]').forEach(function (icon) {
                    icon.innerHTML = dark ? SUN : MOON;
                });
            }

            document.querySelectorAll('[data-admin-theme]').forEach(function (trigger) {
                trigger.addEventListener('click', function () {
                    var dark = !document.documentElement.classList.contains('dark');
                    document.documentElement.classList.toggle('dark', dark);
                    try { localStorage.setItem('ecojourney_theme', dark ? 'dark' : 'light'); } catch (e) { /* ignore */ }
                    paintThemeIcons();
                });
            });

            paintThemeIcons();

            document.addEventListener('submit', function (event) {
                var form = event.target;
                var message = form && form.dataset ? form.dataset.confirm : null;
                if (message && !window.confirm(message)) {
                    event.preventDefault();
                }
            });
        })();
    </script>

</body>
</html>
