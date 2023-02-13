<script setup>
import { useRouter } from 'vue-router';
import { reactive, ref } from 'vue';
import {Users} from "@/api/users";
const router = useRouter();

const loading = ref(false);
const message = ref(null);
const formData = reactive({
    email: '',
    plainPassword: '',
    firstname: '',
    lastname: ''
});

const verify = ({ email, plainPassword, firstname, lastname }) => {
    if (!email.includes('@')) {
        message.value = 'Email invalide';
        return false;
    }
    if (plainPassword.length < 8) {
        throw new Error('Le mot de passe doit contenir au moins 8 caractères');
    }
    if (!/[A-Z]/.test(plainPassword)) {
        throw new Error('Le mot de passe doit contenir au moins une majuscule');
    }
    if (!/[a-z]/.test(plainPassword)) {
        throw new Error('Le mot de passe doit contenir au moins une minuscule');
    }
    if (firstname.length < 2) {
        throw new Error('Le prénom doit contenir au moins 2 caractères');
    }
    if (/[0-9]/.test(firstname)) {
        throw new Error('Le prénom ne doit pas contenir de chiffre');
    }
    if (lastname.length < 2) {
        throw new Error('Le nom doit contenir au moins 2 caractères');
    }
    if (/[0-9]/.test(lastname)) {
        throw new Error("Le nom ne doit pas contenir de chiffre");
    }
}

const handleRegistration = async () => {
    try {
        const user = new Users();
        verify(formData);
        const response = await user.create(formData);

        if (response.status === 201) {
            router.push("/login");
        }
    } catch (err) {
        message.value = err.message;
    }
}
</script>

<template>
    <form @submit.prevent="handleRegistration">
        <h2>M'inscrire</h2>

        <div v-if="message">
            {{ message }}
        </div>

        <br>

        <div class="app-form_row">
            <input v-model="formData.email" type="email" placeholder="Email" />
        </div>

        <div class="app-form_row">
            <input
                v-model="formData.plainPassword"
                type="password"
                placeholder="Mot de passe"
            />
        </div>

        <div class="app-form_row">
            <input
                v-model="formData.firstname"
                type="text"
                placeholder="Prénom"
            />
        </div>
        <div class="app-form_row">
            <input
                v-model="formData.lastname"
                type="text"
                placeholder="Nom"
            />
        </div>

        <div class="app-form_row">
            <router-link to="/login" style="text-decoration: underline; font-size: 0.8rem">Me connecter</router-link>
        </div>

        <button type="submit" :disabled="loading">
            {{ loading ? "..." : "M'inscrire" }}
        </button>
    </form>
</template>

