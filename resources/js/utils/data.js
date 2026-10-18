export function asArray(value) {
    if (Array.isArray(value)) {
        return value;
    }

    if (!value || typeof value !== 'object') {
        return [];
    }

    const dataCollection = asArray(value.data);
    if (dataCollection.length) {
        return dataCollection;
    }

    for (const key of ['featured_destinations', 'destinations', 'items', 'results', 'categories', 'regions', 'activities']) {
        if (Array.isArray(value[key])) {
            return value[key];
        }

        if (Array.isArray(value[key]?.data)) {
            return value[key].data;
        }
    }

    return [];
}

export function extractCollection(payload, keys = []) {
    if (Array.isArray(payload)) {
        return payload;
    }

    for (const key of keys) {
        const value = payload?.[key];
        const collection = asArray(value);
        if (collection.length) {
            return collection;
        }
    }

    return asArray(payload);
}

export function extractPagination(payload, key = 'destinations') {
    const source = payload?.[key] || payload?.data?.[key] || payload;
    const meta = source?.meta || payload?.meta || source || {};
    const items = asArray(source);

    return {
        items,
        current_page: Number(meta.current_page || meta.currentPage || payload?.current_page || 1),
        last_page: Number(meta.last_page || meta.lastPage || payload?.last_page || 1),
        per_page: Number(meta.per_page || meta.perPage || payload?.per_page || items.length || 12),
        total: Number(meta.total || payload?.total || items.length),
    };
}

export function imageUrl(destination) {
    return destination?.image_url || destination?.imageUrl || destination?.image || 'https://images.unsplash.com/photo-1469474968028-56623f02e42e?auto=format&fit=crop&w=1200&q=80';
}

export function categoryName(destination) {
    return destination?.category?.name || destination?.category_name || destination?.category || 'Eco destination';
}

export function destinationScore(destination) {
    return Number(destination?.sustainability_score ?? destination?.sustainabilityScore ?? destination?.score ?? 0);
}

export function destinationLocation(destination) {
    return destination?.location || destination?.region || 'Indonesia';
}

export function uniqueBy(items, key) {
    const seen = new Set();
    return items.filter((item) => {
        const value = typeof key === 'function' ? key(item) : item?.[key];
        if (!value || seen.has(value)) {
            return false;
        }
        seen.add(value);
        return true;
    });
}

export function cleanQuery(query) {
    return Object.fromEntries(
        Object.entries(query).filter(([, value]) => value !== undefined && value !== null && value !== '')
    );
}
