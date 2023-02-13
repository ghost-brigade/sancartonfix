<script setup>
import {inject, ref} from "vue";
import {Users} from "@/api/users";
import { SECURITY_currentUser } from "@/providers/ProviderKeys";

const error = ref(null);
const amount = ref(null);

const { currentUser } = inject(SECURITY_currentUser);
const users = new Users();

const submit = async () => {
    error.value = null;

    if (!amount.value) {
        error.value = "Veuillez saisir un montant";
        return;
    }

    if (amount.value < 0) {
        error.value = "Veuillez saisir un montant positif";
        return;
    }

    if (amount.value > 1000) {
        error.value = "Veuillez saisir un montant inférieur à 1000";
        return;
    }

    try {
        await users.payementIntent(amount.value).then(async (response) => {
            const res = await response.json();
            window.location.href = res.paymentIntent;
        });
        amount.value = 0;
    } catch (e) {
        error.value = e.message;
    }
};
</script>

<template>
    <form @submit.prevent="submit">
        <h2>Portefeuille</h2>

        <div v-if="error">
            {{ error }}
        </div>

        <div v-if="currentUser">
            <div class="app-form_row">
                <label>Argent disponible</label>
                <span>{{ currentUser.balance }} €</span>
            </div>
        </div>

        <div class="app-form_row">
            <input v-model="amount" type="number" placeholder="Argent a ajouter" />
        </div>

            <button class="app-form_button" type="submit" @click="submit">
                Payé
            </button>
    </form>
</template>
