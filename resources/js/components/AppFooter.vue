<template>
    <footer class="mt-24 border-t border-gray-200 bg-white dark:border-white/10 dark:bg-[#121d16]">
        <div class="container-eco py-14">
            <div class="grid gap-10 md:grid-cols-12">
                <div class="md:col-span-5">
                    <RouterLink to="/" class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-forest-700 text-white shadow-md shadow-forest-900/20">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21c4.5-2.2 7-5.6 7-10.5C19 6 15.8 3 12 3S5 6 5 10.5C5 15.4 7.5 18.8 12 21Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21V9" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 14c-2.2-1.4-3.7-3.1-4.5-5" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 12c1.8-1.2 3.3-2.7 4.2-4.5" />
                            </svg>
                        </div>
                        <span class="font-display text-xl font-extrabold tracking-tight text-forest-900 dark:text-forest-100">EcoJourney</span>
                    </RouterLink>
                    <p class="mt-5 max-w-md text-sm leading-7 text-gray-500 dark:text-gray-400">{{ $t('Discover meaningful destinations, plan smarter journeys, and make travel choices that support places and people.') }}</p>
                </div>
                <div class="grid gap-8 sm:grid-cols-2 md:col-span-7 md:grid-cols-3">
                    <div><p class="text-xs font-extrabold uppercase tracking-[.18em] text-gray-500 dark:text-gray-400">{{ $t('Explore') }}</p><div class="mt-4 flex flex-col gap-3"><RouterLink to="/explore" class="footer-link text-sm font-semibold text-gray-600 dark:text-gray-300">{{ $t('Destinations') }}</RouterLink><RouterLink to="/favorites" class="footer-link text-sm font-semibold text-gray-600 dark:text-gray-300">{{ $t('Favorites') }}</RouterLink><RouterLink to="/planner" class="footer-link text-sm font-semibold text-gray-600 dark:text-gray-300">{{ $t('Trip planner') }}</RouterLink></div></div>
                    <div><p class="text-xs font-extrabold uppercase tracking-[.18em] text-gray-500 dark:text-gray-400">{{ $t('Learn') }}</p><div class="mt-4 flex flex-col gap-3"><RouterLink to="/sustainability" class="footer-link text-sm font-semibold text-gray-600 dark:text-gray-300">{{ $t('Sustainability') }}</RouterLink><RouterLink to="/about" class="footer-link text-sm font-semibold text-gray-600 dark:text-gray-300">{{ $t('About us') }}</RouterLink><RouterLink to="/contact" class="footer-link text-sm font-semibold text-gray-600 dark:text-gray-300">{{ $t('Contact') }}</RouterLink><button type="button" class="footer-link text-left text-sm font-semibold text-gray-600 dark:text-gray-300" @click="openCookieSettings">{{ $t('Cookie settings') }}</button></div></div>
                    <div><p class="text-xs font-extrabold uppercase tracking-[.18em] text-gray-500 dark:text-gray-400">{{ $t('Join') }}</p><div class="mt-4 flex flex-col gap-3"><RouterLink to="/register" class="footer-link text-sm font-semibold text-gray-600 dark:text-gray-300">{{ $t('Create account') }}</RouterLink><RouterLink to="/login" class="footer-link text-sm font-semibold text-gray-600 dark:text-gray-300">{{ $t('Sign in') }}</RouterLink></div></div>
                </div>
            </div>
            <div class="mt-12 flex flex-col gap-3 border-t border-forest-900/[0.07] pt-6 text-xs font-medium text-gray-500 dark:border-white/[0.06] dark:text-gray-400 sm:flex-row sm:items-center sm:justify-between"><span>{{ $t('© {year} EcoJourney. Travel with care.', { year }) }}</span><span>{{ $t('Made for lighter footprints.') }}</span></div>
        </div>
    </footer>
</template>

<script setup>
import { reset as openCookieSettings } from '../state/consent';

const year = new Date().getFullYear();
</script>
