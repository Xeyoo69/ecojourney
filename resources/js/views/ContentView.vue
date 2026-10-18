<template>
    <div>
        <section class="relative overflow-hidden bg-forest-900 py-16 text-white sm:py-20"><div class="pointer-events-none absolute -right-24 -top-24 h-72 w-72 rounded-full bg-white/10 blur-3xl"></div><div class="pointer-events-none absolute -bottom-24 -left-24 h-72 w-72 rounded-full bg-forest-700/40 blur-3xl"></div><div class="container-eco relative"><div class="max-w-3xl"><div class="section-kicker !text-emerald-300">{{ $t(content.eyebrow) }}</div><h1 class="mt-4 font-display text-4xl font-extrabold tracking-tight sm:text-5xl">{{ $t(content.heading) }}</h1><p class="mt-5 max-w-2xl text-base leading-7 text-white/60">{{ $t(content.description) }}</p></div></div></section>
        <section class="container-eco py-16 sm:py-20">
            <template v-if="page === 'about'"><div class="grid gap-8 lg:grid-cols-2"><article class="team-card rounded-[28px] border border-gray-100 bg-white p-8 shadow-xl shadow-forest-900/5 dark:border-white/10 dark:bg-[#16211b] dark:shadow-black/40"><h2 class="font-display text-2xl font-extrabold text-gray-900 dark:text-gray-100">{{ $t('Our mission') }}</h2><p class="mt-3 text-sm leading-7 text-gray-500 dark:text-gray-400">{{ $t('Make sustainable travel simple and inspiring. We curate destinations, surface eco scores, and help build itineraries that respect places and people.') }}</p><div class="mt-6 grid grid-cols-3 gap-4 text-center"><div v-for="stat in aboutStats" :key="stat.label" class="rounded-2xl bg-forest-50 p-4 dark:bg-white/5"><div class="font-display text-xl font-extrabold text-forest-900 dark:text-forest-100">{{ stat.value }}</div><div class="text-xs text-gray-500 dark:text-gray-400">{{ $t(stat.label) }}</div></div></div></article><article class="team-card rounded-[28px] bg-sand-100 p-8 dark:bg-[#1e2c24]"><h2 class="font-display text-xl font-bold text-forest-900 dark:text-forest-100">{{ $t('What we value') }}</h2><ul class="mt-5 space-y-4 text-sm leading-6 text-gray-600 dark:text-gray-300"><li v-for="item in values" :key="item" class="flex gap-3"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-white text-forest-700 shadow-sm dark:bg-[#16211b] dark:text-forest-300"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg></span>{{ $t(item) }}</li></ul><RouterLink to="/planner" class="eco-btn eco-btn-primary mt-7">{{ $t('Plan my journey') }} <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 6l6 6-6 6" /></svg></RouterLink></article></div></template>
            <template v-else-if="page === 'sustainability'"><div class="grid gap-6 md:grid-cols-3"><article v-for="item in principles" :key="item.title" class="card-soft p-7 transition hover:-translate-y-1"><div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-forest-50 text-forest-700 dark:bg-white/5 dark:text-forest-300"><svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path v-for="p in item.icon" :key="p" stroke-linecap="round" stroke-linejoin="round" :d="p" /></svg></div><h2 class="mt-5 font-display text-lg font-bold text-gray-900 dark:text-gray-100">{{ $t(item.title) }}</h2><p class="mt-2 text-sm leading-6 text-gray-500 dark:text-gray-400">{{ $t(item.description) }}</p></article></div><div class="mt-10 grid gap-6 lg:grid-cols-[1.2fr_0.8fr]"><article class="rounded-[28px] border border-gray-100 bg-white p-8 shadow-xl shadow-forest-900/5 dark:border-white/10 dark:bg-[#16211b] dark:shadow-black/40"><h2 class="font-display text-2xl font-extrabold text-gray-900 dark:text-gray-100">{{ $t('How EcoJourney scores destinations') }}</h2><p class="mt-3 text-sm leading-6 text-gray-500 dark:text-gray-400">{{ $t('Each destination has sustainability score 0-100 based on conservation, community benefit, and visitor management.') }}</p><div class="mt-6 space-y-4"><div v-for="score in scoreBands" :key="score.label" class="flex items-center gap-4"><div class="h-2 flex-1 rounded-full bg-gray-100 dark:bg-white/10"><div class="h-2 rounded-full" :class="score.color" :style="{ width: `${score.percent}%` }"></div></div><span class="w-32 text-sm font-bold text-gray-600 dark:text-gray-300">{{ $t(score.label) }}</span></div></div></article><article class="rounded-[28px] bg-forest-900 p-8 text-white"><h2 class="font-display text-xl font-bold">{{ $t('Quick checklist') }}</h2><ul class="mt-5 space-y-3 text-sm leading-6 text-white/70"><li v-for="item in checklist" :key="item" class="flex gap-3"><svg class="mt-1 h-4 w-4 shrink-0 text-emerald-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>{{ $t(item) }}</li></ul><RouterLink to="/explore" class="eco-btn eco-btn-light mt-7 w-full justify-center">{{ $t('Explore destinations') }}</RouterLink></article></div></template>
            <template v-else><div class="grid gap-8 lg:grid-cols-[1.1fr_0.9fr]"><form class="rounded-[28px] border border-gray-100 bg-white p-6 shadow-xl shadow-forest-900/5 sm:p-8 dark:border-white/10 dark:bg-[#16211b] dark:shadow-black/40" @submit.prevent="sendContact"><div class="grid gap-4 sm:grid-cols-2"><label class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ $t('Name') }}<input v-model.trim="contact.name" required class="input-soft mt-2 h-12 w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 text-sm outline-none dark:border-white/10 dark:bg-white/5" :placeholder="$t('Your name')"></label><label class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ $t('Email') }}<input v-model.trim="contact.email" required type="email" class="input-soft mt-2 h-12 w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 text-sm outline-none dark:border-white/10 dark:bg-white/5" :placeholder="$t('you@example.com')"></label></div><label class="mt-4 block text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ $t('Message') }}<textarea v-model.trim="contact.message" required rows="5" class="input-soft mt-2 w-full rounded-2xl border border-gray-200 bg-gray-50 p-4 text-sm outline-none dark:border-white/10 dark:bg-white/5" :placeholder="$t('How can we help?')"></textarea></label><button type="submit" class="eco-btn eco-btn-primary mt-6 w-full justify-center">{{ $t('Send message') }}</button><p v-if="sent" class="mt-4 rounded-xl bg-forest-50 p-3 text-sm font-semibold text-forest-700 dark:bg-white/5 dark:text-forest-300">{{ $t('Thanks! This is a demo — no message was sent.') }}</p></form><aside class="rounded-[28px] bg-forest-900 p-8 text-white"><h2 class="font-display text-xl font-bold">{{ $t('Contact info') }}</h2><div class="mt-6 space-y-4 text-sm leading-6 text-white/70"><div><div class="text-xs font-bold uppercase tracking-wider text-white/60">{{ $t('Email') }}</div><div class="mt-1 font-semibold text-white">hello@ecojourney.example</div></div><div><div class="text-xs font-bold uppercase tracking-wider text-white/60">{{ $t('Location') }}</div><div class="mt-1">{{ $t('Indonesia — remote friendly') }}</div></div><div><div class="text-xs font-bold uppercase tracking-wider text-white/60">{{ $t('Hours') }}</div><div class="mt-1">{{ $t('Mon–Fri, 09:00–17:00 WIB') }}</div></div></div><div class="mt-8 rounded-2xl border border-white/10 bg-white/5 p-4 text-xs leading-6 text-white/60">{{ $t('This site is a portfolio concept. No real messages are sent.') }}</div></aside></div></template>
        </section>
    </div>
