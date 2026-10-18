import { reactive } from 'vue';

const STORAGE_KEY = 'ecojourney_theme';
const systemDark = window.matchMedia('(prefers-color-scheme: dark)');

function readStored() {
    try {
        const value = localStorage.getItem(STORAGE_KEY);
        return value === 'dark' || value === 'light' ? value : null;
    } catch {
        return null;
    }
}

function systemPreference() {
    return systemDark.matches ? 'dark' : 'light';
}

function apply(mode) {
    document.documentElement.classList.toggle('dark', mode === 'dark');
}

function syncThemeColor() {
    const meta = document.querySelector('meta[name="theme-color"]');
    if (meta) {
        meta.setAttribute('content', theme.mode === 'dark' ? '#0f1713' : '#31583d');
    }
}

export const theme = reactive({
    mode: readStored() || systemPreference(),
});

apply(theme.mode);
syncThemeColor();

export function toggle() {
    theme.mode = theme.mode === 'dark' ? 'light' : 'dark';

    try {
        localStorage.setItem(STORAGE_KEY, theme.mode);
    } catch {
        /* storage unavailable — theme still applies for this session */
    }

    apply(theme.mode);
    syncThemeColor();
}

systemDark.addEventListener('change', (event) => {
    if (readStored()) return;

    theme.mode = event.matches ? 'dark' : 'light';
    apply(theme.mode);
    syncThemeColor();
});

export default { theme, toggle };
