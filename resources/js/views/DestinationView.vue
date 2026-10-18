<template>
    <div>
        <AppLoader v-if="loading" label="Loading destination…" />
        <AppError v-else-if="error" :message="error" :retry="loadDestination" />
        <template v-else-if="destination">
            <section class="bg-white pb-14 pt-8 sm:pb-18 dark:bg-transparent">
                <div class="container-eco">
                    <nav class="mb-7 flex flex-wrap items-center gap-2 text-sm text-gray-500 dark:text-gray-400" aria-label="Breadcrumb">
                        <RouterLink to="/" class="transition hover:text-forest-700 dark:hover:text-forest-300">{{ $t('Home') }}</RouterLink>
                        <span>/</span>
                        <RouterLink to="/explore" class="transition hover:text-forest-700 dark:hover:text-forest-300">{{ $t('Explore') }}</RouterLink>
                        <span>/</span>
                        <span class="font-medium text-gray-600 dark:text-gray-300">{{ destination.name }}</span>
                    </nav>
                    <div class="relative overflow-hidden rounded-[32px] bg-forest-900 shadow-xl shadow-forest-900/10">
                        <div class="relative h-[390px] sm:h-[520px]">
                            <img :src="imageUrl(destination)" :alt="destination.name" class="h-full w-full object-cover" @error="useFallback">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/15 to-black/5"></div>
                            <button type="button" class="absolute right-5 top-5 flex h-11 w-11 cursor-pointer items-center justify-center rounded-full border border-white/40 bg-white/90 text-gray-500 shadow-lg backdrop-blur transition hover:bg-white hover:text-rose-500" :class="isFavorite(destination.slug) ? 'text-rose-500' : ''" :aria-label="isFavorite(destination.slug) ? $t('Remove favorite') : $t('Save destination')" :title="auth.isAuthenticated() ? (isFavorite(destination.slug) ? $t('Remove from favorites') : $t('Save to favorites')) : $t('Sign in to save favorites')" @click="toggle(destination.slug)">
                                <svg class="h-5 w-5" :fill="isFavorite(destination.slug) ? 'currentColor' : 'none'" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78L12 21.23l8.84-8.84a5.5 5.5 0 0 0 0-7.78Z" /></svg>
                            </button>
                            <div class="absolute inset-x-0 bottom-0 p-6 sm:p-10">
                                <div class="flex flex-wrap items-center gap-3">
                                    <span class="rounded-full bg-white/90 px-4 py-2 text-xs font-bold uppercase tracking-wider text-forest-700 backdrop-blur">{{ categoryName(destination) }}</span>
                                    <span v-if="destination.region" class="rounded-full bg-white/15 px-4 py-2 text-xs font-bold uppercase tracking-wider text-white backdrop-blur">{{ destination.region }}</span>
                                    <span class="rounded-full bg-forest-600/90 px-4 py-2 text-xs font-bold text-white backdrop-blur">{{ $t('Eco Score {score}/100', { score }) }}</span>
                                    <span v-if="ratingCount" class="inline-flex items-center gap-1.5 rounded-full bg-white/15 px-4 py-2 text-xs font-bold text-white backdrop-blur"><svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 24 24" stroke="none" aria-hidden="true"><path d="M11.48 3.5a.56.56 0 0 1 1.04 0l2.13 5.1a.56.56 0 0 0 .48.34l5.52.44c.55.04.77.73.37 1.1l-4.2 3.6a.56.56 0 0 0-.18.56l1.28 5.37a.56.56 0 0 1-.84.61l-4.78-2.9a.56.56 0 0 0-.58 0l-4.78 2.9a.56.56 0 0 1-.84-.61l1.28-5.37a.56.56 0 0 0-.18-.56l-4.2-3.6a.56.56 0 0 1 .37-1.1l5.52-.44a.56.56 0 0 0 .48-.34l2.13-5.1Z" /></svg>{{ destination.rating.average }} · {{ $t('{count} reviews', { count: ratingCount }) }}</span>
                                </div>
                                <h1 class="mt-5 max-w-3xl font-display text-4xl font-extrabold tracking-tight text-white sm:text-6xl">{{ destination.name }}</h1>
                                <div class="mt-4 flex items-center gap-2 text-sm text-white/80"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21s7-5.3 7-12a7 7 0 1 0-14 0c0 6.7 7 12 7 12Z" /><circle cx="12" cy="9" r="2.2" /></svg>{{ destination.location }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="container-eco pb-20 sm:pb-24">
                <div class="grid gap-10 lg:grid-cols-[minmax(0,1fr)_360px]">
                    <div>
                        <div class="section-kicker">{{ $t('About destination') }}</div>
                        <h2 class="mt-3 font-display text-3xl font-extrabold text-gray-900 dark:text-gray-100">{{ $t('Discover {name}', { name: destination.name }) }}</h2>
                        <p class="mt-5 text-base leading-8 text-gray-600 dark:text-gray-300">{{ destination.description }}</p>

                        <div class="mt-14">
                            <div class="section-kicker">{{ $t('Things to do') }}</div>
                            <h2 class="mt-3 font-display text-2xl font-extrabold text-gray-900 dark:text-gray-100">{{ $t('Experiences you can explore') }}</h2>
                            <div v-if="activities.length" class="mt-6 grid gap-4 sm:grid-cols-2">
                                <article v-for="activity in activities" :key="activity.id || activity.name" class="hover-lift rounded-3xl border border-gray-100 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#16211b]">
                                    <h3 class="font-display font-bold text-gray-900 dark:text-gray-100">{{ activity.name }}</h3>
                                    <p v-if="activity.duration" class="mt-2 text-xs font-bold uppercase tracking-wider text-forest-600 dark:text-forest-300">{{ activity.duration }}</p>
                                    <p v-if="activity.description" class="mt-3 text-sm leading-6 text-gray-500 dark:text-gray-400">{{ activity.description }}</p>
                                </article>
                            </div>
                            <p v-else class="mt-5 rounded-2xl bg-forest-50 p-5 text-sm leading-6 text-gray-600 dark:bg-white/5 dark:text-gray-300">{{ $t('Plan time to explore local nature, culture, and community-led experiences.') }}</p>
                        </div>

                        <div v-if="related.length" class="mt-14">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between"><div><div class="section-kicker">{{ $t('Keep exploring') }}</div><h2 class="mt-3 font-display text-2xl font-extrabold text-gray-900 dark:text-gray-100">{{ $t('More thoughtful journeys') }}</h2></div><RouterLink to="/explore" class="text-sm font-bold text-forest-700 dark:text-forest-300">{{ $t('View all destinations') }}</RouterLink></div>
                            <div class="mt-6 grid gap-6 sm:grid-cols-2 xl:grid-cols-3"><DestinationCard v-for="item in related" :key="item.slug || item.id" :destination="item" /></div>
                        </div>

                        <ReviewSection :slug="destination.slug" />
                    </div>

                    <aside class="space-y-6 lg:sticky lg:top-24 lg:self-start">
                        <div class="card-soft p-6">
                            <div class="text-xs font-bold uppercase tracking-[0.18em] text-forest-600 dark:text-forest-300">{{ $t('Travel responsibly') }}</div>
                            <p class="mt-3 text-sm leading-6 text-gray-600 dark:text-gray-300">{{ $t('Choose local guides, refill water bottles, respect nature, and follow community rules during your visit.') }}</p>
                            <RouterLink to="/planner" class="eco-btn eco-btn-primary mt-6 w-full">{{ $t('Plan this journey') }}</RouterLink>
                        </div>
                        <div v-if="indicatorEntries.length" class="card-soft p-6">
                            <div class="text-xs font-bold uppercase tracking-[0.18em] text-forest-600 dark:text-forest-300">{{ $t('Sustainability indicators') }}</div>
                            <div class="mt-5 space-y-4">
                                <div v-for="[label, value] in indicatorEntries" :key="label"><div class="flex justify-between gap-3 text-xs font-bold text-gray-600 dark:text-gray-300"><span>{{ $t(label) }}</span><span class="text-forest-700 dark:text-forest-300">{{ value }}/100</span></div><div class="mt-2 h-2 overflow-hidden rounded-full bg-gray-100 dark:bg-white/10"><div class="h-full rounded-full bg-forest-600" :style="{ width: `${Math.max(0, Math.min(100, Number(value) || 0))}%` }"></div></div></div>
                            </div>
                        </div>
                    </aside>
                </div>
            </section>
        </template>
    </div>
