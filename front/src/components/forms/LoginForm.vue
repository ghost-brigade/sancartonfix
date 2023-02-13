<script setup>
import { ref, onMounted, inject } from "vue";
import { Security } from "@/api/security";
import { SECURITY_currentUser } from "@/providers/ProviderKeys";
import { useRouter } from "vue-router";
import Swal from 'sweetalert2'

const email = ref("");
const password = ref("");
const loading = ref(false);
const error = ref(null);

const { setCurrentUser } = inject(SECURITY_currentUser);
const security = new Security();

const router = useRouter();
const redirectRoute = router.currentRoute.value.query.redirect;

const submit = async () => {
    if (loading.value) {
        return;
    }

    if (!email.value || !password.value) {
        Swal.fire({ title: "Attention", text: "Un des champs est vide", icon: "warning" });
        return;
    }

    loading.value = true;

    try {
        await security.token(
            { email: email.value, password: password.value },
            false
        );
        await security
            .profile()
            .then((user) => {
                setCurrentUser(user);
                Swal.fire({ title: "Validation", text: "Connexion effectuée avec succès !", icon: "success" });
            })
            .catch((error) => {
                error.value = error.message;
                Swal.fire({ title: "Erreur", text: error.message, icon: "error" });

            });
    } catch (err) {
        error.value = err.message;
        Swal.fire({ title: "Erreur", text: err.message, icon: "error" });

    } finally {
        loading.value = false;
    }

    if (redirectRoute) {
        router.push(redirectRoute);
    } else {
        router.push("/profile");
    }
};

onMounted(() => {
    localStorage.removeItem("token");
});
</script>

<template>
    <form @submit.prevent="submit">
        <h2>Me connecter</h2>

        <div v-if="error">
            {{ error }}
        </div>

        <div class="app-form_row">
            <input v-model="email" type="email" placeholder="Email" />
        </div>

        <div class="app-form_row">
            <input v-model="password" type="password" placeholder="Mot de passe" />
        </div>

        <div class="app-form_row" style="
            text-decoration: underline;
            font-size: 0.8rem;
            display: flex;
            justify-content: space-between;
        ">
            <router-link to="/forgot-password">Mot de passe oublié</router-link>
            <router-link to="/register">Créer un compte</router-link>
        </div>


        <button type="submit" :disabled="loading">
            {{ loading? "...": "Connexion" }}
        </button>
    </form>
</template>
