<template>
    <section class="container-eco flex min-h-[calc(100vh-260px)] items-center justify-center py-14 sm:py-20">
        <div class="w-full max-w-lg rounded-[32px] border border-gray-100 bg-white p-7 shadow-xl shadow-forest-900/5 sm:p-10 dark:border-white/10 dark:bg-[#16211b] dark:shadow-black/40">
            <div class="text-center"><div class="section-kicker">{{ isRegister ? $t('Join EcoJourney') : $t('Welcome back') }}</div><h1 class="mt-3 font-display text-3xl font-extrabold text-forest-900 dark:text-forest-100">{{ isRegister ? $t('Create your account.') : $t('Sign in to continue.') }}</h1><p class="mt-3 text-sm leading-6 text-gray-500 dark:text-gray-400">{{ isRegister ? $t('Save places and plan your next thoughtful journey.') : $t('Your next thoughtful journey is waiting.') }}</p></div>
            <div v-if="reason === 'favorites'" class="mt-6 flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm leading-6 text-emerald-800 dark:border-emerald-500/25 dark:bg-emerald-500/10 dark:text-emerald-300">
                <svg class="mt-0.5 h-5 w-5 shrink-0 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78L12 21.23l8.84-8.84a5.5 5.5 0 0 0 0-7.78Z" /></svg>
                <span><strong>{{ $t('Sign in to save favorites.') }}</strong> {{ isRegister ? $t('Create an account') : $t('Log in') }} {{ $t('first — you’ll be taken back to the destination right after.') }}</span>
            </div>
            <div v-if="oauthError" class="mt-6 rounded-2xl bg-rose-50 p-4 text-sm font-semibold text-rose-700 dark:bg-rose-500/10 dark:text-rose-300">{{ $t(oauthError) }}</div>
            <form class="mt-8 space-y-5" @submit.prevent="submit">
                <label v-if="isRegister" class="block text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ $t('Name') }}<input v-model.trim="form.name" required autocomplete="name" class="input-soft mt-2 h-12 w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 text-sm outline-none dark:border-white/10 dark:bg-white/5 dark:text-gray-100 dark:placeholder:text-gray-500" :class="fieldError('name') ? 'border-rose-400' : ''" :placeholder="$t('Your name')"><span v-if="fieldError('name')" class="mt-1 block text-xs font-semibold text-rose-600 dark:text-rose-400">{{ $t(fieldError('name')) }}</span></label>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ $t('Email') }}<input v-model.trim="form.email" required type="email" autocomplete="email" class="input-soft mt-2 h-12 w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 text-sm outline-none dark:border-white/10 dark:bg-white/5 dark:text-gray-100 dark:placeholder:text-gray-500" :class="fieldError('email') ? 'border-rose-400' : ''" :placeholder="$t('you@example.com')"><span v-if="fieldError('email')" class="mt-1 block text-xs font-semibold text-rose-600 dark:text-rose-400">{{ $t(fieldError('email')) }}</span></label>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ $t('Password') }}<input v-model="form.password" required :type="showPassword ? 'text' : 'password'" :autocomplete="isRegister ? 'new-password' : 'current-password'" class="input-soft mt-2 h-12 w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 text-sm outline-none dark:border-white/10 dark:bg-white/5 dark:text-gray-100 dark:placeholder:text-gray-500" :class="fieldError('password') ? 'border-rose-400' : ''" :placeholder="isRegister ? $t('At least 8 characters') : $t('Your password')"><span v-if="fieldError('password')" class="mt-1 block text-xs font-semibold text-rose-600 dark:text-rose-400">{{ $t(fieldError('password')) }}</span></label>
                <label v-if="isRegister" class="block text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ $t('Confirm password') }}<input v-model="form.password_confirmation" required type="password" autocomplete="new-password" class="input-soft mt-2 h-12 w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 text-sm outline-none dark:border-white/10 dark:bg-white/5 dark:text-gray-100 dark:placeholder:text-gray-500" :placeholder="$t('Repeat your password')"></label>
                <label v-if="!isRegister" class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300"><input v-model="form.remember" type="checkbox" class="rounded border-gray-300 text-forest-700 focus:ring-forest-500 dark:border-white/20 dark:bg-white/5">{{ $t('Remember me') }}</label>
                <p v-if="error" class="rounded-2xl bg-rose-50 p-4 text-sm font-semibold text-rose-700 dark:bg-rose-500/10 dark:text-rose-300">{{ $t(error) }}</p>
                <button type="submit" class="eco-btn eco-btn-primary w-full justify-center" :disabled="loading">{{ loading ? $t('Please wait…') : isRegister ? $t('Create account') : $t('Sign in') }}</button>
            </form>
            <div class="mt-6 flex items-center gap-4" aria-hidden="true">
                <div class="h-px flex-1 bg-gray-200 dark:bg-white/10"></div>
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">{{ $t('or') }}</span>
                <div class="h-px flex-1 bg-gray-200 dark:bg-white/10"></div>
            </div>
            <a :href="googleAuthUrl" class="mt-6 flex h-12 w-full items-center justify-center gap-3 rounded-2xl border border-gray-200 bg-white text-sm font-bold text-gray-700 transition hover:bg-gray-50 dark:border-white/10 dark:bg-white/5 dark:text-gray-200 dark:hover:bg-white/10">
                <svg class="h-5 w-5" viewBox="0 0 24 24" aria-hidden="true"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                {{ $t('Continue with Google') }}
            </a>
            <p class="mt-6 text-center text-sm text-gray-500 dark:text-gray-400">{{ isRegister ? $t('Already have an account?') : $t('New to EcoJourney?') }} <RouterLink :to="switchAuthPath" class="font-bold text-forest-700 hover:text-forest-900 dark:text-forest-300 dark:hover:text-forest-200">{{ isRegister ? $t('Sign in') : $t('Create account') }}</RouterLink></p>
        </div>
    </section>
