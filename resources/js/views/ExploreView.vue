<template>
    <div>
        <section class="relative overflow-hidden bg-forest-900 py-16 text-white sm:py-20">
            <div class="pointer-events-none absolute -right-24 -top-24 h-72 w-72 rounded-full bg-white/10 blur-3xl"></div>
            <div class="pointer-events-none absolute -bottom-24 -left-24 h-72 w-72 rounded-full bg-forest-700/40 blur-3xl"></div>
            <div class="container-eco relative max-w-3xl">
                <div class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3 py-1 text-[11px] font-bold uppercase tracking-[0.18em] text-forest-100 backdrop-blur"><span class="h-1.5 w-1.5 rounded-full bg-emerald-300"></span> {{ $t('Explore EcoJourney') }}</div>
                <h1 class="mt-4 font-display text-4xl font-extrabold tracking-tight sm:text-5xl">{{ $t('Find your next destination.') }}</h1>
                <p class="mt-5 max-w-2xl text-base leading-7 text-white/60">{{ $t('Search places, compare sustainability scores, and discover travel shaped by local nature and culture.') }}</p>
            </div>
        </section>

        <section class="relative z-10 -mt-8">
            <div class="container-eco">
                <form class="rounded-[28px] border border-gray-100 bg-white p-5 shadow-xl shadow-forest-900/5 dark:border-white/10 dark:bg-[#16211b] dark:shadow-black/40" @submit.prevent="applyFilters">
                    <div class="grid gap-4 lg:grid-cols-[1fr_auto]">
                        <label class="relative block">
                            <span class="sr-only">{{ $t('Search destinations') }}</span>
                            <svg class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.3-4.3m2.3-5.2a7.5 7.5 0 1 1-15 0 7.5 7.5 0 0 1 15 0Z" /></svg>
                            <input v-model.trim="search" type="search" :placeholder="$t('Search destination, location, or region...')" class="h-14 w-full rounded-2xl border border-gray-200 bg-gray-50 pl-12 pr-4 text-sm outline-none transition focus:border-forest-400 focus:bg-white focus:ring-4 focus:ring-forest-100 dark:border-white/10 dark:bg-white/5 dark:text-gray-100 dark:placeholder:text-gray-500 dark:focus:bg-[#16211b] dark:focus:ring-forest-500/25">
                        </label>
                        <button type="submit" class="eco-btn eco-btn-primary h-14 px-7">{{ $t('Search') }}</button>
                    </div>

                    <div class="mt-5 flex flex-wrap items-center gap-2">
                        <span class="mr-2 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ $t('Category') }}</span>
                        <button type="button" class="filter-pill" :class="!filters.category ? 'filter-pill-active' : ''" @click="setFilter('category', '')">{{ $t('All') }}</button>
                        <button v-for="category in categories" :key="category.slug || category.id || category.name" type="button" class="filter-pill" :class="filters.category === category.slug ? 'filter-pill-active' : ''" @click="setFilter('category', category.slug)">{{ category.name }}</button>
                    </div>

                    <div class="mt-4 flex flex-wrap items-center gap-2">
                        <span class="mr-2 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ $t('Region') }}</span>
                        <button type="button" class="filter-pill" :class="!filters.region ? 'filter-pill-active' : ''" @click="setFilter('region', '')">{{ $t('All') }}</button>
                        <button v-for="region in regions" :key="region" type="button" class="filter-pill" :class="filters.region === region ? 'filter-pill-active' : ''" @click="setFilter('region', region)">{{ region }}</button>
                    </div>

                    <div class="mt-5 flex flex-col gap-3 border-t border-gray-100 pt-5 sm:flex-row sm:items-center sm:justify-between dark:border-white/10">
                        <p class="text-sm text-gray-500 dark:text-gray-400"><strong class="text-gray-800 dark:text-gray-200">{{ $t('{count} destinations found', { count: pagination.total }) }}</strong></p>
                        <div class="flex items-center gap-3">
                            <label for="sort" class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ $t('Sort') }}</label>
                            <select id="sort" v-model="selectedSort" class="rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm font-semibold text-gray-700 outline-none focus:border-forest-400 dark:border-white/10 dark:bg-white/5 dark:text-gray-200" @change="setFilter('sort', selectedSort)">
                                <option value="eco">{{ $t('Highest eco score') }}</option>
                                <option value="name">{{ $t('Name A–Z') }}</option>
                                <option value="latest">{{ $t('Newest') }}</option>
                            </select>
                            <button v-if="hasFilters" type="button" class="text-sm font-bold text-forest-700 hover:text-forest-900 dark:text-forest-300 dark:hover:text-forest-200" @click="clearFilters">{{ $t('Clear') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </section>

        <section class="container-eco pt-10 sm:pt-14">
            <PromoBanner
                :kicker="$t('New feature')"
                :title="$t('Plan smarter with Eco Planner')"
                :description="$t('Build a sustainable itinerary day by day — with eco scores and local picks.')"
                :cta-label="$t('Try planner')"
                to="/planner"
                icon="sparkle"
                storage-key="ecojourney:promo:explore"
            />
        </section>

        <section class="container-eco py-12 sm:py-16">
            <AppLoader v-if="loading" label="Finding better destinations…" />
            <AppError v-else-if="error" :message="error" :retry="loadDestinations" />
            <div v-else-if="destinations.length" ref="gridRef" class="stagger-reveal grid gap-6 sm:grid-cols-2 lg:grid-cols-3" :class="{ 'is-visible': gridVisible }">
                <TransitionGroup name="list">
                    <DestinationCard v-for="destination in destinations" :key="destination.slug || destination.id" :destination="destination" />
                </TransitionGroup>
            </div>
            <div v-else class="card-soft mx-auto max-w-2xl p-10 text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-forest-50 text-forest-700 dark:bg-white/5 dark:text-forest-300">
                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.3-4.3m2.3-5.2a7.5 7.5 0 1 1-15 0 7.5 7.5 0 0 1 15 0Z" /></svg>
                </div>
                <h2 class="mt-5 font-display text-2xl font-extrabold text-gray-900 dark:text-gray-100">{{ $t('No matching destinations') }}</h2>
                <p class="mt-3 text-sm leading-6 text-gray-500 dark:text-gray-400">{{ $t('Try another search or clear filters to see every destination.') }}</p>
                <button type="button" class="eco-btn eco-btn-primary mt-7" @click="clearFilters">{{ $t('Clear filters') }}</button>
            </div>

            <nav v-if="!loading && !error && pagination.last_page > 1" class="mt-12 flex items-center justify-center gap-2" :aria-label="$t('Destination pages')">
                <button type="button" class="pagination-button" :disabled="pagination.current_page <= 1" @click="goToPage(pagination.current_page - 1)">{{ $t('Previous') }}</button>
                <button v-for="page in pages" :key="page" type="button" class="pagination-button" :class="page === pagination.current_page ? 'pagination-button-active' : ''" @click="goToPage(page)">{{ page }}</button>
                <button type="button" class="pagination-button" :disabled="pagination.current_page >= pagination.last_page" @click="goToPage(pagination.current_page + 1)">{{ $t('Next') }}</button>
            </nav>
        </section>
    </div>
</template>

<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api from '../api';
import AppError from '../components/AppError.vue';
import AppLoader from '../components/AppLoader.vue';
import DestinationCard from '../components/DestinationCard.vue';
import PromoBanner from '../components/PromoBanner.vue';
import { asArray, cleanQuery, extractPagination, uniqueBy } from '../utils/data';
import { useStaggeredReveal } from '../utils/animations';

const route = useRoute();
const router = useRouter();
const loading = ref(true);
const error = ref('');
const destinations = ref([]);
const categories = ref([]);
const regions = ref([]);
const pagination = ref({ items: [], current_page: 1, last_page: 1, per_page: 12, total: 0 });
const search = ref('');
const selectedSort = ref('eco');
let requestId = 0;
const { containerRef: gridRef, visible: gridVisible } = useStaggeredReveal({ step: 70 });

const readQuery = (key, fallback = '') => {
    const value = route.query[key];
    return Array.isArray(value) ? value[0] || fallback : value || fallback;
};

const filters = computed(() => ({
    search: readQuery('search'),
    category: readQuery('category'),
    region: readQuery('region'),
    sort: readQuery('sort', 'eco'),
    page: Number(readQuery('page', '1')) || 1,
}));

const hasFilters = computed(() => Boolean(filters.value.search || filters.value.category || filters.value.region || filters.value.sort !== 'eco'));
const pages = computed(() => {
    const start = Math.max(1, pagination.value.current_page - 2);
    const end = Math.min(pagination.value.last_page, start + 4);
    return Array.from({ length: end - start + 1 }, (_, index) => start + index);
});

function syncInputs() {
    search.value = filters.value.search;
    selectedSort.value = filters.value.sort;
}

function apiParams() {
    return cleanQuery({
        search: filters.value.search,
        category: filters.value.category,
        region: filters.value.region,
        sort: filters.value.sort,
        page: filters.value.page,
    });
}

async function loadCategories() {
    try {
        const payload = await api.categories();
        categories.value = uniqueBy(asArray(payload), (category) => category.slug || category.id || category.name);
        const suppliedRegions = Array.isArray(payload?.regions) ? payload.regions : [];
        if (suppliedRegions.length) {
            regions.value = suppliedRegions.map((region) => typeof region === 'string' ? region : region.name || region.region).filter(Boolean);
        }
    } catch (categoryError) {
        // Destination data supplies fallback filters.
    }
}

async function loadDestinations() {
    const currentRequest = ++requestId;
    loading.value = true;
    error.value = '';
    syncInputs();

    try {
        const payload = await api.destinations(apiParams());
        if (currentRequest !== requestId) return;
        const pageData = extractPagination(payload, 'destinations');
        destinations.value = pageData.items;
        pagination.value = pageData;

        if (!categories.value.length) {
            categories.value = uniqueBy(destinations.value.map((destination) => destination.category).filter(Boolean), (category) => category.slug || category.id || category.name);
        }
        if (!regions.value.length) {
            regions.value = [...new Set(destinations.value.map((destination) => destination.region).filter(Boolean))].sort();
        }
    } catch (loadError) {
        if (currentRequest === requestId) {
            error.value = loadError.message;
            destinations.value = [];
        }
    } finally {
        if (currentRequest === requestId) loading.value = false;
    }
}

function updateQuery(next) {
    router.push({ name: 'explore', query: cleanQuery({ ...route.query, ...next }) });
}

function applyFilters() {
    updateQuery({ search: search.value, page: undefined });
}

function setFilter(key, value) {
    updateQuery({ [key]: value || undefined, page: undefined });
}

function clearFilters() {
    router.push({ name: 'explore' });
}

function goToPage(page) {
    if (page < 1 || page > pagination.value.last_page || page === pagination.value.current_page) return;
    updateQuery({ page });
}

watch(() => route.fullPath, loadDestinations);
onMounted(() => {
    loadCategories();
    loadDestinations();
});
</script>
