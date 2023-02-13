<script setup>

import { ref, onMounted } from 'vue';
import { Users } from "@/api/users";
import { useRouter } from 'vue-router';

const email = ref('');
const loading = ref(false);
const error = ref(null);

const router = useRouter();
const users = new Users();

const submit = async () => {
    if (loading.value) {
        return;
    }

    if (!email.value) {
        return;
    }

    loading.value = true;

    try {
        await users.forgotPassword({email: email.value});
        router.push("/login");
    } catch (err) {
        error.value = err.message;
    } finally {
        loading.value = false;
    }
};

onMounted(() => {});

</script>

<template>
    <form @submit.prevent="submit">
        <h2>Mot de passe oublié</h2>

        <div v-if="error">
            {{ error }}
        </div>

        <div class="app-form_row">
            <input v-model="email" type="email" placeholder="Email" />
        </div>

        <button type="submit" :disabled="loading">
            {{ loading ? "..." : "Envoyer" }}
        </button>
    </form>
</template>
