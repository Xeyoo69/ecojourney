<template>
    <div>
        <section class="hero-gradient relative overflow-hidden">
            <div class="pointer-events-none absolute -right-32 -top-32 h-96 w-96 rounded-full bg-forest-200/30 blur-3xl animate-float-slow dark:bg-forest-700/25"></div>
            <div class="pointer-events-none absolute -bottom-40 -left-20 h-80 w-80 rounded-full bg-sand-200/40 blur-3xl animate-float-slow-delay dark:bg-sand-300/10"></div>
            <div class="container-eco relative grid min-h-[640px] items-center gap-12 py-16 lg:grid-cols-2 lg:py-20">
                <div class="animate-reveal">
                    <div class="mb-6 inline-flex items-center gap-3 rounded-full border border-forest-700/10 bg-gradient-to-r from-forest-600 via-forest-700 to-forest-600 px-5 py-2.5 text-xs font-bold uppercase tracking-wider text-white shadow-lg shadow-forest-700/25">
                        <span class="relative flex h-2 w-2"><span class="absolute inset-0 animate-ping rounded-full bg-emerald-300/60"></span><span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-400"></span></span>
                        {{ $t('Smart Sustainable Tourism') }}
                    </div>
                    <h1 class="max-w-2xl font-display text-4xl font-extrabold leading-[1.08] tracking-[-0.04em] text-forest-900 sm:text-5xl lg:text-7xl dark:text-forest-100">
                        {{ $t('Travel better.') }}<br><span class="bg-gradient-to-r from-forest-600 via-forest-500 to-emerald-500 bg-clip-text text-transparent dark:from-forest-400 dark:via-forest-300 dark:to-emerald-400">{{ $t('Leave lighter footprints.') }}</span>
                    </h1>
                    <p class="mt-6 max-w-xl text-base leading-7 text-gray-600 sm:text-lg sm:leading-8 dark:text-gray-300">{{ $t('Discover meaningful destinations, plan smarter journeys, and make choices that are better for places you visit.') }}</p>
                    <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                        <RouterLink to="/explore" class="eco-btn eco-btn-primary">{{ $t('Explore Destinations') }}</RouterLink>
                        <RouterLink to="/planner" class="eco-btn eco-btn-light">{{ $t('Plan My Journey') }}</RouterLink>
                    </div>
                    <div class="mt-10 flex w-full flex-wrap items-center gap-x-6 gap-y-5 rounded-3xl border border-forest-900/[0.07] bg-white/55 px-6 py-5 shadow-sm backdrop-blur-xl sm:inline-flex sm:w-auto dark:border-white/[0.07] dark:bg-white/[0.06]">
                                            <div><div class="font-display text-2xl font-extrabold text-forest-900 dark:text-forest-100">{{ shownStat(animatedDestinations, stats.destinations) }}</div><div class="mt-1 text-xs font-medium text-gray-500 dark:text-gray-400">{{ $t('Curated destinations') }}</div></div>
                                            <div class="hidden h-10 w-px bg-forest-900/10 sm:block dark:bg-white/10"></div>
                                            <div><div class="font-display text-2xl font-extrabold text-forest-900 dark:text-forest-100">{{ shownStat(animatedCategories, stats.categories) }}</div><div class="mt-1 text-xs font-medium text-gray-500 dark:text-gray-400">{{ $t('Travel categories') }}</div></div>
                                            <div class="hidden h-10 w-px bg-forest-900/10 sm:block dark:bg-white/10"></div>
                                            <div><div class="font-display text-2xl font-extrabold text-forest-900 dark:text-forest-100">{{ shownStat(animatedRegions, stats.regions) }}</div><div class="mt-1 text-xs font-medium text-gray-500 dark:text-gray-400">{{ $t('Regions across Indonesia') }}</div></div>
                    </div>
                </div>
                <div class="relative hidden lg:block">
                    <div class="relative mx-auto h-[560px] max-w-[500px]">
                        <div class="absolute right-0 top-5 h-[470px] w-[370px] overflow-hidden rounded-[40px] bg-forest-100 shadow-2xl shadow-forest-900/20 ring-1 ring-forest-900/5 dark:bg-forest-900 dark:ring-white/10">
                            <img v-if="heroImage" :src="heroImage" :alt="$t('Nature destination')" class="ken-burns h-full w-full object-cover" loading="lazy" decoding="async">
                            <div class="absolute inset-0 bg-gradient-to-t from-forest-900/40 to-transparent"></div>
                        </div>
                                            <div class="glass-card absolute left-0 top-24 rounded-[28px] p-5 animate-float-slow-delay">
                            <div class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ $t('Eco Score') }}</div>
                                                <div class="mt-2 font-display text-4xl font-extrabold text-forest-700 dark:text-forest-300">{{ ecoScore }}</div>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $t('Community + conservation') }}</p>
                        </div>
                                            <div class="absolute bottom-8 left-10 max-w-[270px] rounded-[28px] bg-forest-900 p-5 text-white shadow-xl shadow-forest-950/30 ring-1 ring-white/10 animate-float-slow">
                            <div class="text-xs font-bold uppercase tracking-wider text-forest-200">{{ $t('Featured route') }}</div>
                            <div class="mt-2 font-display text-lg font-bold">{{ $t('Rainforest to reef journey') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="container-eco pt-10 sm:pt-14">
            <PromoBanner
                :kicker="$t('New feature')"
                :title="$t('National Sustainability Map')"
                :description="$t('Explore sustainable destinations across Indonesia on one interactive map.')"
                :cta-label="$t('Open map')"
                to="/national"
                icon="map"
                storage-key="ecojourney:promo:home"
            />
        </section>

        <AppError v-if="error" :message="error" :retry="loadHome" />
        <section v-else class="py-18 sm:py-24">
            <div class="container-eco">
                <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <div class="section-kicker">{{ $t('Featured destinations') }}</div>
                        <h2 class="mt-3 font-display text-3xl font-extrabold text-gray-900 sm:text-4xl dark:text-gray-100">{{ $t('Start with places doing better.') }}</h2>
                    </div>
                    <RouterLink to="/explore" class="eco-btn eco-btn-light">{{ $t('View all') }}</RouterLink>
                </div>
                <AppLoader v-if="loading" />
                <div v-else-if="destinations.length" ref="gridRef" class="stagger-reveal mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3" :class="{ 'is-visible': gridVisible }">
                    <TransitionGroup name="list">
                        <DestinationCard v-for="destination in destinations" :key="destination.slug || destination.id" :destination="destination" />
                    </TransitionGroup>
                </div>
                <div v-else class="card-soft mt-10 p-8 text-center text-gray-500 dark:text-gray-400">{{ $t('No destinations found yet.') }}</div>
            </div>
        </section>
    </div>
</template>

<script setup>
import { computed, onMounted, reactive, ref } from 'vue';
import api from '../api';
import AppError from '../components/AppError.vue';
import AppLoader from '../components/AppLoader.vue';
import DestinationCard from '../components/DestinationCard.vue';
import PromoBanner from '../components/PromoBanner.vue';
import { useAnimatedNumber, useStaggeredReveal } from '../utils/animations';
import { extractCollection, uniqueBy } from '../utils/data';

const loading = ref(true);
const error = ref('');
const destinations = ref([]);
const stats = reactive({ destinations: '—', categories: '—', regions: '—' });
const heroOverride = ref('');
const heroImage = computed(() => heroOverride.value || destinations.value[0]?.image_url || destinations.value[0]?.image || '');

const { containerRef: gridRef, visible: gridVisible } = useStaggeredReveal({ step: 90 });

const statNumber = (value) => Number(value) || 0;
const ecoScore = ref(0);
const ecoTarget = 95;
const { display: animatedDestinations } = useAnimatedNumber(computed(() => statNumber(stats.destinations)));
const { display: animatedCategories } = useAnimatedNumber(computed(() => statNumber(stats.categories)));
const { display: animatedRegions } = useAnimatedNumber(computed(() => statNumber(stats.regions)));

function shownStat(animated, raw) {
    return raw === '—' ? '—' : animated;
}

function animateScore() {
    const start = performance.now();
    const duration = 1400;
    const tick = (now) => {
        const progress = Math.min((now - start) / duration, 1);
        ecoScore.value = Math.round(ecoTarget * (1 - Math.pow(1 - progress, 3)));
        if (progress < 1) requestAnimationFrame(tick);
    };
    requestAnimationFrame(tick);
}

async function loadHome() {
    loading.value = true;
    error.value = '';
    try {
        const payload = await api.home();
        const data = payload?.data || payload;
        const items = extractCollection(data, ['featured_destinations', 'destinations', 'featured']);
        destinations.value = items.slice(0, 6);
        heroOverride.value = data?.hero_image_url || '';
        stats.destinations = data?.destinationCount || data?.destination_count || data?.stats?.destinations || items.length || '—';
        stats.categories = data?.categoryCount || data?.category_count || data?.stats?.categories || uniqueBy(items.map((item) => item.category).filter(Boolean), 'slug').length || '—';
        stats.regions = data?.regionCount || data?.region_count || data?.stats?.regions || uniqueBy(items, 'region').length || '—';
    } catch (homeError) {
        try {
            const payload = await api.destinations({ sort: 'eco', per_page: 6 });
            const items = extractCollection(payload, ['destinations']);
            destinations.value = items.slice(0, 6);
            stats.destinations = payload?.total || payload?.meta?.total || items.length || '—';
            stats.categories = uniqueBy(items.map((item) => item.category).filter(Boolean), 'slug').length || '—';
            stats.regions = uniqueBy(items, 'region').length || '—';
        } catch (fallbackError) {
            error.value = fallbackError.message || homeError.message;
        }
    } finally {
        loading.value = false;
    }
}

onMounted(() => {
    loadHome();
    animateScore();
});
</script>

