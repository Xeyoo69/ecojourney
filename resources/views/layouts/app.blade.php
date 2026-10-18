<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        @yield('title', 'EcoJourney')
    </title>

    <meta
        name="description"
        content="EcoJourney — Smart Sustainable Tourism Platform"
    >

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>

    {{-- NAVBAR --}}
    <header id="site-header" class="sticky top-0 z-50 border-b border-black/[0.06] bg-white/80 backdrop-blur-xl transition-all duration-300">
        <nav class="container-eco flex h-[72px] items-center justify-between gap-6 sm:h-[76px]">

            {{-- LOGO --}}
            <a
                href="{{ url('/') }}"
                class="group flex items-center gap-3"
            >
                <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-forest-700 text-white shadow-lg shadow-forest-700/15 transition duration-300 group-hover:scale-105 group-hover:shadow-xl">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 21c4.5-2.2 7-5.6 7-10.5C19 6 15.8 3 12 3S5 6 5 10.5C5 15.4 7.5 18.8 12 21Z"
                        />
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 21V9"
                        />
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 14c-2.2-1.4-3.7-3.1-4.5-5"
                        />
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 12c1.8-1.2 3.3-2.7 4.2-4.5"
                        />
                    </svg>
                </div>

                <div>
                    <div class="font-display text-lg font-extrabold tracking-tight text-forest-900">
                        EcoJourney
                    </div>

                    <div class="-mt-1 text-[10px] font-semibold uppercase tracking-[0.18em] text-gray-400">
                        Travel Responsibly
                    </div>
                </div>
            </a>


            {{-- DESKTOP NAV --}}
            <div class="hidden items-center gap-7 md:flex">

                <a
                    href="{{ url('/') }}"
                    class="nav-link {{ request()->is('/') ? 'active' : '' }}"
                >
                    {{ __('Dashboard') }}
                </a>

                <a
                    href="{{ url('/explore') }}"
                    class="nav-link {{ request()->is('explore*') ? 'active' : '' }}"
                >
                    {{ __('Explore') }}
                </a>

                <a
                    href="{{ url('/planner') }}"
                    class="nav-link {{ request()->is('planner*') ? 'active' : '' }}"
                >
                    {{ __('Planner') }}
                </a>

                <a
                    href="{{ url('/sustainability') }}"
                    class="nav-link {{ request()->is('sustainability*') ? 'active' : '' }}"
                >
                    {{ __('Sustainability') }}
                </a>

                <a
                    href="{{ url('/about') }}"
                    class="nav-link {{ request()->is('about*') ? 'active' : '' }}"
                >
                    {{ __('About') }}
                </a>
            </div>


            {{-- RIGHT --}}
            <div class="hidden items-center gap-3 md:flex">

                {{-- LANGUAGE SWITCHER --}}
                <div class="flex items-center gap-1 rounded-full border border-gray-200 bg-gray-50 p-1 text-xs font-bold shadow-sm">
                    <a href="{{ route('lang.switch', 'id') }}" class="rounded-full px-2.5 py-1 transition {{ app()->getLocale() === 'id' ? 'bg-forest-700 text-white shadow-sm' : 'text-gray-600 hover:text-forest-700' }}" title="Bahasa Indonesia">ID</a>
                    <a href="{{ route('lang.switch', 'en') }}" class="rounded-full px-2.5 py-1 transition {{ app()->getLocale() === 'en' ? 'bg-forest-700 text-white shadow-sm' : 'text-gray-600 hover:text-forest-700' }}" title="English">EN</a>
                </div>

                <a
                    href="{{ url('/favorites') }}"
                    class="group flex h-11 w-11 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-500 shadow-sm transition duration-300 hover:-translate-y-0.5 hover:border-forest-200 hover:bg-forest-50 hover:text-forest-700 hover:shadow-md"
                    title="Favorites"
                    aria-label="Favorites"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M20.8 8.7c0 5.5-8.8 10.3-8.8 10.3S3.2 14.2 3.2 8.7A4.7 4.7 0 0 1 12 6.2a4.7 4.7 0 0 1 8.8 2.5Z"
                        />
                    </svg>
                </a>

                <a
                    href="{{ url('/explore') }}"
                    class="eco-btn eco-btn-primary"
                >
                    {{ __('Start Exploring') }}

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M5 12h14M13 6l6 6-6 6"
                        />
                    </svg>
                </a>

                @auth
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-1.5 rounded-full bg-amber-500/10 border border-amber-500/20 px-4 py-2 text-xs font-bold text-amber-700 shadow-sm hover:bg-amber-500/20 transition">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Admin Panel
                        </a>
                    @endif
                    <div class="flex items-center gap-2 pl-2 border-l border-gray-200">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-forest-100 text-forest-800 text-xs font-bold">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <div class="hidden xl:block text-left">
                            <div class="text-xs font-bold text-gray-800 leading-none">{{ auth()->user()->name }}</div>
                            <div class="text-[10px] text-gray-500 uppercase tracking-wider mt-0.5">{{ auth()->user()->role }}</div>
                        </div>
                        <form action="{{ route('logout') }}" method="POST" class="inline ml-2">
                            @csrf
                            <button type="submit" class="flex h-9 w-9 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-500 shadow-sm transition hover:border-red-200 hover:bg-red-50 hover:text-red-600" title="Sign Out" aria-label="Sign Out">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                            </button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-xs font-bold text-forest-700 hover:text-forest-900 px-3 py-2 transition">
                        Sign In
                    </a>
                    <a href="{{ route('register') }}" class="eco-btn eco-btn-primary py-2 px-4 text-xs">
                        Register
                    </a>
                @endauth
            </div>


            {{-- MOBILE BUTTON --}}
            <button
                type="button"
                id="mobile-menu-button"
                class="flex h-11 w-11 items-center justify-center rounded-full border border-gray-200 bg-white shadow-sm transition hover:bg-gray-50 md:hidden"
                aria-expanded="false"
                aria-controls="mobile-menu"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M4 7h16M4 12h16M4 17h16"
                    />
                </svg>
            </button>

        </nav>


        {{-- MOBILE MENU --}}
        <div
            id="mobile-menu"
            class="hidden border-t border-gray-100 bg-white/95 backdrop-blur-xl md:hidden"
        >
            <div class="container-eco flex flex-col gap-1 py-4 animate-slide-down">

                {{-- MOBILE LANGUAGE SWITCHER --}}
                <div class="mb-2 flex items-center justify-between rounded-xl bg-gray-50 p-3">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Language / Bahasa</span>
                    <div class="flex items-center gap-1 rounded-full border border-gray-200 bg-white p-1 text-xs font-bold shadow-sm">
                        <a href="{{ route('lang.switch', 'id') }}" class="rounded-full px-3 py-1 transition {{ app()->getLocale() === 'id' ? 'bg-forest-700 text-white shadow-sm' : 'text-gray-600 hover:text-forest-700' }}">ID</a>
                        <a href="{{ route('lang.switch', 'en') }}" class="rounded-full px-3 py-1 transition {{ app()->getLocale() === 'en' ? 'bg-forest-700 text-white shadow-sm' : 'text-gray-600 hover:text-forest-700' }}">EN</a>
                    </div>
                </div>

                <a
                    href="{{ url('/explore') }}"
                    class="rounded-xl px-4 py-3 text-sm font-semibold text-gray-700 transition hover:bg-forest-50 hover:text-forest-700"
                >
                    {{ __('Explore') }}
                </a>

                <a
                    href="{{ url('/planner') }}"
                    class="rounded-xl px-4 py-3 text-sm font-semibold text-gray-700 transition hover:bg-forest-50 hover:text-forest-700"
                >
                    {{ __('Planner') }}
                </a>

                <a
                    href="{{ url('/sustainability') }}"
                    class="rounded-xl px-4 py-3 text-sm font-semibold text-gray-700 transition hover:bg-forest-50 hover:text-forest-700"
                >
                    {{ __('Sustainability') }}
                </a>

                <a
                    href="{{ url('/about') }}"
                    class="rounded-xl px-4 py-3 text-sm font-semibold text-gray-700 transition hover:bg-forest-50 hover:text-forest-700"
                >
                    {{ __('About') }}
                </a>

                <a
                    href="{{ url('/favorites') }}"
                    class="rounded-xl px-4 py-3 text-sm font-semibold text-gray-700 transition hover:bg-forest-50 hover:text-forest-700"
                >
                    {{ __('Favorites') }}
                </a>

                <a
                    href="{{ url('/explore') }}"
                    class="eco-btn eco-btn-primary mt-2 shadow-lg"
                >
                    {{ __('Start Exploring') }}
                </a>

            </div>
        </div>
    </header>


    {{-- PAGE CONTENT --}}
    <main>
        @yield('content')
    </main>


    {{-- FOOTER --}}
    <footer class="mt-24 border-t border-gray-200 bg-white">
        <div class="container-eco py-14">
            <div class="grid gap-10 md:grid-cols-12">
                {{-- BRAND --}}
                <div class="md:col-span-5">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-forest-700 text-white shadow-md">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21c4.5-2.2 7-5.6 7-10.5C19 6 15.8 3 12 3S5 6 5 10.5C5 15.4 7.5 18.8 12 21Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21V9" />
                            </svg>
                        </div>
                        <span class="font-display text-xl font-extrabold tracking-tight text-forest-900">EcoJourney</span>
                    </div>
                    <p class="mt-5 max-w-md text-sm leading-7 text-gray-500">
                        {{ __('Discover meaningful destinations, plan smarter journeys, and make travel choices that are better for the places you visit.') }}
                    </p>
                    <div class="mt-6 flex items-center gap-3">
                        <a href="#" class="flex h-9 w-9 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-500 transition hover:border-forest-200 hover:bg-forest-50 hover:text-forest-700" aria-label="Instagram">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1.2" fill="currentColor" stroke="none"/></svg>
                        </a>
                        <a href="#" class="flex h-9 w-9 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-500 transition hover:border-forest-200 hover:bg-forest-50 hover:text-forest-700" aria-label="Twitter">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6"><path stroke-linecap="round" stroke-linejoin="round" d="M22 5.8c-.7.3-1.5.6-2.3.7.8-.5 1.4-1.2 1.7-2.1-.8.5-1.6.8-2.5 1-1.4-1.5-3.8-1.5-5.2 0-1 1-1.3 2.5-.8 3.8-2.1-.1-4-1-5.4-2.6-.7 1.2-.3 2.7.9 3.5-.6 0-1.2-.2-1.7-.5 0 1.7 1.2 3.2 2.9 3.5-.5.1-1 .1-1.5 0 .4 1.4 1.8 2.4 3.3 2.4A7.7 7.7 0 0 1 2 17.3 10.9 10.9 0 0 0 8 19c6 0 9.3-5 9.3-9.3v-.4A6.6 6.6 0 0 0 22 5.8Z"/></svg>
                        </a>
                        <a href="#" class="flex h-9 w-9 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-500 transition hover:border-forest-200 hover:bg-forest-50 hover:text-forest-700" aria-label="YouTube">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6"><path stroke-linecap="round" stroke-linejoin="round" d="M14 12 10 8.5v7L14 12Z"/><rect x="2" y="6" width="20" height="12" rx="3"/></svg>
                        </a>
                    </div>
                </div>
                {{-- EXPLORE --}}
                <div class="md:col-span-3">
                    <h3 class="font-display text-sm font-bold text-gray-900">{{ __('Explore') }}</h3>
                    <div class="mt-4 flex flex-col gap-3 text-sm text-gray-500">
                        <a href="{{ url('/') }}" class="footer-link w-fit">{{ __('Dashboard') }}</a>
                        <a href="{{ url('/explore') }}" class="footer-link w-fit">{{ __('Destinations') }}</a>
                        <a href="{{ url('/planner') }}" class="footer-link w-fit">{{ __('Trip Planner') }}</a>
                        <a href="{{ url('/favorites') }}" class="footer-link w-fit">{{ __('Favorites') }}</a>
                    </div>
                </div>
                {{-- COMPANY --}}
                <div class="md:col-span-4">
                    <h3 class="font-display text-sm font-bold text-gray-900">EcoJourney</h3>
                    <div class="mt-4 flex flex-col gap-3 text-sm text-gray-500">
                        <a href="{{ url('/sustainability') }}" class="footer-link w-fit">{{ __('Sustainability') }}</a>
                        <a href="{{ url('/about') }}" class="footer-link w-fit">{{ __('About Us') }}</a>
                        <a href="{{ url('/contact') }}" class="footer-link w-fit">{{ __('Contact') }}</a>
                    </div>
                    <div class="mt-6 rounded-2xl border border-forest-100 bg-forest-50/70 p-4">
                        <div class="text-xs font-bold uppercase tracking-wider text-forest-700">{{ __('Travel responsibly') }}</div>
                        <p class="mt-1 text-xs leading-5 text-forest-800/70">{{ __('Get curated low-impact destinations and planning tips.') }}</p>
                        <a href="{{ url('/explore') }}" class="mt-3 inline-flex items-center gap-1.5 text-xs font-bold text-forest-700 hover:gap-2">{{ __('Explore now') }} <span aria-hidden="true">→</span></a>
                    </div>
                </div>
            </div>
            <div class="mt-12 flex flex-col gap-3 border-t border-gray-100 pt-6 text-xs text-gray-400 sm:flex-row sm:items-center sm:justify-between">
                <p>© {{ date('Y') }} EcoJourney. {{ __('Built for sustainable travel.') }}</p>
                <p class="flex items-center gap-2"><span class="h-1.5 w-1.5 rounded-full bg-forest-400"></span> {{ __('Travel better. Leave a lighter footprint.') }}</p>
            </div>
        </div>
    </footer>


    <script>
        const mobileButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        const siteHeader = document.getElementById('site-header');

        if (mobileButton && mobileMenu) {
            mobileButton.addEventListener('click', () => {
                const isHidden = mobileMenu.classList.toggle('hidden');
                mobileButton.setAttribute('aria-expanded', String(!isHidden));
            });
            document.addEventListener('click', (e) => {
                if (!mobileMenu.classList.contains('hidden') && !siteHeader.contains(e.target)) {
                    mobileMenu.classList.add('hidden');
                    mobileButton.setAttribute('aria-expanded', 'false');
                }
            });
        }

        const onScroll = () => {
            if (!siteHeader) return;
            if (window.scrollY > 8) siteHeader.classList.add('is-scrolled');
            else siteHeader.classList.remove('is-scrolled');
        };
        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();

        const revealEls = document.querySelectorAll('.reveal-on-scroll');
        if ('IntersectionObserver' in window && revealEls.length) {
            const io = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        io.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.15 });
            revealEls.forEach((el) => io.observe(el));
        } else {
            revealEls.forEach((el) => el.classList.add('is-visible'));
        }

        const IS_AUTH = {{ auth()->check() ? 'true' : 'false' }};
        const LOGIN_URL = "{{ route('login') }}";

        const FAV_KEY = 'ecojourney_favorites';
        const getFavs = () => {
            try { return JSON.parse(localStorage.getItem(FAV_KEY)) || []; }
            catch (e) { return []; }
        };
        const setFavs = (favs) => localStorage.setItem(FAV_KEY, JSON.stringify(favs));
        const paintFavButtons = () => {
            const favs = getFavs();
            document.querySelectorAll('[data-favorite-toggle]').forEach((btn) => {
                const slug = btn.getAttribute('data-favorite-toggle');
                const active = favs.includes(slug);
                btn.classList.toggle('!text-red-500', active);
                btn.querySelector('svg')?.setAttribute('fill', active ? 'currentColor' : 'none');
            });
        };
        document.addEventListener('click', (e) => {
            const btn = e.target.closest('[data-favorite-toggle]');
            if (!btn) return;
            if (!IS_AUTH) {
                e.preventDefault();
                showAuthModal();
                return;
            }
            const slug = btn.getAttribute('data-favorite-toggle');
            let favs = getFavs();
            favs = favs.includes(slug) ? favs.filter((s) => s !== slug) : [...favs, slug];
            setFavs(favs);
            paintFavButtons();
        });
        paintFavButtons();

        function showAuthModal() {
            let modal = document.getElementById('auth-required-modal');
            if (!modal) {
                modal = document.createElement('div');
                modal.id = 'auth-required-modal';
                modal.className = 'fixed inset-0 z-[100] flex items-center justify-center p-4';
                modal.innerHTML = `
                    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="document.getElementById('auth-required-modal').remove()"></div>
                    <div class="relative w-full max-w-sm rounded-[28px] bg-white p-8 shadow-2xl animate-scale-in">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-forest-100 text-forest-700">
                            <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <h3 class="mt-5 text-center font-display text-xl font-extrabold text-gray-900">Login Required</h3>
                        <p class="mt-2 text-center text-sm leading-6 text-gray-500">You need to sign in or create an account to save favorites and use the trip planner.</p>
                        <div class="mt-6 flex flex-col gap-3">
                            <a href="${LOGIN_URL}" class="eco-btn eco-btn-primary w-full justify-center">Sign In</a>
                            <a href="{{ route('register') }}" class="eco-btn eco-btn-light w-full justify-center">Create Account</a>
                        </div>
                        <button onclick="document.getElementById('auth-required-modal').remove()" class="mt-4 w-full text-center text-xs font-semibold text-gray-400 hover:text-gray-600">Maybe later</button>
                    </div>
                `;
                document.body.appendChild(modal);
            }
        }
        window.showAuthModal = showAuthModal;
    </script>

</body>
</html>