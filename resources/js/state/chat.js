import { reactive } from 'vue';
import { api } from '../api';
import { state as i18n } from '../i18n';

const MAX_HISTORY = 8;

export const chat = reactive({
    open: false,
    pending: false,
    error: null,
    messages: [],
});

export function toggle() {
    chat.open = !chat.open;

    if (chat.open) {
        chat.error = null;
    }
}

export function reset() {
    chat.messages = [];
    chat.error = null;
}

export async function send(text) {
    const message = (text || '').trim();

    if (!message || chat.pending) {
        return;
    }

    chat.error = null;
    chat.messages.push({ role: 'user', content: message });
    chat.pending = true;

    const history = chat.messages
        .slice(-MAX_HISTORY - 1, -1)
        .map(({ role, content }) => ({ role, content }));

    try {
        const payload = await api.sendChat({ message, history, locale: i18n.locale });
        const reply = payload?.data?.reply || payload?.reply || '';
        chat.messages.push({ role: 'assistant', content: reply });
    } catch (error) {
        chat.error = error?.message || 'Unable to reach the assistant. Please try again.';
    } finally {
        chat.pending = false;
    }
}

export default { chat, toggle, send, reset };
