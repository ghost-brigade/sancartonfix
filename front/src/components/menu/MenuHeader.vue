<script setup>
import { ref } from "vue";
import { inject } from "vue";
import { SECURITY_currentUser } from "../../providers/ProviderKeys";

const { currentUser } = inject(SECURITY_currentUser);

const MENU_opened = ref(false);

const toggleOpened = () => {
    MENU_opened.value = !MENU_opened.value;
};
const closeMenu = () => {
    MENU_opened.value = false;
};
</script>

<template>
    <FontAwesomeIcon
        class="app-menu_responsive_icon"
        icon="bars"
        @click="toggleOpened"
    />
    <div class="app-menu_links" :class="`${MENU_opened ? 'opened' : ''}`">
        <nav>
            <RouterLink to="/" @click="closeMenu">Accueil</RouterLink>
            <template v-if="currentUser.id">
                <RouterLink :to="{ name: 'profile-renting' }" @click="closeMenu"
                    >Mes locations</RouterLink
                >
                <RouterLink :to="{ name: 'profile-housing' }" @click="closeMenu"
                    >Mes logements</RouterLink
                >
                <RouterLink to="/profile/likes" @click="closeMenu">
                    Mes favoris
                </RouterLink>
            </template>
            <template v-if="currentUser?.roles?.includes('ROLE_ADMIN')">
                <RouterLink :to="{ name: 'reports-admin' }" @click="closeMenu"
                    >Admin
                </RouterLink>
            </template>
            <RouterLink to="/profile" @click="closeMenu">
                {{ currentUser.id ? "Profil" : "Connexion " }}
            </RouterLink>
        </nav>
    </div>
</template>
