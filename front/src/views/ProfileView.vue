<script setup>
import ChangePasswordForm from "../components/forms/ChangePasswordForm.vue";
import BalanceForm from "../components/forms/BalanceForm.vue";
import ProfileForm from "../components/forms/ProfileForm.vue";
import { inject } from "vue";
import { SECURITY_currentUser } from "@/providers/ProviderKeys";
import { useRouter } from "vue-router";
import Swal from 'sweetalert2'

const { setCurrentUser } = inject(SECURITY_currentUser);
const $router = useRouter();

const disconnect = () => {
    setCurrentUser({});
    $router.push("/login");
    Swal.fire({title: "Validation", text: "Déconnexion effectué avec succès !", icon: "success"});
};
</script>

<template>
    <BalanceForm />
    <ProfileForm />
    <ChangePasswordForm />
    <div :style="{ textAlign: 'center', marginTop: '1rem' }">
        <button class="app-form_outside_button" @click="disconnect">
            Se déconnecter
        </button>
    </div>
</template>
