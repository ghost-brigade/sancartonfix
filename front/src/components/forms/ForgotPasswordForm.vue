<script setup>
import { ref } from 'vue';
import { Users } from "@/api/users";
import {useRoute} from "vue-router";

const message   = ref(null);
const password  = ref(null);
const password_confirm  = ref(null);

const { token } = useRoute().params;

const handleConfirmation = async () => {
    try {
        message.value = null;

        if (
            token === '' || token === undefined ||
            password === '' || password === undefined ||
            password_confirm === '' || password_confirm === undefined
        ) {
            throw new Error('Tous les champs sont obligatoires');
        }
        if (password.value !== password_confirm.value) {
            throw new Error('Les mots de passe ne correspondent pas');
        }
        if (password.length < 8) {
            throw new Error('Le mot de passe doit contenir au moins 8 caractères');
        }
        if (!/[A-Z]/.test(password)) {
            throw new Error('Le mot de passe doit contenir au moins une majuscule');
        }
        if (!/[a-z]/.test(password)) {
            throw new Error('Le mot de passe doit contenir au moins une minuscule');
        }

        const users = new Users();
        const response = await users.resetPassword(token, {password: password.value}).catch((error) => {
            message.value = error.message;
        });

        if (response.status === 204) {
            return router.push('/login');
        }

        if(response["@type"] === 'hydra:Error') {
            throw new Error('Le lien de réinitialisation de mot de passe est invalide ou a expiré');

        }

        const data = await response.json();
        message.value = data.messages;
    } catch (err) {
        message.value = err.message;
        Swal.fire({ title: "Erreur", text: err.message, icon: "error" });
    }
};
</script>

<template>
    <form @submit.prevent="handleConfirmation">
        <h2>Réinitialiser mon mot de passe</h2>

        <div v-if="message">
            {{ message }}
        </div>

        <div class="app-form_row">
            <label for="password">Nouveau mot de passe</label>
            <input type="password" v-model="password" />
        </div>
        <div class="app-form_row">
            <label for="password_confirmation">Confirmer le mot de passe</label>
            <input type="password" v-model="password_confirm" />
        </div>
        <button type="submit">Réinitialiser mon mot de passe</button>
    </form>
</template>
