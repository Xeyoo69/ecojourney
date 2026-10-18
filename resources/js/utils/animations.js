import { onUnmounted, ref, unref, watch } from 'vue';

const prefersReducedMotion = !import.meta.env.SSR && typeof window !== 'undefined'
    ? window.matchMedia('(prefers-reduced-motion: reduce)')
    : null;

export function motionReduced() {
    return prefersReducedMotion ? prefersReducedMotion.matches : false;
}

export function useScrollReveal(options = {}) {
    const { threshold = 0.12, rootMargin = '0px 0px -60px 0px', delay = 0, once = true } = options;
    const elementRef = ref(null);
    const isVisible = ref(false);
    let observer = null;
    let timer = null;

    function observe(element) {
        if (!element) return;
        if (motionReduced() || typeof IntersectionObserver === 'undefined') {
            isVisible.value = true;
            return;
        }

        if (observer) observer.disconnect();
        observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    timer = setTimeout(() => {
                        isVisible.value = true;
                    }, delay);
                    if (once) observer.unobserve(entry.target);
                } else if (!once) {
                    isVisible.value = false;
                }
            });
        }, { threshold, rootMargin });

        observer.observe(element);
    }

    watch(elementRef, (element) => {
        if (element) observe(element);
    }, { flush: 'post' });

    onUnmounted(() => {
        if (timer) clearTimeout(timer);
        if (observer) observer.disconnect();
    });

    return { elementRef, isVisible };
}

export function useStaggeredReveal(options = {}) {
    const { step = 80, threshold = 0, rootMargin = '0px 0px -60px 0px' } = options;
    const containerRef = ref(null);
    const visible = ref(false);
    let observer = null;

    function checkInitialVisibility(element) {
        if (!element) return false;
        const rect = element.getBoundingClientRect();
        const viewportHeight = window.innerHeight || document.documentElement.clientHeight;
        return rect.top < viewportHeight && rect.bottom > 0;
    }

    function observe(element) {
        if (!element) return;
        if (motionReduced() || typeof IntersectionObserver === 'undefined') {
            visible.value = true;
            return;
        }

        if (checkInitialVisibility(element)) {
            visible.value = true;
            return;
        }

        if (observer) observer.disconnect();
        observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    visible.value = true;
                    observer.disconnect();
                }
            });
        }, { threshold, rootMargin });

        observer.observe(element);
    }

    watch(containerRef, (element) => {
        if (element) observe(element);
    }, { flush: 'post' });

    onUnmounted(() => {
        if (observer) observer.disconnect();
    });

    const itemStyle = (index) => ({
        transitionDelay: visible.value ? `${Math.min(index, 12) * step}ms` : '0ms',
    });

    return { containerRef, visible, itemStyle };
}

export function useAnimatedNumber(target, options = {}) {
    const { duration = 1200 } = options;
    const display = ref(0);
    const elementRef = ref(null);
    let frame = null;

    function run(value) {
        const end = Number(value) || 0;
        if (frame) cancelAnimationFrame(frame);
        if (motionReduced() || end === 0) {
            display.value = end;
            return;
        }

        const start = performance.now();
        const tick = (now) => {
            const progress = Math.min((now - start) / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3);
            display.value = Math.round(end * eased);
            if (progress < 1) frame = requestAnimationFrame(tick);
        };
        frame = requestAnimationFrame(tick);
    }

    function observe(element) {
        if (!element) return;
        if (typeof IntersectionObserver === 'undefined') {
            run(target.value);
            return;
        }

        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    run(target.value);
                    observer.disconnect();
                }
            });
        }, { threshold: 0.3 });

        observer.observe(element);
    }

    watch(elementRef, (element) => {
        if (element) observe(element);
    }, { flush: 'post' });

    watch(() => unref(target), (value) => run(value));

    onUnmounted(() => {
        if (frame) cancelAnimationFrame(frame);
    });

    return { display, elementRef };
}
