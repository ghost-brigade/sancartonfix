<script setup>
import { RouterView } from "vue-router";
import MenuHeader from "./components/menu/MenuHeader.vue";
import SecurityProvider from "./providers/SecurityProvider.vue";
</script>

<template>
    <SecurityProvider>
        <header class="app-header">
            <a href="/"><img src="./assets/images/logo.png" alt="logo" /></a>
            <a href="/"><img src="./assets/images/logo_text.png" alt="SanCartonFix" /></a>

            <MenuHeader />
        </header>

        <main>
            <RouterView />
        </main>
    </SecurityProvider>
</template>
