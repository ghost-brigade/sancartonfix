<script setup>
import { RouterView } from "vue-router";
import MenuHeader from "./components/menu/MenuHeader.vue";
import SecurityProvider from "./providers/SecurityProvider.vue";
</script>

<template>
    <SecurityProvider>
        <header class="app-header">
            <img src="./assets/images/logo.png" alt="logo" />
            <img src="./assets/images/logo_text.png" alt="SanCartonFix" />
            <MenuHeader />
        </header>

        <main>
            <RouterView />
        </main>
    </SecurityProvider>
</template>
