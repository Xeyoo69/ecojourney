import { reactive } from 'vue';
import api from '../api';

const state = reactive({
    user: null,
    loading: false,
    initialized: false,
    error: null,
});

function pickUser(payload) {
    const user = payload?.user || payload?.data?.user || payload?.data || payload;

    if (!user || Array.isArray(user) || typeof user !== 'object') {
        return null;
    }

    return user;
}

async function loadUser({ force = false } = {}) {
    if (state.loading || (state.initialized && !force)) {
        return state.user;
    }

    state.loading = true;
    state.error = null;

    try {
        state.user = pickUser(await api.user());
    } catch (error) {
        state.user = null;
        state.error = error.message;
    } finally {
        state.loading = false;
        state.initialized = true;
    }

    return state.user;
}

async function login(credentials) {
    state.loading = true;
    state.error = null;

    try {
        const payload = await api.login(credentials);
        state.user = pickUser(payload) || await loadUser({ force: true });
        state.initialized = true;
        return state.user;
    } catch (error) {
        state.error = error.message;
        throw error;
    } finally {
        state.loading = false;
    }
}

async function register(details) {
    state.loading = true;
    state.error = null;

    try {
        const payload = await api.register(details);
        state.user = pickUser(payload) || await loadUser({ force: true });
        state.initialized = true;
        return state.user;
    } catch (error) {
        state.error = error.message;
        throw error;
    } finally {
        state.loading = false;
    }
}

async function logout() {
    state.loading = true;
    state.error = null;

    try {
        await api.logout();
    } catch (error) {
        state.error = error?.message || 'Sign out failed. Please try again.';
        console.error('[auth] logout request failed', error);
    } finally {
        state.user = null;
        state.loading = false;
        state.initialized = true;
    }

    // Clearing state.user only records intent — the server decides whether the
    // session is really gone. Re-check so a failed logout (419/429/network) can
    // never leave the UI claiming the user signed out.
    await loadUser({ force: true });

    if (state.user) {
        state.error = 'Sign out failed - your session is still active. Please try again.';
        return false;
    }

    return true;
}

export const auth = {
    state,
    loadUser,
    login,
    register,
    logout,
    isAuthenticated: () => Boolean(state.user),
};
