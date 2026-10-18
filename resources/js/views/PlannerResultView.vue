<template>
    <div>
        <AppLoader v-if="loading" label="Loading your journey…" />
        <AppError v-else-if="error" title="Unable to load journey" :message="error" :retry="loadItinerary" />
        <template v-else-if="itinerary">
            <section class="relative overflow-hidden bg-forest-900 py-16 text-white sm:py-20"><div class="pointer-events-none absolute -right-24 -top-24 h-72 w-72 rounded-full bg-white/10 blur-3xl"></div><div class="container-eco relative"><div class="max-w-3xl"><div class="section-kicker !text-emerald-300">{{ $t('Your EcoJourney') }}</div><h1 class="mt-4 font-display text-4xl font-extrabold tracking-tight sm:text-5xl">{{ $t('Your journey is ready.') }}</h1><p class="mt-5 max-w-2xl text-base leading-7 text-white/60">{{ $t('We created a {duration}-day {preference} journey based on your selected experience.', { duration: itinerary.duration, preference: formattedPreference }) }}</p></div></div></section>
            <section class="container-eco py-16 sm:py-20"><div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between"><div><div class="section-kicker">{{ $t('Personalized itinerary') }}</div><h2 class="mt-3 font-display text-3xl font-extrabold text-gray-900 dark:text-gray-100">{{ itinerary.name }}</h2></div><div class="rounded-full bg-forest-50 px-4 py-2 text-sm font-bold text-forest-700 dark:bg-white/5 dark:text-forest-300">{{ itinerary.duration }} {{ itinerary.duration === 1 ? $t('Day') : $t('Days') }}</div></div>
                <div class="mt-12 space-y-6"><article v-for="[day, items] in days" :key="day" class="overflow-hidden rounded-[28px] border border-gray-200 bg-white dark:border-white/10 dark:bg-[#16211b]"><header class="flex items-center gap-4 border-b border-gray-100 bg-gray-50 px-6 py-5 sm:px-8 dark:border-white/10 dark:bg-white/5"><div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-forest-700 font-display text-sm font-bold text-white">{{ String(day).padStart(2, '0') }}</div><div><div class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ $t('Day {day}', { day }) }}</div><div class="mt-1 font-display font-bold text-gray-900 dark:text-gray-100">{{ $t('Explore & experience') }}</div></div></header><div class="space-y-7 p-6 sm:p-8"><div v-for="item in items" :key="item.id" class="flex gap-5"><div class="w-14 shrink-0 text-sm font-bold text-forest-700 dark:text-forest-300">{{ formatTime(item.start_time) }}</div><div class="relative flex-1 border-l border-forest-100 pl-7 dark:border-white/10"><span class="absolute -left-1.5 top-1 h-3 w-3 rounded-full bg-forest-500 ring-4 ring-forest-50 dark:ring-white/10"></span><div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between"><div><RouterLink :to="`/destinations/${item.destination?.slug}`" class="font-display text-xl font-bold text-gray-900 transition hover:text-forest-700 dark:text-gray-100 dark:hover:text-forest-300">{{ item.destination?.name || $t('Destination') }}</RouterLink><div class="mt-2 flex flex-wrap gap-2"><span v-if="item.destination?.category?.name" class="rounded-full bg-forest-50 px-3 py-1 text-xs font-bold text-forest-700 dark:bg-white/5 dark:text-forest-300">{{ item.destination.category.name }}</span><span v-if="item.destination?.location" class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-500 dark:bg-white/10 dark:text-gray-400">{{ item.destination.location }}</span></div><p class="mt-4 text-sm leading-6 text-gray-500 dark:text-gray-400">{{ item.description }}</p></div><div v-if="item.destination" class="shrink-0 rounded-2xl bg-forest-50 px-4 py-3 text-center dark:bg-white/5"><div class="text-[10px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ $t('Eco Score') }}</div><div class="mt-1 font-display text-xl font-extrabold text-forest-700 dark:text-forest-300">{{ item.destination.sustainability_score }}</div></div></div></div></div></div></article></div>
                <div class="mt-10 flex flex-col gap-3 sm:flex-row"><RouterLink to="/planner" class="eco-btn eco-btn-primary">{{ $t('Create another journey') }}</RouterLink><RouterLink to="/explore" class="eco-btn eco-btn-light">{{ $t('Explore destinations') }}</RouterLink></div>
            </section>
        </template>
    </div>
</template>

<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import api from '../api';
import AppError from '../components/AppError.vue';
import AppLoader from '../components/AppLoader.vue';
import { t } from '../i18n';

const props = defineProps({ id: { type: [String, Number], required: true } });
const loading = ref(true);
const error = ref('');
const itinerary = ref(null);
const days = computed(() => {
    const grouped = new Map();
    for (const item of itinerary.value?.items || []) {
        const day = item.day || 1;
        grouped.set(day, [...(grouped.get(day) || []), item]);
    }
    return [...grouped.entries()].sort(([a], [b]) => Number(a) - Number(b));
});
const formattedPreference = computed(() => String(itinerary.value?.preference || 'sustainable').replace('-', ' '));
function formatTime(value) { return value ? String(value).slice(0, 5) : '09:00'; }

async function loadItinerary() {
    loading.value = true;
    error.value = '';
    try {
        const payload = await api.plannerResult(props.id);
        itinerary.value = payload?.data || payload;
        if (!itinerary.value?.id) throw new Error(t('Journey was not found.'));
    } catch (loadError) {
        itinerary.value = null;
        error.value = loadError.message || t('Unable to load journey.');
    } finally {
        loading.value = false;
    }
}
watch(() => props.id, loadItinerary);
onMounted(loadItinerary);
</script>
