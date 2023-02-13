<script setup>
import { ref } from 'vue';
import { Users } from "@/api/users";
import {useRoute, useRouter} from "vue-router";

const router = useRouter();

const message = ref(null);
const { token } = useRoute().params;

const handleConfirmation = async () => {
    try {
        message.value = null;

        if (token === '' || token === undefined) {
            throw new Error('Le lien de réinitialisation de mot de passe est invalide ou a expiré');
        }

        const users = new Users();
        const response = await users.validateAccount(token);

        if (response.status === 200) {
            router.push("/login");
        } else {
            throw new Error('Le lien de réinitialisation de mot de passe est invalide ou a expiré');
        }
    } catch (err) {
        message.value = err.message;
    }
};

handleConfirmation();
</script>

<template>
    <h2>Confirmation de compte</h2>

    <div class="app-form_row">
        <p v-if="message" class="text-danger">{{ message }}</p>
    </div>
</template>
