<script setup>
import { ref, onMounted, inject } from "vue";
import { SECURITY_currentUser } from "@/providers/ProviderKeys";
import { useRouter } from "vue-router";
import { Users } from "@/api/users";
import Swal from 'sweetalert2'

const password = ref("");
const passwordCheck = ref("");
const loading = ref(false);
const message = ref("");

const { currentUser, setCurrentUser } = inject(SECURITY_currentUser);
const users = new Users();

const router = useRouter();

const reset = () => {
    password.value = "";
    passwordCheck.value = "";
};

const submit = async () => {
    if (password.value !== passwordCheck.value) {
        // message.value = "Les mots de passe ne correspondent pas";
        await Swal.fire({title: "Attention", text: "Les mots de passe ne correspondent pas", icon: "warning"});

        reset();
        return;
    }

    if (password.value?.length < 8) {
        // message.value = "Le mot de passe doit faire au moins 8 caractères";
        await Swal.fire({title: "Attention", text: "Le mot de passe doit faire au moins 8 caractères", icon: "warning"});

        reset();
        return;
    }

    loading.value = true;

    try {
        const response = await users.update(
            currentUser?.id,
            {
                plainPassword: password.value,
            },
            false
        );

        if (response.ok) {
            await Swal.fire({title: "Validation", text: "Le mot de passe a bien été modifié", icon: "success"});
        } else {
            const error = await response.json();
            message.value = error.message;
        }
    } catch (err) {
        await Swal.fire({title: "Erreur", text: "Une erreur est survenue lors de la modification du mot de passe", icon: "error"});
    } finally {
        loading.value = false;
        reset();
    }
};
</script>

<template>
    <form @submit.prevent="submit">
        <h2>Changer de mot de passe</h2>

        <div v-if="message">{{ message }}</div>

        <div class="app-form_row">
            <input
                v-model="password"
                type="password"
                placeholder="Mot de passe"
            />
        </div>

        <div class="app-form_row">
            <input
                v-model="passwordCheck"
                type="password"
                placeholder="Vérifier le mot de passe"
            />
        </div>

        <button type="submit" :disabled="loading">
            {{ loading ? "..." : "Modifier son mot de passe" }}
        </button>
    </form>
</template>