</template>

<script setup>
import { computed, reactive, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { auth } from '../state/auth';
import { t } from '../i18n';

const props = defineProps({ mode: { type: String, required: true } });
const route = useRoute();
const router = useRouter();
const isRegister = computed(() => props.mode === 'register');
const reason = computed(() => (route.query.reason === 'favorites' ? 'favorites' : ''));
const switchAuthPath = computed(() => {
    const query = { ...route.query };
    return { path: isRegister.value ? '/login' : '/register', query };
});
const loading = ref(false);
const error = ref('');
const errors = ref({});
const showPassword = ref(false);
const form = reactive({ name: '', email: '', password: '', password_confirmation: '', remember: false });
const oauthError = computed(() => (typeof route.query.oauth_error === 'string' ? route.query.oauth_error : ''));
const googleAuthUrl = computed(() => {
    const redirect = safeRedirect(route.query.redirect);
    return redirect ? `/auth/google?${new URLSearchParams({ redirect })}` : '/auth/google';
});
function safeRedirect(value) {
    if (typeof value !== 'string' || !value.startsWith('/') || value.startsWith('//') || value.startsWith('/\\')) {
        return '';
    }
    return value;
}
function fieldError(field) { return errors.value[field]?.[0] || ''; }

async function submit() {
    loading.value = true;
    error.value = '';
    errors.value = {};
    try {
        if (isRegister.value) {
            const { name, email, password, password_confirmation } = form;
            await auth.register({ name, email, password, password_confirmation });
        } else {
            const { email, password, remember } = form;
            await auth.login({ email, password, remember });
        }
        const redirect = safeRedirect(route.query.redirect) || '/';
        await router.replace(redirect);
    } catch (submitError) {
        error.value = submitError.message || t('Unable to sign in. Please try again.');
        errors.value = submitError.errors || {};
    } finally {
        loading.value = false;
    }
}
</script>
