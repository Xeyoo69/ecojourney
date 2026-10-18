export function getCookie(name) {
    const row = document.cookie
        .split('; ')
        .find((entry) => entry.startsWith(`${name}=`));

    if (!row) return null;

    return decodeURIComponent(row.slice(name.length + 1));
}

export function setCookie(name, value, { days = 180 } = {}) {
    const expires = new Date(Date.now() + days * 86400000).toUTCString();
    const secure = window.location.protocol === 'https:' ? '; Secure' : '';

    document.cookie = `${name}=${encodeURIComponent(value)}; expires=${expires}; path=/; SameSite=Lax${secure}`;
}

export function deleteCookie(name) {
    document.cookie = `${name}=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/; SameSite=Lax`;
}

export default { getCookie, setCookie, deleteCookie };
