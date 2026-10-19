<template>
    <div>
        <Transition name="chat-panel">
            <section
                v-if="chat.open"
                role="dialog"
                aria-modal="false"
                :aria-label="$t('Eco assistant')"
                class="fixed inset-x-4 bottom-24 z-40 flex max-h-[min(34rem,calc(100vh-7rem))] flex-col overflow-hidden rounded-[28px] border border-gray-200 bg-white shadow-2xl shadow-forest-950/20 sm:inset-x-auto sm:right-6 sm:w-[26rem] dark:border-white/10 dark:bg-[#16211b] dark:shadow-black/50"
                @keydown.esc="close"
            >
                <header class="flex items-center gap-3 border-b border-gray-100 bg-forest-700 px-5 py-4 text-white dark:border-white/10 dark:bg-forest-800">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white/15">
                        <img src="/images/logo-ecojourney.png" alt="EcoJourney" class="h-5 w-auto">
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="truncate font-display text-sm font-extrabold tracking-tight">{{ $t('Eco assistant') }}</p>
                        <p class="truncate text-xs text-white/70">{{ $t('Sustainable travel guide') }}</p>
                    </div>
                    <button v-if="chat.messages.length" type="button" class="cursor-pointer rounded-full px-2 py-1 text-xs font-semibold text-white/80 transition hover:bg-white/10 hover:text-white" @click="reset">
                        {{ $t('Clear') }}
                    </button>
                    <button type="button" class="flex h-8 w-8 cursor-pointer items-center justify-center rounded-full text-white/80 transition hover:bg-white/10 hover:text-white" :aria-label="$t('Close chat')" @click="close">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M6 6l12 12M18 6 6 18" />
                        </svg>
                    </button>
                </header>

                <div ref="scroller" class="flex-1 space-y-3 overflow-y-auto px-5 py-4" aria-live="polite">
                    <p v-if="!chat.messages.length" class="rounded-2xl bg-forest-50 px-4 py-3 text-sm leading-6 text-forest-900 dark:bg-white/5 dark:text-gray-200">
                        {{ $t('Hi! I’m your EcoJourney guide. Ask me for low-impact destinations in Indonesia, or how to plan a lighter-footprint trip.') }}
                    </p>

                    <div
                        v-for="(message, index) in chat.messages"
                        :key="index"
                        class="flex"
                        :class="message.role === 'user' ? 'justify-end' : 'justify-start'"
                    >
                        <p
                            class="max-w-[85%] whitespace-pre-line rounded-2xl px-4 py-2.5 text-sm leading-6"
                            :class="message.role === 'user'
                                ? 'bg-forest-700 text-white dark:bg-forest-600'
                                : 'bg-gray-100 text-gray-800 dark:bg-white/10 dark:text-gray-100'"
                        >{{ message.content }}</p>
                    </div>

                    <div v-if="chat.pending" class="flex justify-start">
                        <span class="rounded-2xl bg-gray-100 px-4 py-2.5 text-sm text-gray-500 dark:bg-white/10 dark:text-gray-300">{{ $t('Thinking…') }}</span>
                    </div>
                </div>

                <p v-if="chat.error" class="border-t border-rose-200 bg-rose-50 px-5 py-2 text-xs font-semibold text-rose-700 dark:border-rose-500/30 dark:bg-rose-500/10 dark:text-rose-300" role="alert">
                    {{ $t(chat.error) }}
                </p>

                <form class="flex items-center gap-2 border-t border-gray-100 p-3 dark:border-white/10" @submit.prevent="submit">
                    <input
                        ref="input"
                        v-model="draft"
                        type="text"
                        :placeholder="$t('Ask about eco-friendly destinations…')"
                        :aria-label="$t('Message')"
                        maxlength="1000"
                        class="input-soft h-11 min-w-0 flex-1 rounded-2xl border border-gray-200 bg-gray-50 px-4 text-sm outline-none dark:border-white/10 dark:bg-white/5 dark:text-gray-100 dark:placeholder:text-gray-500"
                    >
                    <button
                        type="submit"
                        class="flex h-11 w-11 shrink-0 cursor-pointer items-center justify-center rounded-2xl bg-[linear-gradient(180deg,#3f7350_0%,#31583d_55%,#2c5038_100%)] text-white shadow-md transition hover:-translate-y-0.5 disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:translate-y-0"
                        :disabled="chat.pending || !draft.trim()"
                        :aria-label="$t('Send')"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M22 2 11 13" />
                            <path d="M22 2 15 22l-4-9-9-4Z" />
                        </svg>
                    </button>
                </form>
            </section>
        </Transition>

        <button
            type="button"
            class="fixed bottom-6 right-6 z-40 flex h-14 w-14 cursor-pointer items-center justify-center rounded-full bg-[linear-gradient(180deg,#3f7350_0%,#31583d_55%,#2c5038_100%)] text-white shadow-xl shadow-forest-950/30 transition hover:-translate-y-0.5 hover:shadow-2xl"
            :aria-expanded="chat.open"
            :aria-label="chat.open ? $t('Close chat') : $t('Open chat')"
            @click="toggle"
        >
            <svg v-if="!chat.open" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M21 11.5a8.5 8.5 0 0 1-12.3 7.6L3 21l1.9-5.7A8.5 8.5 0 1 1 21 11.5Z" />
            </svg>
            <svg v-else class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M6 6l12 12M18 6 6 18" />
            </svg>
        </button>
    </div>
</template>

<script setup>
import { nextTick, ref, watch } from 'vue';
import { chat, send, toggle, reset } from '../state/chat';

const draft = ref('');
const scroller = ref(null);
const input = ref(null);

function close() {
    chat.open = false;
}

async function submit() {
    const message = draft.value.trim();

    if (!message || chat.pending) {
        return;
    }

    draft.value = '';
    await send(message);
}

watch(() => chat.messages.length, async () => {
    await nextTick();
    if (scroller.value) {
        scroller.value.scrollTop = scroller.value.scrollHeight;
    }
});

watch(() => chat.open, async (open) => {
    if (open) {
        await nextTick();
        input.value?.focus();
    }
});
</script>

<style scoped>
@media (prefers-reduced-motion: no-preference) {
    .chat-panel-enter-active,
    .chat-panel-leave-active {
        transition: opacity 0.25s cubic-bezier(0.22, 1, 0.36, 1), transform 0.25s cubic-bezier(0.22, 1, 0.36, 1);
    }

    .chat-panel-enter-from,
    .chat-panel-leave-to {
        opacity: 0;
        transform: translateY(16px) scale(0.98);
    }
}
</style>
