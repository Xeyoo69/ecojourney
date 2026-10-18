import { createRouter, createWebHistory } from 'vue-router';
import { auth } from './state/auth';
import { t } from './i18n';
import { loadFavorites } from './utils/favorites';

const routes = [
    { path: '/', name: 'home', component: () => import('./views/HomeView.vue'), meta: { title: 'EcoJourney — Travel Better' } },
    { path: '/explore', name: 'explore', component: () => import('./views/ExploreView.vue'), meta: { title: 'Explore Destinations — EcoJourney' } },
    { path: '/national', name: 'national', component: () => import('./views/NationalView.vue'), meta: { title: 'National Map — EcoJourney' } },
    { path: '/destinations/:slug', name: 'destination', component: () => import('./views/DestinationView.vue'), props: true },
    { path: '/planner', name: 'planner', component: () => import('./views/PlannerView.vue'), meta: { requiresAuth: true, title: 'Trip Planner — EcoJourney' } },
    { path: '/planner/result/:id', name: 'planner-result', component: () => import('./views/PlannerResultView.vue'), props: true, meta: { requiresAuth: true, title: 'Your Journey — EcoJourney' } },
    { path: '/favorites', name: 'favorites', component: () => import('./views/FavoritesView.vue'), meta: { requiresAuth: true, title: 'Favorite Destinations — EcoJourney' } },
    { path: '/sustainability', name: 'sustainability', component: () => import('./views/ContentView.vue'), props: { page: 'sustainability' }, meta: { title: 'Sustainable Travel — EcoJourney' } },
    { path: '/about', name: 'about', component: () => import('./views/ContentView.vue'), props: { page: 'about' }, meta: { title: 'About EcoJourney' } },
    { path: '/contact', name: 'contact', component: () => import('./views/ContentView.vue'), props: { page: 'contact' }, meta: { title: 'Contact EcoJourney' } },
    { path: '/login', name: 'login', component: () => import('./views/AuthView.vue'), props: { mode: 'login' }, meta: { guestOnly: true, title: 'Sign in — EcoJourney' } },
    { path: '/register', name: 'register', component: () => import('./views/AuthView.vue'), props: { mode: 'register' }, meta: { guestOnly: true, title: 'Create account — EcoJourney' } },
    { path: '/:pathMatch(.*)*', name: 'not-found', component: () => import('./views/NotFoundView.vue'), meta: { title: 'Page not found — EcoJourney' } },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
    scrollBehavior(to, from, savedPosition) {
        return savedPosition || { top: 0, behavior: 'smooth' };
    },
});

router.beforeEach(async (to) => {
    if (to.meta.requiresAuth || to.meta.guestOnly || !auth.state.initialized) {
        await auth.loadUser();
    }

    if (to.meta.requiresAuth && !auth.isAuthenticated()) {
        return { name: 'login', query: { redirect: to.fullPath } };
    }

    if (to.meta.guestOnly && auth.isAuthenticated()) {
        return { name: 'home' };
    }

    if (auth.isAuthenticated()) {
        await loadFavorites();
    }

    return true;
});

router.afterEach((to) => {
    document.title = t(to.meta.title || 'EcoJourney');
});

export default router;