</template>

<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import api from '../api';
import AppError from '../components/AppError.vue';
import AppLoader from '../components/AppLoader.vue';
import DestinationCard from '../components/DestinationCard.vue';
import ReviewSection from '../components/ReviewSection.vue';
import { categoryName, destinationScore, imageUrl } from '../utils/data';
import { useFavorites } from '../utils/favorites';
import { auth } from '../state/auth';
import { t } from '../i18n';

const props = defineProps({ slug: { type: String, required: true } });
const loading = ref(true);
const error = ref('');
const destination = ref(null);
const related = ref([]);
const { isFavorite, toggle } = useFavorites();
const score = computed(() => destinationScore(destination.value));
const activities = computed(() => Array.isArray(destination.value?.activities) ? destination.value.activities : []);
const ratingCount = computed(() => Number(destination.value?.rating?.count) || 0);
const indicatorEntries = computed(() => {
    const indicator = destination.value?.sustainability_indicator;
    if (!indicator || typeof indicator !== 'object') return [];
    return [['Eco transport', indicator.eco_transport], ['Waste management', indicator.waste_management], ['Local community', indicator.local_community], ['Conservation', indicator.conservation]].filter(([, value]) => value !== null && value !== undefined);
});
const fallbackImage = 'https://images.unsplash.com/photo-1469474968028-56623f02e42e?auto=format&fit=crop&w=1200&q=80';

function useFallback(event) {
    if (event.target.src !== fallbackImage) event.target.src = fallbackImage;
}

function applyPageMeta(item) {
    document.title = `${item.name} — ${item.location} | EcoJourney`;
    const description = document.querySelector('meta[name="description"]');
    if (description && item.description) {
        description.setAttribute('content', item.description.slice(0, 155));
    }
}

async function loadDestination() {
    loading.value = true;
    error.value = '';
    try {
        const payload = await api.destination(props.slug);
        const data = payload?.data || payload;
        destination.value = data?.destination || data;
        related.value = data?.related_destinations || data?.related || [];
        if (!destination.value?.slug) throw new Error(t('Destination was not found.'));
        applyPageMeta(destination.value);
    } catch (loadError) {
        destination.value = null;
        related.value = [];
        error.value = loadError.message || t('Unable to load this destination.');
    } finally {
        loading.value = false;
    }
}

watch(() => props.slug, loadDestination);
onMounted(loadDestination);
</script>
