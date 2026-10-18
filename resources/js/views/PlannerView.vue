<template>
    <div>
        <section class="relative overflow-hidden bg-forest-900 py-16 text-white sm:py-20"><div class="pointer-events-none absolute -right-24 -top-24 h-72 w-72 rounded-full bg-white/10 blur-3xl"></div><div class="container-eco relative"><div class="max-w-3xl"><div class="section-kicker !text-emerald-300">{{ $t('Smart trip planner') }}</div><h1 class="mt-4 font-display text-4xl font-extrabold tracking-tight sm:text-5xl">{{ $t('Build a journey that fits you.') }}</h1><p class="mt-5 max-w-2xl text-base leading-7 text-white/60">{{ $t('Tell us how you want to travel. EcoJourney creates itinerary based on your preference and sustainability score.') }}</p></div></div></section>
        <section class="container-eco -mt-10 pb-24"><div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_360px]">
            <form class="rounded-[32px] border border-gray-100 bg-white p-6 shadow-xl shadow-forest-900/5 sm:p-10 dark:border-white/10 dark:bg-[#16211b] dark:shadow-black/40" @submit.prevent="generate">
                <div class="section-kicker">{{ $t('Step 01') }}</div><h2 class="mt-2 font-display text-2xl font-extrabold text-gray-900 dark:text-gray-100">{{ $t('How long is your journey?') }}</h2><p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ $t('Choose how many days you want to explore.') }}</p>
                <div class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-4"><label v-for="day in [1, 2, 3, 4]" :key="day" class="cursor-pointer"><input v-model.number="form.duration" type="radio" :value="day" class="peer sr-only"><div class="rounded-2xl border border-gray-200 p-4 text-center transition hover:border-forest-300 peer-checked:border-forest-700 peer-checked:bg-forest-50 peer-checked:shadow-md peer-checked:shadow-forest-900/10 peer-focus-visible:ring-2 peer-focus-visible:ring-forest-500 peer-focus-visible:ring-offset-2 dark:border-white/15 dark:peer-checked:border-forest-400 dark:peer-checked:bg-white/5 dark:peer-focus-visible:ring-offset-[#16211b]"><div class="font-display text-xl font-bold text-gray-900 dark:text-gray-100">{{ day }}</div><div class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ day === 1 ? $t('Day') : $t('Days') }}</div></div></label></div>
                <div class="mt-12 border-t border-gray-100 pt-10 dark:border-white/10"><div class="section-kicker">{{ $t('Step 02') }}</div><h2 class="mt-2 font-display text-2xl font-extrabold text-gray-900 dark:text-gray-100">{{ $t('What kind of experience?') }}</h2><p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ $t('Choose experience you want to prioritize.') }}</p>
                    <div class="mt-6 grid gap-4 sm:grid-cols-2"><label v-for="option in preferences" :key="option.value" class="cursor-pointer"><input v-model="form.preference" type="radio" :value="option.value" class="peer sr-only"><div class="rounded-3xl border border-gray-200 p-5 transition hover:border-forest-300 peer-checked:border-forest-700 peer-checked:bg-forest-50 peer-checked:shadow-md peer-checked:shadow-forest-900/10 peer-focus-visible:ring-2 peer-focus-visible:ring-forest-500 peer-focus-visible:ring-offset-2 dark:border-white/15 dark:peer-checked:border-forest-400 dark:peer-checked:bg-white/5 dark:peer-focus-visible:ring-offset-[#16211b]"><div class="font-display font-bold text-gray-900 dark:text-gray-100">{{ $t(option.label) }}</div><p class="mt-2 text-xs leading-5 text-gray-500 dark:text-gray-400">{{ $t(option.description) }}</p></div></label></div>
                </div>
                <p v-if="error" role="alert" class="mt-8 rounded-2xl border border-rose-100 bg-rose-50 p-4 text-sm font-semibold text-rose-700 dark:border-rose-500/25 dark:bg-rose-500/10 dark:text-rose-300">{{ $t(error) }}</p><button type="submit" class="eco-btn eco-btn-primary mt-8 w-full sm:w-auto" :disabled="loading">{{ loading ? $t('Creating journey…') : $t('Generate my journey') }}</button>
            </form>
            <aside class="rounded-[32px] bg-sand-100 p-7 sm:p-8 dark:bg-[#1e2c24]"><div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-forest-700 shadow-sm dark:bg-[#16211b] dark:text-forest-300"><svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3l1.8 5.2L19 10l-5.2 1.8L12 17l-1.8-5.2L5 10l5.2-1.8L12 3Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19 15l.9 2.6L22.5 18.5l-2.6.9L19 22l-.9-2.6-2.6-.9 2.6-.9L19 15Z" /></svg></div><h2 class="mt-6 font-display text-2xl font-extrabold text-forest-900 dark:text-forest-100">{{ $t('Designed around you.') }}</h2><p class="mt-3 text-sm leading-7 text-gray-600 dark:text-gray-300">{{ $t('We prioritize high sustainability scores and match destinations to your preferred experience.') }}</p><ul class="mt-6 space-y-3 text-sm text-gray-600 dark:text-gray-300"><li v-for="item in ['Personalized daily route', 'Eco score for every stop', 'Local activities when available']" :key="item" class="flex gap-3"><span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-forest-100 text-forest-700 dark:bg-forest-800 dark:text-forest-300"><svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg></span>{{ $t(item) }}</li></ul></aside>
        </div></section>
    </div>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import api from '../api';
import { t } from '../i18n';

const router = useRouter();
const form = reactive({ duration: 2, preference: 'nature' });
const loading = ref(false);
const error = ref('');
const preferences = [
    { value: 'nature', label: 'Nature', description: 'Forests, wildlife, landscapes, and natural experiences.' },
    { value: 'culture', label: 'Culture', description: 'Heritage, communities, food, art, and local stories.' },
    { value: 'adventure', label: 'Adventure', description: 'Active experiences with a lighter footprint.' },
    { value: 'eco-tourism', label: 'Eco tourism', description: 'Conservation-focused and community-led travel.' },
];

async function generate() {
    loading.value = true;
    error.value = '';
    try {
        const payload = await api.generatePlanner(form);
        const itinerary = payload?.data || payload;
        if (!itinerary?.id) throw new Error(t('Journey could not be created.'));
        router.push({ name: 'planner-result', params: { id: itinerary.id } });
    } catch (generateError) {
        error.value = generateError.message || t('Journey could not be created.');
    } finally {
        loading.value = false;
    }
}
</script>
