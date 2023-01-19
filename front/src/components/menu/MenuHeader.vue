<script setup>
import { ref } from 'vue';
import { inject } from 'vue';
import { SECURITY_currentUser } from "../../providers/ProviderKeys";

const { currentUser } = inject(SECURITY_currentUser);

const MENU_opened = ref(false);

const toggleOpened = () => {
    MENU_opened.value = !MENU_opened.value;
}
</script>

<template>
    <FontAwesomeIcon class="app-menu_responsive_icon" icon="bars" @click="toggleOpened"/>
    <div class="app-menu_links" :class="`${MENU_opened ? 'opened' : ''}`">
        <nav>
            <RouterLink to="/">Accueil</RouterLink>
            <RouterLink to="/about">Contact</RouterLink>
            <RouterLink to="/profile">Compte</RouterLink>
        </nav>
    </div>
</template>