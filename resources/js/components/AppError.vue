<template>
    <section class="container-eco py-16 sm:py-24">
        <div class="mx-auto max-w-xl rounded-[28px] border border-rose-100 bg-white p-8 text-center shadow-xl shadow-rose-900/5 dark:border-rose-500/20 dark:bg-[#16211b] dark:shadow-black/40">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-rose-50 text-rose-600 dark:bg-rose-500/15 dark:text-rose-300">
                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M5.1 19h13.8c1.5 0 2.4-1.6 1.6-2.9L13.6 4.2a1.85 1.85 0 0 0-3.2 0L3.5 16.1C2.7 17.4 3.6 19 5.1 19Z" />
                </svg>
            </div>
            <h2 class="mt-5 font-display text-2xl font-extrabold text-gray-900 dark:text-gray-100">{{ $t(title) }}</h2>
            <p class="mt-3 text-sm leading-6 text-gray-500 dark:text-gray-400">{{ $t(message) }}</p>
            <button v-if="retry" type="button" class="eco-btn eco-btn-primary mt-7" @click="retry">{{ $t('Try again') }}</button>
            <RouterLink v-else to="/" class="eco-btn eco-btn-primary mt-7">{{ $t('Back home') }}</RouterLink>
        </div>
    </section>
</template>

<script setup>
defineProps({
    title: {
        type: String,
        default: 'Unable to load this page',
    },
    message: {
        type: String,
        default: 'Please check your connection and try again.',
    },
    retry: {
        type: Function,
        default: null,
    },
});
</script>
