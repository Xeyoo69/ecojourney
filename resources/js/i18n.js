import { reactive } from 'vue';
import en from '../../lang/en.json';
import id from '../../lang/id.json';

const catalogs = { en, id };
const STORAGE_KEY = 'ecojourney_locale';
const DEFAULT_LOCALE = 'id';

function resolveLocale(value) {
    return value && catalogs[value] ? value : DEFAULT_LOCALE;
}

export const state = reactive({
    locale: resolveLocale(localStorage.getItem(STORAGE_KEY)),
});

export function setLocale(locale) {
    const next = resolveLocale(locale);
    state.locale = next;
    localStorage.setItem(STORAGE_KEY, next);
    document.documentElement.lang = next;
}

export function t(key, params = {}) {
    if (key === null || key === undefined || key === '') return '';

    const catalog = catalogs[state.locale] || {};
    const template = catalog[key] ?? catalogs.en[key] ?? key;

    if (typeof template !== 'string' || !params) return template;

    return template.replace(/\{(\w+)\}/g, (match, name) => (
        params[name] !== undefined ? String(params[name]) : match
    ));
}

export function applyLocaleToDocument() {
    document.documentElement.lang = state.locale;
}

export default { state, t, setLocale, applyLocaleToDocument };
