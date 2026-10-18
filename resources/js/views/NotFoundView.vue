<template>
    <section class="container-eco py-20 sm:py-28">
        <div class="mx-auto max-w-xl text-center">
            <div class="mx-auto flex h-24 w-24 items-center justify-center rounded-[28px] bg-gradient-to-br from-forest-600 to-forest-800 shadow-xl shadow-forest-900/25">
                <span class="font-display text-3xl font-extrabold text-white">404</span>
            </div>
            <h1 class="mt-6 font-display text-4xl font-extrabold text-forest-900 dark:text-forest-100 sm:text-5xl">{{ $t('Trail not found.') }}</h1>
            <p class="mt-4 text-gray-500 dark:text-gray-400">{{ $t('This page may have moved. Explore destinations or return home.') }}</p>
            <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
                <RouterLink to="/" class="eco-btn eco-btn-primary">{{ $t('Back home') }}</RouterLink>
                <RouterLink to="/explore" class="eco-btn eco-btn-light">{{ $t('Explore destinations') }}</RouterLink>
            </div>
        </div>
    </section>
</template>
