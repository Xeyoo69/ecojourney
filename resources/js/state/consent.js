import { reactive } from 'vue';
import { getCookie, setCookie, deleteCookie } from '../utils/cookies';

const COOKIE_NAME = 'ecojourney_consent';
const COOKIE_DAYS = 180; 

function readConsent() {
    const value = getCookie(COOKIE_NAME);
    return value === 'accepted' || value === 'rejected' ? value : null;
}

export const consent = reactive({
    value: readConsent(),
    visible: readConsent() === null,
});

export function accept() {
    consent.value = 'accepted';
    consent.visible = false;
    setCookie(COOKIE_NAME, 'accepted', { days: COOKIE_DAYS });
}

export function reject() {
    consent.value = 'rejected';
    consent.visible = false;
    setCookie(COOKIE_NAME, 'rejected', { days: COOKIE_DAYS });
}

export function reset() {
    deleteCookie(COOKIE_NAME);
    consent.value = null;
    consent.visible = true;
}

export function hasAnalyticsConsent() {
    return consent.value === 'accepted';
}

export default { consent, accept, reject, reset, hasAnalyticsConsent };
