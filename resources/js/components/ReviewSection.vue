<template>
    <section class="mt-14">
        <div class="section-kicker">{{ $t('Reviews') }}</div>
        <h2 class="mt-3 font-display text-2xl font-extrabold text-gray-900 dark:text-gray-100">{{ $t('What travelers say') }}</h2>

        <div class="mt-6 flex flex-wrap items-center gap-6">
            <div class="flex items-baseline gap-2">
                <span class="font-display text-4xl font-extrabold text-forest-700 dark:text-forest-300">{{ averageRating }}</span>
                <span class="text-sm font-semibold text-gray-500 dark:text-gray-400">/ 5</span>
            </div>
            <div class="flex items-center gap-1 text-forest-500 dark:text-forest-400">
                <svg v-for="i in 5" :key="i" class="h-5 w-5" :fill="i <= Math.round(Number(averageRating)) ? 'currentColor' : 'none'" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.5a.56.56 0 0 1 1.04 0l2.13 5.1a.56.56 0 0 0 .48.34l5.52.44c.55.04.77.73.37 1.1l-4.2 3.6a.56.56 0 0 0-.18.56l1.28 5.37a.56.56 0 0 1-.84.61l-4.78-2.9a.56.56 0 0 0-.58 0l-4.78 2.9a.56.56 0 0 1-.84-.61l1.28-5.37a.56.56 0 0 0-.18-.56l-4.2-3.6a.56.56 0 0 1 .37-1.1l5.52-.44a.56.56 0 0 0 .48-.34l2.13-5.1Z" /></svg>
            </div>
            <span class="text-sm font-semibold text-gray-500 dark:text-gray-400">{{ $t('{count} reviews', { count: reviews.length }) }}</span>
        </div>

        <form v-if="auth.isAuthenticated()" class="card-soft mt-8 p-6" @submit.prevent="submitReview">
            <div class="text-xs font-bold uppercase tracking-[0.18em] text-forest-600 dark:text-forest-300">{{ myReview ? $t('Update your review') : $t('Write a review') }}</div>
            <div class="mt-4 flex items-center gap-3">
                <span class="text-sm font-semibold text-gray-600 dark:text-gray-300">{{ $t('Your rating') }}</span>
                <div class="flex items-center gap-1">
                    <button v-for="i in 5" :key="i" type="button" class="transition" :class="i <= form.rating ? 'text-forest-500 dark:text-forest-400' : 'text-gray-300 hover:text-forest-400 dark:text-gray-600'" :aria-label="`${i} stars`" @click="form.rating = i">
                        <svg class="h-6 w-6 cursor-pointer" :fill="i <= form.rating ? 'currentColor' : 'none'" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.5a.56.56 0 0 1 1.04 0l2.13 5.1a.56.56 0 0 0 .48.34l5.52.44c.55.04.77.73.37 1.1l-4.2 3.6a.56.56 0 0 0-.18.56l1.28 5.37a.56.56 0 0 1-.84.61l-4.78-2.9a.56.56 0 0 0-.58 0l-4.78 2.9a.56.56 0 0 1-.84-.61l1.28-5.37a.56.56 0 0 0-.18-.56l-4.2-3.6a.56.56 0 0 1 .37-1.1l5.52-.44a.56.56 0 0 0 .48-.34l2.13-5.1Z" /></svg>
                    </button>
                </div>
            </div>
            <input v-model="form.title" type="text" :placeholder="$t('Title (optional)')" maxlength="120" class="mt-4 w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 outline-none transition focus:border-forest-500 focus:ring-2 focus:ring-forest-100 dark:border-white/10 dark:bg-white/5 dark:text-gray-100 dark:placeholder:text-gray-500 dark:focus:ring-forest-500/25" />
            <textarea v-model="form.comment" rows="3" required minlength="10" :placeholder="$t('Share your experience (min. 10 characters)')" class="mt-3 w-full resize-none rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 outline-none transition focus:border-forest-500 focus:ring-2 focus:ring-forest-100 dark:border-white/10 dark:bg-white/5 dark:text-gray-100 dark:placeholder:text-gray-500 dark:focus:ring-forest-500/25"></textarea>
            <p v-if="formError" class="mt-3 text-sm font-semibold text-rose-600 dark:text-rose-400">{{ formError }}</p>
            <div class="mt-4 flex items-center gap-3">
                <button type="submit" class="eco-btn eco-btn-primary" :disabled="submitting">{{ submitting ? $t('Sending…') : (myReview ? $t('Update review') : $t('Submit review')) }}</button>
                <span v-if="formSuccess" class="text-sm font-semibold text-forest-700 dark:text-forest-300">{{ $t('Thank you for your review!') }}</span>
            </div>
        </form>
        <div v-else class="card-soft mt-8 p-6 text-center text-sm text-gray-500 dark:text-gray-400">
            <RouterLink to="/login" class="font-bold text-forest-700 dark:text-forest-300">{{ $t('Sign in') }}</RouterLink>
            {{ $t('to write a review.') }}
        </div>

        <div v-if="loading" class="mt-8"><AppLoader :label="$t('Loading reviews…')" /></div>
        <AppError v-else-if="loadError" class="mt-8" :message="loadError" :retry="loadReviews" />
        <div v-else-if="reviews.length" class="mt-8 space-y-5">
            <article v-for="review in reviews" :key="review.id" class="card-soft p-6">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-forest-100 text-xs font-extrabold text-forest-800 dark:bg-forest-800 dark:text-forest-200">{{ initialsOf(review.user?.name) }}</div>
                        <div>
                            <div class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ review.user?.name || $t('Traveler') }}</div>
                            <div class="flex items-center gap-0.5 text-forest-500 dark:text-forest-400">
                                <svg v-for="i in 5" :key="i" class="h-3.5 w-3.5" :fill="i <= review.rating ? 'currentColor' : 'none'" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.5a.56.56 0 0 1 1.04 0l2.13 5.1a.56.56 0 0 0 .48.34l5.52.44c.55.04.77.73.37 1.1l-4.2 3.6a.56.56 0 0 0-.18.56l1.28 5.37a.56.56 0 0 1-.84.61l-4.78-2.9a.56.56 0 0 0-.58 0l-4.78 2.9a.56.56 0 0 1-.84-.61l1.28-5.37a.56.56 0 0 0-.18-.56l-4.2-3.6a.56.56 0 0 1 .37-1.1l5.52-.44a.56.56 0 0 0 .48-.34l2.13-5.1Z" /></svg>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <time class="text-xs font-semibold text-gray-500 dark:text-gray-400">{{ formatDate(review.created_at) }}</time>
                        <button v-if="canDelete(review)" type="button" class="text-xs font-bold text-rose-600 transition hover:text-rose-700 dark:text-rose-400 dark:hover:text-rose-300" @click="removeReview(review)">{{ $t('Delete') }}</button>
                    </div>
                </div>
                <h3 v-if="review.title" class="mt-4 font-display font-bold text-gray-900 dark:text-gray-100">{{ review.title }}</h3>
                <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-300">{{ review.comment }}</p>
            </article>
        </div>
        <p v-else class="mt-8 rounded-2xl bg-forest-50 p-5 text-sm leading-6 text-gray-600 dark:bg-white/5 dark:text-gray-300">{{ $t('No reviews yet. Be the first to share your experience!') }}</p>
    </section>
