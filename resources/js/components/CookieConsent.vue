<template>
    <Transition name="cookie">
        <div
            v-if="consent.visible"
            role="region"
            :aria-label="$t('Cookie notice')"
            class="fixed inset-x-0 bottom-0 z-50 px-4 pb-4 sm:px-6 sm:pb-6"
        >
            <div class="mx-auto flex max-w-4xl flex-col gap-5 rounded-[28px] border border-gray-200 bg-white p-6 shadow-xl shadow-forest-950/15 sm:flex-row sm:items-center sm:gap-6 dark:border-white/10 dark:bg-[#16211b] dark:shadow-black/50">
                <div class="min-w-0 flex-1">
                    <div class="text-[11px] font-bold uppercase tracking-[0.14em] text-forest-600 dark:text-forest-300">{{ $t('Cookie notice') }}</div>
                    <h2 class="mt-1.5 font-display text-lg font-extrabold tracking-[-0.02em] text-gray-900 sm:text-xl dark:text-gray-100">{{ $t('We use cookies') }}</h2>
                    <p class="mt-1.5 text-sm leading-6 text-gray-500 dark:text-gray-400">{{ $t('Essential cookies keep you signed in and remember your preferences. With your permission we also use cookies to understand how EcoJourney is used. You can change this anytime via Cookie settings in the footer.') }}</p>
                </div>

                <div class="flex shrink-0 flex-col gap-2 sm:flex-row sm:items-center">
                    <button type="button" class="eco-btn eco-btn-light text-sm" @click="reject">
                        {{ $t('Reject non-essential') }}
                    </button>
                    <button type="button" class="eco-btn eco-btn-primary text-sm" @click="accept">
                        {{ $t('Accept all') }}
                    </button>
                </div>
            </div>
        </div>
    </Transition>
</template>

<script setup>
import { consent, accept, reject } from '../state/consent';
</script>

<style scoped>
@media (prefers-reduced-motion: no-preference) {
    .cookie-enter-active,
    .cookie-leave-active {
        transition: opacity 0.35s ease, transform 0.35s ease;
    }

    .cookie-enter-from,
    .cookie-leave-to {
        opacity: 0;
        transform: translateY(14px);
    }
}
</style>
