<template>
    <div class="min-h-screen">
        <AppHeader />
        <main>
            <RouterView v-slot="{ Component }">
                <Suspense>
                    <Transition name="page" mode="out-in">
                        <component :is="Component" :key="route.path" />
                    </Transition>
                    <template #fallback>
                        <AppLoader />
                    </template>
                </Suspense>
            </RouterView>
        </main>
        <AppFooter />
        <CookieConsent />
        <ChatWidget />
    </div>
</template>

<script setup>
import { useRoute } from 'vue-router';
import AppFooter from './components/AppFooter.vue';
import AppHeader from './components/AppHeader.vue';
import AppLoader from './components/AppLoader.vue';
import CookieConsent from './components/CookieConsent.vue';
import ChatWidget from './components/ChatWidget.vue';

const route = useRoute();
</script>
