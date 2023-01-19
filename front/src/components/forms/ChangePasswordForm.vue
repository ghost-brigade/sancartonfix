<script setup>
import { ref, onMounted, inject } from 'vue';
import { Security } from "@/api/security";
import { SECURITY_currentUser } from "@/providers/ProviderKeys";
import { useRouter } from 'vue-router';

const password = ref('');
const passwordCheck = ref('');
const loading = ref(false);

const { setCurrentUser } = inject(SECURITY_currentUser);
const security = new Security();

const router = useRouter();

const submit = async () => {
    if (loading.value) {
        return;
    }
}

</script>

<template>
    <form @submit.prevent="submit">
        <h2>Changer de mot de passe</h2>

        <div class="app-form_row">
            <input v-model="password" type="password" placeholder="Mot de passe">
        </div>
        
        <div class="app-form_row">
            <input v-model="passwordCheck" type="password" placeholder="Vérifier le mot de passe">
        </div>

        <button type="submit" :disabled="loading">
            {{ loading ? '...' : 'Connexion'}}
        </button>
    </form>
</template>