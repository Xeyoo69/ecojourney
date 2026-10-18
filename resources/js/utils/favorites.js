import { computed, ref, watch } from 'vue';
import { useRouter } from 'vue-router';
import api from '../api';
import { auth } from '../state/auth';
import { asArray } from './data';

const FAVORITES_EVENT = 'ecojourney:favorites';

const slugs = ref([]);
const loading = ref(false);
const error = ref('');
let loadedForUserId = null;

watch(
    () => auth.state.user,
    (user) => {
        if (!user) {
            slugs.value = [];
            loadedForUserId = null;
            emit([]);
        }
    },
);

function emit(next) {
    window.dispatchEvent(new CustomEvent(FAVORITES_EVENT, { detail: next }));
}

export function favoriteSlugs() {
    return slugs.value;
}

export async function loadFavorites({ force = false } = {}) {
    if (!auth.isAuthenticated()) {
        slugs.value = [];
        loadedForUserId = null;
        return [];
    }

    const userId = auth.state.user?.id ?? null;
    if (!force && loadedForUserId === userId) {
        return slugs.value;
    }

    loading.value = true;
    error.value = '';

    try {
        const payload = await api.favorites();
        const destinations = asArray(payload?.data ? payload : { data: payload?.data });
        slugs.value = Array.isArray(payload?.slugs) && payload.slugs.length
            ? [...new Set(payload.slugs)]
            : destinations.map((item) => item?.slug).filter(Boolean);
        loadedForUserId = userId;
        emit(slugs.value);
    } catch (loadError) {
        slugs.value = [];
        loadedForUserId = null;
        error.value = loadError.message || 'Unable to load your favorites.';
    } finally {
        loading.value = false;
    }

    return slugs.value;
}

export function useFavorites() {
    const router = useRouter();
    const favoriteList = computed(() => slugs.value);
    const isFavorite = (slug) => Boolean(slug) && slugs.value.includes(slug);

    async function toggle(slug) {
        if (!slug) {
            return false;
        }

        error.value = '';

        if (!auth.isAuthenticated()) {
            const currentPath = router.currentRoute.value.fullPath;
            router.push({ name: 'login', query: { redirect: currentPath, reason: 'favorites' } });
            return false;
        }

        try {
            if (!loadedForUserId) {
                await loadFavorites();
            }

            if (isFavorite(slug)) {
                await api.removeFavorite(slug);
                slugs.value = slugs.value.filter((item) => item !== slug);
                emit(slugs.value);
                return true;
            }

            await api.addFavorite(slug);
            slugs.value = [...new Set([...slugs.value, slug])];
            emit(slugs.value);
            return true;
        } catch (toggleError) {
            error.value = toggleError?.message || 'Unable to update your favorites.';
            return false;
        }
    }

    async function remove(slug) {
        if (!slug || !auth.isAuthenticated()) {
            return false;
        }

        error.value = '';

        try {
            await api.removeFavorite(slug);
            slugs.value = slugs.value.filter((item) => item !== slug);
            emit(slugs.value);
            return true;
        } catch (removeError) {
            error.value = removeError?.message || 'Unable to update your favorites.';
            return false;
        }
    }

    async function clear() {
        error.value = '';

        if (!auth.isAuthenticated()) {
            slugs.value = [];
            loadedForUserId = null;
            emit(slugs.value);
            return true;
        }

        try {
            await api.clearFavorites();
            slugs.value = [];
            emit(slugs.value);
            return true;
        } catch (clearError) {
            error.value = clearError?.message || 'Unable to clear your favorites.';
            return false;
        }
    }

    return {
        favorites: favoriteList,
        loading,
        error,
        loadFavorites,
        isFavorite,
        toggle,
        remove,
        clear,
    };
}
