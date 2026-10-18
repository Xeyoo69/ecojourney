<template>
    <header id="site-header" class="sticky top-0 z-50 border-b border-forest-900/[0.07] bg-white/85 backdrop-blur-xl dark:border-white/[0.06] dark:bg-[#0f1713]/85">
        <nav class="container-eco flex h-[72px] items-center justify-between gap-5 sm:h-[76px]">
            <RouterLink to="/" class="group flex shrink-0 items-center gap-3" :aria-label="$t('EcoJourney home')">
                <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-forest-700 text-white shadow-lg shadow-forest-700/15 transition group-hover:scale-105">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21c4.5-2.2 7-5.6 7-10.5C19 6 15.8 3 12 3S5 6 5 10.5C5 15.4 7.5 18.8 12 21Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21V9" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 14c-2.2-1.4-3.7-3.1-4.5-5" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 12c1.8-1.2 3.3-2.7 4.2-4.5" />
                    </svg>
                </div>
                <span class="hidden sm:block">
                    <span class="block font-display text-lg font-extrabold tracking-tight text-forest-900 dark:text-forest-100">EcoJourney</span>
                </span>
            </RouterLink>

            <div class="hidden items-center gap-6 lg:flex">
                <RouterLink v-for="item in navItems" :key="item.to" :to="item.to" class="nav-link" active-class="active">{{ $t(item.label) }}</RouterLink>
            </div>

            <div class="hidden items-center gap-3 md:flex">
                <button
                    type="button"
                    class="flex h-10 w-10 cursor-pointer items-center justify-center rounded-full border border-gray-200 bg-white text-gray-500 shadow-sm transition hover:border-forest-200 hover:bg-forest-50 hover:text-forest-700 dark:border-white/10 dark:bg-white/5 dark:text-gray-300 dark:hover:border-forest-400/40 dark:hover:bg-white/10 dark:hover:text-forest-300"
                    :aria-label="theme.mode === 'dark' ? $t('Switch to light mode') : $t('Switch to dark mode')"
                    :title="theme.mode === 'dark' ? $t('Switch to light mode') : $t('Switch to dark mode')"
                    @click="toggleTheme"
                >
                    <svg v-if="theme.mode === 'dark'" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <circle cx="12" cy="12" r="4" />
                        <path d="M12 2v2m0 16v2M4.93 4.93l1.41 1.41m11.32 11.32 1.41 1.41M2 12h2m16 0h2M4.93 19.07l1.41-1.41m11.32-11.32 1.41-1.41" />
                    </svg>
                    <svg v-else class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79Z" />
                    </svg>
                </button>
                <div class="flex items-center gap-1 rounded-full border border-gray-200 bg-gray-50 p-1 text-xs font-bold shadow-sm dark:border-white/10 dark:bg-white/5">
                    <button v-for="code in ['id', 'en']" :key="code" type="button" class="cursor-pointer rounded-full px-2.5 py-1 uppercase transition" :class="locale === code ? 'bg-forest-700 text-white shadow-sm' : 'text-gray-600 hover:text-forest-700 dark:text-gray-400 dark:hover:text-forest-300'" :aria-label="$t('Switch language')" @click="setLocale(code)">{{ code }}</button>
                </div>
                <RouterLink to="/favorites" class="hidden h-10 w-10 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-500 shadow-sm transition hover:border-forest-200 hover:bg-forest-50 hover:text-forest-700 dark:border-white/10 dark:bg-white/5 dark:text-gray-300 dark:hover:border-forest-400/40 dark:hover:bg-white/10 dark:hover:text-forest-300 xl:flex" :aria-label="$t('Favorites')">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.8 8.7c0 5.5-8.8 10.3-8.8 10.3S3.2 14.2 3.2 8.7A4.7 4.7 0 0 1 12 6.2a4.7 4.7 0 0 1 8.8 2.5Z" />
                    </svg>
                </RouterLink>
                <template v-if="auth.state.user">
                    <a v-if="isAdmin" href="/admin/destinations" class="hidden xl:flex items-center gap-1.5 rounded-full border border-amber-500/20 bg-amber-500/10 px-3.5 py-2 text-xs font-bold text-amber-700 transition hover:bg-amber-500/20 dark:text-amber-300">{{ $t('Admin') }}</a>
                    <div class="flex items-center gap-2 border-l border-gray-200 pl-3 dark:border-white/10">
                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-forest-100 text-xs font-extrabold text-forest-800 dark:bg-forest-800 dark:text-forest-200">{{ initials }}</div>
                        <button type="button" class="cursor-pointer text-xs font-bold text-gray-600 transition hover:text-forest-700 dark:text-gray-300 dark:hover:text-forest-300" @click="signOut">{{ $t('Sign out') }}</button>
                    </div>
                </template>
                <template v-else>
                    <RouterLink to="/login" class="px-2 text-xs font-bold text-forest-700 hover:text-forest-900 dark:text-forest-300 dark:hover:text-forest-200">{{ $t('Sign in') }}</RouterLink>
                    <RouterLink to="/register" class="eco-btn eco-btn-primary py-2.5 text-xs">{{ $t('Register') }}</RouterLink>
                </template>
            </div>

            <button type="button" class="flex h-11 w-11 cursor-pointer items-center justify-center rounded-full border border-gray-200 bg-white shadow-sm transition hover:border-forest-200 hover:bg-forest-50 hover:text-forest-700 dark:border-white/10 dark:bg-white/5 dark:text-gray-300 dark:hover:border-forest-400/40 dark:hover:bg-white/10 md:hidden" :aria-expanded="menuOpen" :aria-label="$t('Open menu')" aria-controls="mobile-menu" @click="menuOpen = !menuOpen">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path v-if="!menuOpen" stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h16M4 17h16" /><path v-else stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6L6 18" /></svg>
            </button>
        </nav>

        <div v-if="auth.state.error" class="border-t border-rose-200 bg-rose-50 px-4 py-2 text-center text-xs font-bold text-rose-600 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-300" role="alert">
            {{ $t(auth.state.error) }}
            <button type="button" class="ml-2 underline" @click="auth.state.error = null">{{ $t('Dismiss') }}</button>
        </div>

        <Transition name="menu">
            <div v-show="menuOpen" id="mobile-menu" class="border-t border-forest-900/[0.06] bg-white/95 backdrop-blur-xl dark:border-white/[0.06] dark:bg-[#121d16]/95 md:hidden">
            <div class="container-eco flex flex-col gap-1 py-4">
                <RouterLink v-for="item in [...navItems, { to: '/favorites', label: 'Favorites' }]" :key="item.to" :to="item.to" class="rounded-xl px-4 py-3 text-sm font-semibold text-gray-700 transition hover:bg-forest-50 hover:text-forest-700 dark:text-gray-200 dark:hover:bg-white/5 dark:hover:text-forest-300" @click="menuOpen = false">{{ $t(item.label) }}</RouterLink>
                <div class="mt-2 flex items-center justify-between rounded-xl bg-gray-50 p-3 dark:bg-white/5">
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ $t('Language') }}</span>
                    <div class="flex gap-1"><button v-for="code in ['id', 'en']" :key="code" type="button" class="rounded-full px-3 py-1 text-xs font-bold uppercase" :class="locale === code ? 'bg-forest-700 text-white' : 'text-gray-600 dark:text-gray-400'" @click="setLocale(code)">{{ code }}</button></div>
                </div>
                <div class="mt-1 flex items-center justify-between rounded-xl bg-gray-50 p-3 dark:bg-white/5">
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ $t('Theme') }}</span>
                    <button
                        type="button"
                        class="flex cursor-pointer items-center gap-2 rounded-full px-3 py-1.5 text-xs font-bold text-gray-700 transition hover:text-forest-700 dark:text-gray-200 dark:hover:text-forest-300"
                        :aria-label="theme.mode === 'dark' ? $t('Switch to light mode') : $t('Switch to dark mode')"
                        @click="toggleTheme"
                    >
                        <svg v-if="theme.mode === 'dark'" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="12" cy="12" r="4" />
                            <path d="M12 2v2m0 16v2M4.93 4.93l1.41 1.41m11.32 11.32 1.41 1.41M2 12h2m16 0h2M4.93 19.07l1.41-1.41m11.32-11.32 1.41-1.41" />
                        </svg>
                        <svg v-else class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79Z" />
                        </svg>
                        {{ theme.mode === 'dark' ? $t('Light') : $t('Dark') }}
                    </button>
                </div>
                <template v-if="auth.state.user">
                    <a v-if="isAdmin" href="/admin/destinations" class="eco-btn eco-btn-light mt-2">{{ $t('Admin destinations') }}</a>
                    <button type="button" class="eco-btn eco-btn-light mt-2" @click="signOut">{{ $t('Sign out') }}</button>
                </template>
                <template v-else>
                    <RouterLink to="/login" class="eco-btn eco-btn-light mt-2" @click="menuOpen = false">{{ $t('Sign in') }}</RouterLink>
                    <RouterLink to="/register" class="eco-btn eco-btn-primary" @click="menuOpen = false">{{ $t('Create account') }}</RouterLink>
                </template>
            </div>
            </div>
        </Transition>
    </header>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { auth } from '../state/auth';
import { setLocale, state as i18n } from '../i18n';
import { theme, toggle as toggleTheme } from '../state/theme';

const route = useRoute();
const router = useRouter();
const menuOpen = ref(false);
const locale = computed(() => i18n.locale);
const navItems = [
    { to: '/', label: 'Home' },
    { to: '/explore', label: 'Explore' },
    { to: '/national', label: 'National Map' },
    { to: '/planner', label: 'Planner' },
    { to: '/sustainability', label: 'Sustainability' },
    { to: '/about', label: 'About' },
];

const initials = computed(() => (auth.state.user?.name || auth.state.user?.email || 'U').split(/\s+/).map((part) => part[0]).join('').slice(0, 2).toUpperCase());
const isAdmin = computed(() => auth.state.user?.role === 'admin');

watch(() => route.fullPath, () => {
    menuOpen.value = false;
});

async function signOut() {
    const signedOut = await auth.logout();
    menuOpen.value = false;

    if (signedOut) {
        await router.push('/');
    }
}
</script>
