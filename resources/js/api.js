import axios from 'axios';

const endpointConfig = window.ECOJOURNEY_API_ENDPOINTS || {};

const endpoint = (key, fallback, replacements = {}) => {
    let value = endpointConfig[key] || fallback;

    Object.entries(replacements).forEach(([name, replacement]) => {
        value = value.replace(`{${name}}`, encodeURIComponent(replacement));
    });

    return value;
};

export const endpoints = {
    home: () => endpoint('home', '/api/home'),
    national: () => endpoint('national', '/api/national'),
    destinations: () => endpoint('destinations', '/api/destinations'),
    destination: (slug) => endpoint('destination', '/api/destinations/{slug}', { slug }),
    categories: () => endpoint('categories', '/api/categories'),
    user: () => endpoint('user', '/api/auth/user'),
    login: () => endpoint('login', '/api/auth/login'),
    register: () => endpoint('register', '/api/auth/register'),
    logout: () => endpoint('logout', '/api/auth/logout'),
    plannerGenerate: () => endpoint('plannerGenerate', '/api/planner/generate'),
    plannerResult: (id) => endpoint('plannerResult', '/api/planner/result/{id}', { id }),
    favorites: () => endpoint('favorites', '/api/favorites'),
    favorite: (slug) => endpoint('favorite', '/api/favorites/{slug}', { slug }),
    reviews: (slug) => endpoint('reviews', '/api/destinations/{slug}/reviews', { slug }),
    review: (slug, id) => endpoint('review', '/api/destinations/{slug}/reviews/{id}', { slug, id }),
    chat: () => endpoint('chat', '/api/chat'),
};

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

const client = axios.create({
    baseURL: window.ECOJOURNEY_API_BASE || '/',
    withCredentials: true,
    headers: {
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'Content-Type': 'application/json',
    },
});

if (csrfToken) {
    client.defaults.headers.common['X-CSRF-TOKEN'] = csrfToken;
}

// Laravel rotates the CSRF token on login/logout (session()->regenerateToken()).
// The meta tag is only read once at page load, so refresh the header from the
// XSRF-TOKEN cookie on every request to avoid stale-token 419 errors.
client.interceptors.request.use((config) => {
    const cookie = document.cookie
        .split('; ')
        .find((row) => row.startsWith('XSRF-TOKEN='));
    if (cookie) {
        config.headers['X-XSRF-TOKEN'] = decodeURIComponent(cookie.split('=')[1]);
    }
    return config;
});

const unwrap = (response) => response.data;

export function normalizeApiError(error) {
    const response = error?.response;
    const data = response?.data;
    const status = response?.status || 0;

    let message = 'Something went wrong. Please try again.';

    if (status === 401) {
        message = 'Please sign in to continue.';
    } else if (status === 404) {
        message = 'Requested resource was not found.';
    } else if (status === 429) {
        message = 'Too many requests. Please wait a moment and try again.';
    } else if (status === 422) {
        message = data?.message || 'Please check the highlighted fields.';
    } else if (typeof data === 'string' && data.trim()) {
        message = data;
    } else if (data?.message) {
        message = data.message;
    } else if (error?.message) {
        message = error.message;
    }

    return {
        status,
        message,
        errors: data?.errors || {},
        raw: error,
    };
}

async function request(config) {
    try {
        const response = await client.request(config);
        return unwrap(response);
    } catch (error) {
        throw normalizeApiError(error);
    }
}

async function optionalRequest(config) {
    try {
        return await request(config);
    } catch (error) {
        if (error.status === 401) {
            return null;
        }

        throw error;
    }
}

export const api = {
    client,
    home: () => request({ method: 'get', url: endpoints.home() }),
    national: () => request({ method: 'get', url: endpoints.national() }),
    destinations: (params = {}) => request({ method: 'get', url: endpoints.destinations(), params }),
    destination: (slug) => request({ method: 'get', url: endpoints.destination(slug) }),
    categories: () => request({ method: 'get', url: endpoints.categories() }),
    user: () => optionalRequest({ method: 'get', url: endpoints.user() }),
    login: (data) => request({ method: 'post', url: endpoints.login(), data }),
    register: (data) => request({ method: 'post', url: endpoints.register(), data }),
    logout: () => request({ method: 'post', url: endpoints.logout() }),
    generatePlanner: (data) => request({ method: 'post', url: endpoints.plannerGenerate(), data }),
    plannerResult: (id) => request({ method: 'get', url: endpoints.plannerResult(id) }),
    favorites: () => request({ method: 'get', url: endpoints.favorites() }),
    addFavorite: (slug) => request({ method: 'post', url: endpoints.favorite(slug) }),
    removeFavorite: (slug) => request({ method: 'delete', url: endpoints.favorite(slug) }),
    clearFavorites: () => request({ method: 'delete', url: endpoints.favorites() }),
    reviews: (slug) => request({ method: 'get', url: endpoints.reviews(slug) }),
    createReview: (slug, data) => request({ method: 'post', url: endpoints.reviews(slug), data }),
    deleteReview: (slug, id) => request({ method: 'delete', url: endpoints.review(slug, id) }),
    sendChat: (data) => request({ method: 'post', url: endpoints.chat(), data }),
};

export default api;
