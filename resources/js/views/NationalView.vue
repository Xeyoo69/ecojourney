<template>
    <div>
        <section class="relative overflow-hidden bg-forest-900 py-16 text-white sm:py-20">
            <div class="pointer-events-none absolute -right-24 -top-24 h-72 w-72 rounded-full bg-white/10 blur-3xl"></div>
            <div class="pointer-events-none absolute -bottom-24 -left-24 h-72 w-72 rounded-full bg-forest-700/40 blur-3xl"></div>
            <div class="container-eco relative">
                <div class="max-w-3xl">
                    <div class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3 py-1 text-[11px] font-bold uppercase tracking-[0.18em] text-forest-100 backdrop-blur">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-300"></span> {{ $t('Nationwide coverage') }}
                    </div>
                    <h1 class="mt-4 font-display text-4xl font-extrabold tracking-tight sm:text-5xl">{{ $t('EcoJourney across Indonesia.') }}</h1>
                    <p class="mt-5 max-w-2xl text-base leading-7 text-white/60">{{ $t('Every curated destination, mapped from Sumatera to Papua — with regional eco scores so you can see where travel is doing better.') }}</p>
                    <div class="mt-8 flex flex-wrap gap-8 border-t border-white/10 pt-6">
                        <div>
                            <div class="font-display text-2xl font-extrabold text-white">{{ loading ? '—' : stats.destinations }}</div>
                            <div class="mt-1 text-xs text-white/50">{{ $t('Destinations mapped') }}</div>
                        </div>
                        <div>
                            <div class="font-display text-2xl font-extrabold text-white">{{ loading ? '—' : stats.regions }}</div>
                            <div class="mt-1 text-xs text-white/50">{{ $t('Island regions') }}</div>
                        </div>
                        <div>
                            <div class="font-display text-2xl font-extrabold text-white">{{ loading ? '—' : `${stats.avg_score}/100` }}</div>
                            <div class="mt-1 text-xs text-white/50">{{ $t('Average eco score') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <AppError v-if="error" :message="error" :retry="loadNational" />
        <template v-else>
            <section class="relative z-10 -mt-8">
                <div class="container-eco grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div v-for="card in statCards" :key="card.label" class="card-soft flex items-center gap-4 p-5">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-forest-50 text-forest-700 dark:bg-white/5 dark:text-forest-300">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" :d="card.icon" /></svg>
                        </div>
                        <div class="min-w-0">
                            <div class="font-display text-2xl font-extrabold text-gray-900 dark:text-gray-100">{{ loading ? '—' : card.value }}</div>
                            <div class="truncate text-xs font-semibold text-gray-500 dark:text-gray-400">{{ $t(card.label) }}</div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="national-map-section" class="container-eco py-12 sm:py-16">
                <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <div class="section-kicker">{{ $t('National map') }}</div>
                        <h2 class="mt-3 font-display text-3xl font-extrabold text-gray-900 sm:text-4xl dark:text-gray-100">{{ $t('Where better travel happens.') }}</h2>
                    </div>
                    <p class="max-w-sm text-sm leading-6 text-gray-500 dark:text-gray-400">{{ $t('Pick a region to focus the map, or tap any marker to preview the destination.') }}</p>
                </div>

                <div class="mt-8 rounded-[28px] border border-gray-100 bg-white p-4 shadow-xl shadow-forest-900/5 sm:p-5 dark:border-white/10 dark:bg-[#16211b] dark:shadow-black/40">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="mr-2 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ $t('Region') }}</span>
                        <button type="button" class="filter-pill" :class="!activeRegion ? 'filter-pill-active' : ''" @click="selectRegion('')">{{ $t('All Indonesia') }}</button>
                        <button v-for="region in regions" :key="region.region" type="button" class="filter-pill" :class="activeRegion === region.region ? 'filter-pill-active' : ''" @click="selectRegion(region.region)">{{ region.region }}</button>
                    </div>

                    <div class="relative mt-4 overflow-hidden rounded-3xl">
                        <div ref="mapEl" class="national-map h-[420px] w-full bg-forest-50 sm:h-[540px] dark:bg-[#0f1713]"></div>
                        <div class="pointer-events-none absolute bottom-4 left-4 z-[10] rounded-2xl border border-gray-100 bg-white/95 p-3 shadow-lg backdrop-blur dark:border-white/10 dark:bg-[#16211b]/95">
                            <div class="text-[10px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ $t('Eco score') }}</div>
                            <ul class="mt-2 space-y-1.5 text-xs font-semibold text-gray-600 dark:text-gray-300">
                                <li class="flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span>{{ $t('85–100 Leading') }}</li>
                                <li class="flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-full bg-amber-500"></span>{{ $t('70–84 Promising') }}</li>
                                <li class="flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-full bg-rose-500"></span>{{ $t('Below 70 Growing') }}</li>
                            </ul>
                        </div>
                    </div>

                    <div class="mt-4 flex flex-wrap items-center justify-between gap-3 text-xs font-semibold text-gray-500 dark:text-gray-400">
                        <span>{{ $t('{current} of {total} destinations shown', { current: visiblePoints.length, total: points.length }) }}</span>
                        <span>{{ $t('Scroll to zoom · drag to pan') }}</span>
                    </div>
                </div>
            </section>

            <section class="bg-gray-50 py-12 sm:py-16 dark:bg-white/[0.03]">
                <div class="container-eco">
                    <div class="section-kicker">{{ $t('Regional statistics') }}</div>
                    <h2 class="mt-3 font-display text-3xl font-extrabold text-gray-900 sm:text-4xl dark:text-gray-100">{{ $t('How each region measures up.') }}</h2>

                    <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                        <button v-for="region in regions" :key="region.region" type="button" class="card-soft group p-6 text-left transition hover:-translate-y-1 hover:shadow-xl" :class="activeRegion === region.region ? 'ring-2 ring-forest-500' : ''" @click="selectRegion(region.region)">
                            <div class="flex items-start justify-between gap-3">
                                <h3 class="font-display text-xl font-extrabold text-gray-900 group-hover:text-forest-700 dark:text-gray-100 dark:group-hover:text-forest-300">{{ region.region }}</h3>
                                <span class="shrink-0 rounded-full bg-forest-50 px-3 py-1 text-[11px] font-extrabold text-forest-700 dark:bg-white/5 dark:text-forest-300">{{ $t('{count} spots', { count: region.destinations }) }}</span>
                            </div>
                            <div class="mt-5 flex items-end justify-between gap-4">
                                <div>
                                    <div class="font-display text-4xl font-extrabold text-forest-800 dark:text-forest-300">{{ region.avg_score }}</div>
                                    <div class="mt-1 text-xs font-semibold text-gray-500 dark:text-gray-400">{{ $t('Average eco score') }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-bold text-gray-800 dark:text-gray-200">{{ region.best_destination.name }}</div>
                                    <div class="mt-1 text-xs font-semibold text-gray-500 dark:text-gray-400">{{ $t('Top: {score}/100', { score: region.best_destination.sustainability_score }) }}</div>
                                </div>
                            </div>
                            <div class="mt-5 h-2 overflow-hidden rounded-full bg-gray-100 dark:bg-white/10">
                                <div class="h-full rounded-full bg-gradient-to-r from-forest-600 to-emerald-500 transition-all duration-700 dark:from-forest-400 dark:to-emerald-400" :style="{ width: `${region.avg_score}%` }"></div>
                            </div>
                            <div class="mt-4 inline-flex items-center gap-2 text-xs font-extrabold text-forest-700 transition group-hover:gap-3 dark:text-forest-300">
                                {{ activeRegion === region.region ? $t('Focused on map') : $t('Show on map') }}
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 6l6 6-6 6" /></svg>
                            </div>
                        </button>
                    </div>
                </div>
            </section>

            <section class="container-eco py-12 sm:py-16">
                <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <div class="section-kicker">{{ $t('Nationwide ranking') }}</div>
                        <h2 class="mt-3 font-display text-3xl font-extrabold text-gray-900 sm:text-4xl dark:text-gray-100">{{ $t('Top rated across the archipelago.') }}</h2>
                    </div>
                    <RouterLink to="/explore" class="eco-btn eco-btn-light">{{ $t('View all') }}</RouterLink>
                </div>

                <AppLoader v-if="loading" />
                <div v-else-if="topDestinations.length" class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <DestinationCard v-for="destination in topDestinations" :key="destination.slug || destination.id" :destination="destination" />
                </div>
                <div v-else class="card-soft mt-10 p-8 text-center text-gray-500 dark:text-gray-400">{{ $t('No destinations yet.') }}</div>
            </section>

            <section class="container-eco pb-12 sm:pb-16">
                <PromoBanner
                    :kicker="$t('Our mission')"
                    :title="$t('Why sustainability matters')"
                    :description="$t('See how we score destinations — conservation, community, and visitor care explained.')"
                    :cta-label="$t('Read more')"
                    to="/sustainability"
                    icon="leaf"
                    storage-key="ecojourney:promo:national"
                />
            </section>
        </template>
    </div>
</template>

<script setup>
import { computed, nextTick, onMounted, onUnmounted, ref, watch } from 'vue';
import { useRouter } from 'vue-router';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import 'leaflet.markercluster';
import 'leaflet.markercluster/dist/MarkerCluster.css';
import api from '../api';
import AppError from '../components/AppError.vue';
import AppLoader from '../components/AppLoader.vue';
import DestinationCard from '../components/DestinationCard.vue';
import PromoBanner from '../components/PromoBanner.vue';
import { t } from '../i18n';

const INDONESIA_BOUNDS = [[6.8, 94.5], [-11.6, 141.5]];
const FALLBACK_IMAGE = 'https://images.unsplash.com/photo-1469474968028-56623f02e42e?auto=format&fit=crop&w=800&q=80';

const router = useRouter();
const loading = ref(true);
const error = ref('');
const data = ref(null);
const activeRegion = ref('');
const mapEl = ref(null);

let map = null;
let markerLayer = null;

const stats = computed(() => data.value?.stats || {});
const regions = computed(() => data.value?.regions || []);
const points = computed(() => data.value?.points || []);
const topDestinations = computed(() => data.value?.top_destinations || []);

const visiblePoints = computed(() => {
    if (!activeRegion.value) return points.value;
    return points.value.filter((point) => point.region === activeRegion.value);
});

const statCards = computed(() => [
    { label: 'Destinations nationwide', value: stats.value.destinations ?? 0, icon: 'M12 21s7-5.3 7-12a7 7 0 1 0-14 0c0 6.7 7 12 7 12Zm0-9a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z' },
    { label: 'Island regions covered', value: stats.value.regions ?? 0, icon: 'M3.5 9.5 9 4l6 5.5M5.5 8.7V17a2 2 0 0 0 2 2h9a2 2 0 0 0 2-2V9.4M9.5 19v-5h5v5' },
    { label: 'Average eco score', value: `${stats.value.avg_score ?? 0}/100`, icon: 'M4 13h3l2.5 5L14 6l2.5 7H20' },
    { label: 'Curated activities', value: stats.value.activities ?? 0, icon: 'M12 3v3m0 12v3M3 12h3m12 0h3M6.2 6.2l2.1 2.1m7.4 7.4 2.1 2.1m0-11.6-2.1 2.1M8.3 15.7l-2.1 2.1M12 9.2a2.8 2.8 0 1 0 0 5.6 2.8 2.8 0 0 0 0-5.6Z' },
]);

function scoreTier(score) {
    const value = Number(score) || 0;
    if (value >= 85) return 'high';
    if (value >= 70) return 'mid';
    return 'low';
}

function markerIcon(point) {
    return L.divIcon({
        className: 'eco-marker-shell',
        html: `<div class="eco-marker eco-marker-${scoreTier(point.sustainability_score)}"><span>${point.sustainability_score}</span></div>`,
        iconSize: [40, 40],
        iconAnchor: [20, 20],
        popupAnchor: [0, -20],
    });
}

function buildPopup(point) {
    const card = document.createElement('div');
    card.className = 'eco-popup';

    const image = document.createElement('img');
    image.className = 'eco-popup-image';
    image.alt = point.name;
    image.loading = 'lazy';
    image.src = point.image_url || FALLBACK_IMAGE;
    image.addEventListener('error', () => {
        if (image.src !== FALLBACK_IMAGE) image.src = FALLBACK_IMAGE;
    }, { once: true });
    card.appendChild(image);

    const body = document.createElement('div');
    body.className = 'eco-popup-body';

    const meta = document.createElement('div');
    meta.className = 'eco-popup-meta';
    meta.textContent = `${point.category || t('Destination')} · ${point.region || 'Indonesia'}`;
    body.appendChild(meta);

    const title = document.createElement('h4');
    title.className = 'eco-popup-title';
    title.textContent = point.name;
    body.appendChild(title);

    const row = document.createElement('div');
    row.className = 'eco-popup-row';

    const location = document.createElement('span');
    location.className = 'eco-popup-location';
    location.textContent = point.location || '';
    row.appendChild(location);

    const score = document.createElement('span');
    score.className = `eco-popup-score eco-popup-score-${scoreTier(point.sustainability_score)}`;
    score.textContent = `${point.sustainability_score}/100`;
    row.appendChild(score);
    body.appendChild(row);

    const link = document.createElement('a');
    link.className = 'eco-popup-link';
    link.href = `/destinations/${point.slug}`;
    link.textContent = t('View destination');
    link.addEventListener('click', (event) => {
        event.preventDefault();
        map?.closePopup();
        router.push({ name: 'destination', params: { slug: point.slug } });
    });
    body.appendChild(link);

    card.appendChild(body);
    return card;
}

function fitToSelection() {
    if (!map) return;

    const visible = visiblePoints.value;
    const region = regions.value.find((item) => item.region === activeRegion.value);

    if (region?.center) {
        map.setView([region.center.latitude, region.center.longitude], 6, { animate: true });
        return;
    }

    if (!visible.length) {
        map.fitBounds(INDONESIA_BOUNDS, { padding: [24, 24] });
        return;
    }

    if (visible.length === 1) {
        map.setView([visible[0].latitude, visible[0].longitude], 9, { animate: true });
        return;
    }

    const bounds = L.latLngBounds(visible.map((point) => [point.latitude, point.longitude]));
    map.fitBounds(bounds, { padding: [48, 48], maxZoom: 8, animate: true });
}

function renderMarkers(shouldFit = true) {
    if (!markerLayer) return;

    markerLayer.clearLayers();

    visiblePoints.value.forEach((point) => {
        const marker = L.marker([point.latitude, point.longitude], {
            icon: markerIcon(point),
            title: point.name,
            riseOnHover: true,
        });
        marker.bindPopup(() => buildPopup(point), { maxWidth: 260, minWidth: 220 });
        marker.addTo(markerLayer);
    });

    if (shouldFit) fitToSelection();
}

function initMap() {
    if (map || !mapEl.value) return;

    map = L.map(mapEl.value, {
        scrollWheelZoom: false,
        minZoom: 4,
        maxZoom: 12,
        zoomControl: true,
        attributionControl: true,
    });

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19,
    }).addTo(map);

    markerLayer = L.markerClusterGroup({
        maxClusterRadius: 48,
        showCoverageOnHover: false,
        spiderfyOnMaxZoom: true,
        disableClusteringAtZoom: 10,
        iconCreateFunction: (cluster) => L.divIcon({
            className: 'eco-marker-shell',
            html: `<div class="eco-cluster">${cluster.getChildCount()}</div>`,
            iconSize: [44, 44],
            iconAnchor: [22, 22],
        }),
    }).addTo(map);
    renderMarkers(false);
    map.fitBounds(INDONESIA_BOUNDS, { padding: [24, 24] });
}

function selectRegion(region) {
    activeRegion.value = region;
    if (region) {
        document.getElementById('national-map-section')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
}

async function loadNational() {
    loading.value = true;
    error.value = '';

    try {
        const payload = await api.national();
        data.value = payload?.data || payload;
    } catch (loadError) {
        error.value = loadError.message;
        data.value = null;
    } finally {
        loading.value = false;
    }

    if (data.value) {
        await nextTick();
        initMap();
    }
}

watch(activeRegion, () => renderMarkers(true));

onMounted(loadNational);

onUnmounted(() => {
    if (map) {
        map.remove();
        map = null;
        markerLayer = null;
    }
});
</script>

<style scoped>
.national-map {
    z-index: 0;
}

.national-map :deep(.eco-marker-shell) {
    background: transparent;
    border: 0;
}

.national-map :deep(.eco-marker) {
    display: flex;
    height: 40px;
    width: 40px;
    align-items: center;
    justify-content: center;
    border-radius: 9999px;
    border: 3px solid #fff;
    color: #fff;
    font-size: 11px;
    font-weight: 800;
    box-shadow: 0 6px 16px rgba(15, 23, 42, 0.28);
    transition: transform 0.15s ease;
}

.national-map :deep(.eco-marker:hover) {
    transform: scale(1.12);
}

.national-map :deep(.eco-cluster) {
    display: flex;
    height: 44px;
    width: 44px;
    align-items: center;
    justify-content: center;
    border-radius: 9999px;
    border: 3px solid #fff;
    background: #166534;
    color: #fff;
    font-family: var(--font-display, 'Plus Jakarta Sans', sans-serif);
    font-size: 13px;
    font-weight: 800;
    box-shadow: 0 8px 20px rgba(15, 23, 42, 0.3);
}

.national-map :deep(.eco-marker-high) {
    background: #10b981;
}

.national-map :deep(.eco-marker-mid) {
    background: #f59e0b;
}

.national-map :deep(.eco-marker-low) {
    background: #f43f5e;
}

.national-map :deep(.leaflet-popup-content-wrapper) {
    border-radius: 20px;
    padding: 0;
    box-shadow: 0 18px 40px rgba(15, 23, 42, 0.18);
    overflow: hidden;
}

.national-map :deep(.leaflet-popup-content) {
    margin: 0;
    width: 230px !important;
}

.national-map :deep(.leaflet-popup-tip) {
    box-shadow: 0 18px 40px rgba(15, 23, 42, 0.18);
}

.national-map :deep(.eco-popup-image) {
    display: block;
    height: 110px;
    width: 100%;
    object-fit: cover;
    background: #ecfdf5;
}

.national-map :deep(.eco-popup-body) {
    padding: 12px 14px 14px;
}

.national-map :deep(.eco-popup-meta) {
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: #9ca3af;
}

.national-map :deep(.eco-popup-title) {
    margin-top: 4px;
    font-family: var(--font-display, 'Plus Jakarta Sans', sans-serif);
    font-size: 16px;
    font-weight: 800;
    color: #111827;
}

.national-map :deep(.eco-popup-row) {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    margin-top: 8px;
}

.national-map :deep(.eco-popup-location) {
    font-size: 11px;
    font-weight: 600;
    color: #6b7280;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.national-map :deep(.eco-popup-score) {
    flex-shrink: 0;
    border-radius: 9999px;
    padding: 3px 8px;
    font-size: 11px;
    font-weight: 800;
    color: #fff;
}

.national-map :deep(.eco-popup-score-high) {
    background: #10b981;
}

.national-map :deep(.eco-popup-score-mid) {
    background: #f59e0b;
}

.national-map :deep(.eco-popup-score-low) {
    background: #f43f5e;
}

.national-map :deep(.eco-popup-link) {
    display: inline-flex;
    margin-top: 12px;
    font-size: 13px;
    font-weight: 800;
    color: #166534;
    text-decoration: none;
}

.national-map :deep(.eco-popup-link:hover) {
    text-decoration: underline;
}

/* Dark mode — invert raster tiles so OSM reads well on dark surfaces */
.dark .national-map {
    background: #0f1713;
}

.dark .national-map :deep(.leaflet-tile-pane) {
    filter: invert(1) hue-rotate(180deg) saturate(0.7) brightness(0.92) contrast(0.9);
}

.dark .national-map :deep(.leaflet-popup-content-wrapper),
.dark .national-map :deep(.leaflet-popup-tip) {
    background: #16211b;
    color: #e7ede9;
    box-shadow: 0 18px 40px rgba(0, 0, 0, 0.55);
}

.dark .national-map :deep(.eco-popup-title) {
    color: #e7ede9;
}

.dark .national-map :deep(.eco-popup-location) {
    color: #9aa8a0;
}

.dark .national-map :deep(.eco-popup-link) {
    color: #7dc79a;
}

.dark .national-map :deep(.eco-popup-image) {
    background: #0f1713;
}

.dark .national-map :deep(.leaflet-bar a) {
    background: #16211b;
    color: #e7ede9;
    border-color: #26332b;
}

.dark .national-map :deep(.leaflet-bar a:hover) {
    background: #1e2c24;
}

.dark .national-map :deep(.leaflet-control-attribution) {
    background: rgba(22, 33, 27, 0.85);
    color: #9aa8a0;
}

.dark .national-map :deep(.leaflet-control-attribution a) {
    color: #7dc79a;
}

.dark .national-map :deep(.leaflet-container a) {
    color: #7dc79a;
}
</style>