</template>

<script setup>
import { computed, onMounted, reactive, ref } from 'vue';
import api from '../api';

const props = defineProps({ page: { type: String, required: true } });
const sent = ref(false);
const contact = reactive({ name: '', email: '', message: '' });
const pageContent = {
    about: { eyebrow: 'About EcoJourney', heading: 'Travel better, together.', description: 'EcoJourney is a concept platform for discovering meaningful destinations and planning lighter-footprint journeys.' },
    sustainability: { eyebrow: 'Sustainability', heading: 'Travel with purpose.', description: 'Small choices create meaningful impact. EcoJourney helps you travel lighter, support local communities, and protect natural places.' },
    contact: { eyebrow: 'Contact', heading: "We'd love to hear from you.", description: 'Questions, feedback, or partnership ideas? Send a message — this is a demo form.' },
};
const content = computed(() => pageContent[props.page] || pageContent.about);
const aboutStats = ref([
    { value: '—', label: 'Destinations' },
    { value: '—', label: 'Categories' },
    { value: '—', label: 'Regions' },
]);
const values = ['Curated, not crowded — quality over quantity', 'Transparency via sustainability indicators', 'Simple planning, thoughtful travel'];
const principles = [
    { icon: ['M12 21c4.5-2.2 7-5.6 7-10.5C19 6 15.8 3 12 3S5 6 5 10.5C5 15.4 7.5 18.8 12 21Z', 'M12 21V9', 'M12 14c-2.2-1.4-3.7-3.1-4.5-5', 'M12 12c1.8-1.2 3.3-2.7 4.2-4.5'], title: 'Protect nature', description: 'Stay on trails, respect wildlife, leave no trace, and choose low-impact activities.' },
    { icon: ['M17 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2', 'M10 3a4 4 0 1 0 0 8 4 4 0 0 0 0-8z', 'M21 21v-2a4 4 0 0 0-3-3.87', 'M15 3.13a4 4 0 0 1 0 7.75'], title: 'Support local', description: 'Eat local, buy local, hire local guides. Your spending empowers communities.' },
    { icon: ['M3 12a9 9 0 0 1 15.3-6.4L21 8', 'M21 3v5h-5', 'M21 12a9 9 0 0 1-15.3 6.4L3 16', 'M3 21v-5h5'], title: 'Reduce waste', description: 'Bring reusable bottle and bag. Refuse single-use plastic. Dispose responsibly.' },
];
const scoreBands = [{ percent: 85, label: '85+ Excellent', color: 'bg-forest-600' }, { percent: 65, label: '60-84 Good', color: 'bg-forest-400' }, { percent: 40, label: '0-59 Needs care', color: 'bg-amber-400' }];
const checklist = ['Book direct with local operators', 'Respect cultural norms and ask before photos', 'Offset transport when possible', 'Share feedback to help improve'];

function sendContact() {
    sent.value = true;
    contact.name = '';
    contact.email = '';
    contact.message = '';
}

onMounted(async () => {
    if (props.page !== 'about') return;
    try {
        const payload = await api.home();
        const stats = payload?.data?.stats || payload?.stats || {};
        aboutStats.value = [
            { value: stats.destinations ?? '—', label: 'Destinations' },
            { value: stats.categories ?? '—', label: 'Categories' },
            { value: stats.regions ?? '—', label: 'Regions' },
        ];
    } catch (statsError) {
        // Keep placeholder values when stats are unavailable.
    }
});
</script>
