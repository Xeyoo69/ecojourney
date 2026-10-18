<template>
    <article class="destination-card group h-full">
        <div class="relative h-56 overflow-hidden bg-forest-100 dark:bg-forest-900">
            <img
                :src="imageUrl(destination)"
                :alt="destination.name"
                class="destination-image h-full w-full object-cover"
                loading="lazy"
                @error="useFallback"
            >
            <div class="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-black/45 to-transparent"></div>
            <div class="absolute left-4 top-4 flex max-w-[75%] flex-wrap gap-2">
                <span class="rounded-full border border-white/50 bg-white/90 px-3 py-1.5 text-[10px] font-extrabold uppercase tracking-wider text-forest-700 shadow-sm backdrop-blur">
                    {{ categoryName(destination) }}
                </span>
            </div>
            <button
                type="button"
                class="absolute right-4 top-4 flex h-10 w-10 cursor-pointer items-center justify-center rounded-full border border-white/40 bg-white/90 text-gray-500 shadow-sm backdrop-blur transition hover:scale-105 hover:bg-white hover:text-rose-500"
                :class="[
                    isFavorite(destination.slug) ? 'text-rose-500' : '',
                    heartPop ? 'heart-pop' : ''
                ]"
                :aria-label="isFavorite(destination.slug) ? $t('Remove {name} from favorites', { name: destination.name }) : $t('Save {name} to favorites', { name: destination.name })"
                :title="auth.isAuthenticated() ? (isFavorite(destination.slug) ? $t('Remove from favorites') : $t('Save to favorites')) : $t('Sign in to save favorites')"
                @click.prevent="onToggle"
            >
                <svg class="h-5 w-5" :fill="isFavorite(destination.slug) ? 'currentColor' : 'none'" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78L12 21.23l8.84-8.84a5.5 5.5 0 0 0 0-7.78Z" />
                </svg>
            </button>
            <div class="absolute bottom-4 right-4 rounded-full bg-gradient-to-br from-forest-600 to-forest-800 px-3 py-1.5 text-xs font-extrabold text-white shadow-lg shadow-forest-950/25">
                {{ score }}/100
            </div>
        </div>
        <div class="flex h-[calc(100%-14rem)] flex-col p-5">
            <div class="flex items-center gap-2 text-xs font-semibold text-gray-500 dark:text-gray-400">
                <svg class="h-4 w-4 text-forest-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s7-5.3 7-12a7 7 0 1 0-14 0c0 6.7 7 12 7 12Z" />
                    <circle cx="12" cy="9" r="2.2" />
                </svg>
                <span class="truncate">{{ destinationLocation(destination) }}</span>
            </div>
            <RouterLink :to="`/destinations/${destination.slug}`" class="mt-3 font-display text-xl font-extrabold text-gray-900 transition hover:text-forest-700 dark:text-gray-100 dark:hover:text-forest-300">
                {{ destination.name }}
            </RouterLink>
            <p class="mt-3 line-clamp-3 text-sm leading-6 text-gray-500 dark:text-gray-400">
                {{ destination.description || $t('Discover a thoughtful journey rooted in nature, culture, and local community.') }}
            </p>
            <RouterLink :to="`/destinations/${destination.slug}`" class="mt-5 inline-flex items-center gap-2 text-sm font-bold text-forest-700 transition hover:gap-3 dark:text-forest-300">
                {{ $t('Discover destination') }}
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 6l6 6-6 6" />
                </svg>
            </RouterLink>
        </div>
    </article>
</template>

<script setup>
import { computed, ref } from 'vue';
import { categoryName, destinationLocation, destinationScore, imageUrl } from '../utils/data';
import { useFavorites } from '../utils/favorites';
import { auth } from '../state/auth';

const props = defineProps({
    destination: {
        type: Object,
        required: true,
    },
});

const { isFavorite, toggle } = useFavorites();
const score = computed(() => destinationScore(props.destination));
const fallbackImage = 'https://images.unsplash.com/photo-1469474968028-56623f02e42e?auto=format&fit=crop&w=1200&q=80';
const heartPop = ref(false);

async function onToggle() {
    heartPop.value = true;
    await toggle(props.destination.slug);
    setTimeout(() => { heartPop.value = false; }, 450);
}

function useFallback(event) {
    if (event.target.src !== fallbackImage) {
        event.target.src = fallbackImage;
    }
}
</script>