</template>

<script setup>
import { computed, onMounted, reactive, ref } from 'vue';
import { RouterLink } from 'vue-router';
import api from '../api';
import AppError from './AppError.vue';
import AppLoader from './AppLoader.vue';
import { auth } from '../state/auth';
import { state as i18n, t } from '../i18n';

const props = defineProps({ slug: { type: String, required: true } });

const reviews = ref([]);
const loading = ref(true);
const loadError = ref('');
const submitting = ref(false);
const formError = ref('');
const formSuccess = ref(false);
const form = reactive({ rating: 0, title: '', comment: '' });

function isMine(review) {
    const me = auth.state.user;
    if (!me || !review?.user) return false;
    if (review.user.id != null && me.id != null) return review.user.id === me.id;
    return review.user.name === me.name;
}

const myReview = computed(() => {
    if (!auth.state.user) return null;
    return reviews.value.find((review) => isMine(review)) || null;
});

const averageRating = computed(() => {
    if (!reviews.value.length) return '0.0';
    const total = reviews.value.reduce((sum, review) => sum + review.rating, 0);
    return (total / reviews.value.length).toFixed(1);
});

function initialsOf(name) {
    if (!name) return 'T';
    return name.split(' ').map((part) => part[0]).slice(0, 2).join('').toUpperCase();
}

function formatDate(iso) {
    if (!iso) return '';
    try {
        const localeTag = i18n.locale === 'en' ? 'en-US' : 'id-ID';
        return new Intl.DateTimeFormat(localeTag, { day: 'numeric', month: 'long', year: 'numeric' }).format(new Date(iso));
    } catch {
        return '';
    }
}

function canDelete(review) {
    return isMine(review);
}

async function loadReviews() {
    loading.value = true;
    loadError.value = '';
    try {
        const payload = await api.reviews(props.slug);
        reviews.value = Array.isArray(payload?.data) ? payload.data : (Array.isArray(payload) ? payload : []);
        const mine = myReview.value;
        if (mine) {
            form.rating = mine.rating;
            form.title = mine.title || '';
            form.comment = mine.comment || '';
        }
    } catch (error) {
        loadError.value = error?.message || t('Unable to load reviews.');
        reviews.value = [];
    } finally {
        loading.value = false;
    }
}

async function submitReview() {
    formError.value = '';
    formSuccess.value = false;
    if (!form.rating) {
        formError.value = t('Please select a rating.');
        return;
    }
    submitting.value = true;
    try {
        await api.createReview(props.slug, {
            rating: form.rating,
            title: form.title || null,
            comment: form.comment,
        });
        formSuccess.value = true;
        await loadReviews();
    } catch (error) {
        formError.value = error.message || t('Unable to submit your review.');
    } finally {
        submitting.value = false;
    }
}

async function removeReview(review) {
    if (!window.confirm(t('Delete this review?'))) return;
    try {
        await api.deleteReview(props.slug, review.id);
        await loadReviews();
    } catch (error) {
        formError.value = error.message || t('Unable to delete the review.');
    }
}

onMounted(loadReviews);
</script>
