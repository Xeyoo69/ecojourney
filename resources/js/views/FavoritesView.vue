<template>
    <div>
        <section class="bg-forest-950 text-white"><div class="container-eco py-20 md:py-28"><div class="max-w-3xl"><div class="section-kicker !text-emerald-300">{{ $t('Your collection') }}</div><h1 class="mt-4 font-display text-4xl font-semibold tracking-tight md:text-6xl">{{ $t('Your favorite') }} <span class="text-emerald-300">{{ $t('destinations') }}</span></h1><p class="mt-6 max-w-2xl text-lg leading-8 text-white/70">{{ $t('Keep places you want to explore closer. Your saved destinations follow you on every device you sign in from.') }}</p></div></div></section>
        <section class="container-eco py-16 md:py-20"><AppLoader v-if="loading" label="Loading favorites…" /><AppError v-else-if="error" :message="error" :retry="loadFavorites" /><div v-else-if="destinations.length"><div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between"><div><p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ destinations.length }} {{ destinations.length === 1 ? $t('saved destination') : $t('saved destinations') }}</p><h2 class="mt-1 font-display text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ $t('Your collection') }}</h2></div><button type="button" class="cursor-pointer rounded-full border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 transition hover:border-rose-300 hover:text-rose-600 dark:border-white/20 dark:bg-transparent dark:text-gray-200 dark:hover:border-rose-400/50 dark:hover:text-rose-300" @click="clearAll">{{ $t('Clear all') }}</button></div><div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3"><DestinationCard v-for="destination in destinations" :key="destination.slug || destination.id" :destination="destination" /></div></div><div v-else class="card-soft mx-auto max-w-xl p-10 text-center"><div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-emerald-50 text-forest-700 dark:bg-emerald-500/10 dark:text-forest-300"><svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78L12 21.23l8.84-8.84a5.5 5.5 0 0 0 0-7.78Z" /></svg></div><h2 class="mt-6 font-display text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $t('No favorite destinations yet') }}</h2><p class="mx-auto mt-3 max-w-md text-gray-600 dark:text-gray-400">{{ $t('Explore destinations and save places you would love to visit.') }}</p><RouterLink to="/explore" class="eco-btn eco-btn-primary mt-8">{{ $t('Explore destinations') }}</RouterLink></div></section>
    </div>
</template>

<script setup>
import { onMounted, ref, watch } from 'vue';
import api from '../api';
import AppError from '../components/AppError.vue';
import AppLoader from '../components/AppLoader.vue';
import DestinationCard from '../components/DestinationCard.vue';
import { useFavorites } from '../utils/favorites';
import { asArray } from '../utils/data';
import { t } from '../i18n';

const loading = ref(true);
const error = ref('');
const destinations = ref([]);
const { favorites, clear, error: favoritesError } = useFavorites();

async function loadFavorites() {
    loading.value = true;
    error.value = '';
    try {
        const payload = await api.favorites();
        destinations.value = asArray(payload?.data ? payload : { data: payload?.data });
    } catch (loadError) {
        error.value = loadError.message || t('Unable to load favorites.');
    } finally {
        loading.value = false;
    }
}

async function clearAll() {
    if (!window.confirm(t('Remove all favorite destinations?'))) return;
    const cleared = await clear();
    if (cleared) {
        destinations.value = [];
    } else {
        error.value = favoritesError.value || t('Unable to clear favorites.');
    }
}

watch(favorites, (next) => {
    destinations.value = destinations.value.filter((destination) => next.includes(destination.slug));
});

onMounted(loadFavorites);
</script>
