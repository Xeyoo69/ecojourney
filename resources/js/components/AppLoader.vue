<template>
    <div class="flex min-h-[16rem] items-center justify-center px-5" role="status" aria-live="polite">
        <div class="text-center">
            <span class="mx-auto block h-10 w-10 animate-spin rounded-full border-4 border-forest-100 border-t-forest-700 dark:border-white/10 dark:border-t-forest-400"></span>
            <p class="mt-4 text-sm font-semibold text-gray-500 dark:text-gray-400">{{ $t(label) }}</p>
        </div>
    </div>
</template>

<script setup>
defineProps({
    label: {
        type: String,
        default: 'Loading EcoJourney…',
    },
});
</script>
