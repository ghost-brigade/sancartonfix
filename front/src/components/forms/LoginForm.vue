<script setup>

import { ref, onMounted, inject } from 'vue';
import { Security } from "@/api/security";
import { SECURITY_currentUser } from "@/providers/ProviderKeys";
import { useRouter } from 'vue-router';

const email = ref('admin@localhost');
const password = ref('password');
const loading = ref(false);

const { setCurrentUser } = inject(SECURITY_currentUser);
const security = new Security();

const router = useRouter();
const redirectRoute = router.currentRoute.value.query.redirect;

const submit = async () => {
    if (loading.value) {
        return;
    }

    if (!email.value || !password.value) {
        return;
    }

    loading.value = true;

    await security.token(
        { email: email.value, password: password.value }
    ).finally(() => {
        loading.value = false;
    });
    
    const user = await security.profile();
    setCurrentUser(user);

    if (redirectRoute) {
        router.push(redirectRoute);
    } else {
        router.push('/profile');
    }
}

onMounted(() => {
    localStorage.removeItem('token');
});

</script>

<template>
    <form @submit.prevent="submit">
        <h2>Me connecter</h2>
        
        <div class="app-form_row">
            <input v-model="email" type="email" placeholder="Email">
        </div>

        <div class="app-form_row">
            <input v-model="password" type="password" placeholder="Mot de passe">
        </div>

        <button type="submit" :disabled="loading">
            {{ loading ? '...' : 'Connexion'}}
        </button>
    </form>
</template>