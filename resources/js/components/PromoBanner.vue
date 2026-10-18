<template>
    <Transition name="promo">
        <div v-if="visible" class="promo-strip relative overflow-hidden rounded-[28px] bg-forest-900 ring-1 ring-white/10 shadow-xl shadow-forest-950/25">
            <div class="pointer-events-none absolute -right-16 -top-24 h-64 w-64 rounded-full bg-forest-600/25 blur-3xl"></div>
            <div class="pointer-events-none absolute -bottom-24 right-1/3 h-52 w-52 rounded-full bg-forest-500/15 blur-3xl"></div>

            <div class="relative flex flex-col gap-5 p-6 sm:flex-row sm:items-center sm:gap-6 sm:p-7">
                <div class="flex h-13 w-13 shrink-0 items-center justify-center rounded-2xl bg-white/10 ring-1 ring-white/15 sm:h-14 sm:w-14">
                    <svg v-if="icon === 'map'" class="h-6 w-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M9 4 3 6.5v13L9 17l6 2.5 6-2.5v-13L15 6.5 9 4Z"/>
                        <path d="M9 4v13"/><path d="M15 6.5v13"/>
                    </svg>
                    <svg v-else-if="icon === 'sparkle'" class="h-6 w-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M12 3l1.8 5.2L19 10l-5.2 1.8L12 17l-1.8-5.2L5 10l5.2-1.8L12 3Z"/>
                        <path d="M19 16.5l.9 2.6 2.6.9-2.6.9-.9 2.6-.9-2.6-2.6-.9 2.6-.9.9-2.6Z"/>
                    </svg>
                    <svg v-else class="h-6 w-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M12 21c4.5-2.2 7-5.6 7-10.5C19 6 15.8 3 12 3S5 6 5 10.5C5 15.4 7.5 18.8 12 21Z"/>
                        <path d="M12 21V9"/>
                        <path d="M12 14c-2.2-1.4-3.7-3.1-4.5-5"/>
                        <path d="M12 12c1.8-1.2 3.3-2.7 4.2-4.5"/>
                    </svg>
                </div>

                <div class="min-w-0 flex-1">
                    <div v-if="kicker" class="text-[11px] font-bold uppercase tracking-[0.14em] text-forest-200">{{ kicker }}</div>
                    <h3 class="mt-1.5 font-display text-lg font-extrabold tracking-[-0.02em] text-white sm:text-xl">{{ title }}</h3>
                    <p v-if="description" class="mt-1.5 text-sm leading-6 text-forest-100/85">{{ description }}</p>
                </div>

                <div class="flex shrink-0 items-center gap-2">
                    <RouterLink :to="to" class="eco-btn eco-btn-light text-sm">
                        {{ $t(ctaLabel) }}
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M5 12h14"/><path d="m13 6 6 6-6 6"/>
                        </svg>
                    </RouterLink>
                    <button v-if="dismissible" type="button" class="rounded-full p-2 text-forest-200 transition-colors hover:bg-white/10 hover:text-white" :aria-label="$t('Dismiss announcement')" @click="dismiss">
                        <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M6 6l12 12M18 6 6 18"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </Transition>
</template>

<script setup>
import { onMounted, ref } from 'vue';

const props = defineProps({
    kicker: { type: String, default: '' },
    title: { type: String, required: true },
    description: { type: String, default: '' },
    ctaLabel: { type: String, default: 'Learn more' },
    to: { type: String, default: '/national' },
    icon: { type: String, default: 'map' },
    dismissible: { type: Boolean, default: true },
    storageKey: { type: String, default: 'ecojourney:promo' },
    ttlDays: { type: Number, default: 30 },
});

const emit = defineEmits(['dismiss']);
const visible = ref(true);

onMounted(() => {
    if (!props.dismissible) return;
    try {
        const dismissedAt = Number(localStorage.getItem(props.storageKey));
        if (dismissedAt && Date.now() - dismissedAt < props.ttlDays * 86400000) {
            visible.value = false;
        }
    } catch {
        /* storage unavailable — keep banner visible */
    }
});

function dismiss() {
    visible.value = false;
    try {
        localStorage.setItem(props.storageKey, String(Date.now()));
    } catch {
        /* storage unavailable — still hide for this session */
    }
    emit('dismiss');
}
</script>

<style scoped>
@media (prefers-reduced-motion: no-preference) {
    .promo-enter-active,
    .promo-leave-active {
        transition: opacity 0.35s ease, transform 0.35s ease;
    }

    .promo-enter-from,
    .promo-leave-to {
        opacity: 0;
        transform: translateY(-10px);
    }
}
</style>
