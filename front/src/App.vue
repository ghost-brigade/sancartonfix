<script setup>
import { RouterLink, RouterView } from "vue-router";
import Menu from "./components/menu/Menu.vue";
</script>

<template>
    <header class="app-header">
        <img src="./assets/images/logo.png" alt="logo" />
        <img src="./assets/images/logo_text.png" alt="SanCartonFix" />
        <Menu />
    </header>

    <main>
        <RouterView />
    </main>
</template>
