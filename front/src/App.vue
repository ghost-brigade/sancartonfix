<script setup>
import { RouterView } from "vue-router";
import MenuHeader from "./components/menu/MenuHeader.vue";
import SecurityProvider from "./providers/SecurityProvider.vue";
</script>

<template>
    <SecurityProvider>
        <header class="app-header">
            <router-link :to="{ name: 'home' }">
                <img src="./assets/images/logo.png" alt="logo" />
            </router-link>
            <router-link :to="{ name: 'home' }">
                <img src="./assets/images/logo_text.png" alt="SanCartonFix" />
            </router-link>
            <MenuHeader />
        </header>

        <main>
            <RouterView />
        </main>
    </SecurityProvider>
</template>
