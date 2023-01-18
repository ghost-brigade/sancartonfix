<script setup>
import { RouterView } from "vue-router";
import MenuComponent from "./components/menu/MenuComponent.vue";
</script>

<template>
    <header class="app-header">
        <img src="./assets/images/logo.png" alt="logo" />
        <img src="./assets/images/logo_text.png" alt="SanCartonFix" />
        <MenuComponent />
    </header>

    <main>
        <RouterView />
    </main>
</template>
