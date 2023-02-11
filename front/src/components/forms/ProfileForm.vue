<script setup>
import { inject, ref, watch } from "vue";
import { SECURITY_currentUser } from "@/providers/ProviderKeys";
import { Users } from "@/api/users";

/**
 * "id": "1ed98034-ff73-61f2-acdc-f91a37951843",
	"email": "admin@localhost",
	"roles": [
		"ROLE_ADMIN",
		"ROLE_USER"
	],
	"firstname": "Julien",
	"lastname": "Bélix",
	"gender": true
 */

const loading = ref(false);

const { currentUser, setCurrentUser } = inject(SECURITY_currentUser);

const email = ref(currentUser.email);
const firstname = ref(currentUser.firstname);
const lastname = ref(currentUser.lastname);

watch(currentUser, () => {
    Object.assign(email, currentUser.email);
    Object.assign(firstname, currentUser.firstname);
    Object.assign(lastname, currentUser.lastname);
});

const users = new Users();
const submit = async () => {
    if (loading.value) {
        return;
    }

    if (!email.value || !firstname.value || !lastname.value) {
        return;
    }

    const userUpdate = {};

    if (email.value !== currentUser.email) {
        userUpdate.email = email.value;
    }
    if (firstname.value !== currentUser.firstname) {
        userUpdate.firstname = firstname.value;
    }
    if (lastname.value !== currentUser.lastname) {
        userUpdate.lastname = lastname.value;
    }

    const updated = await users
        .update(currentUser.id, userUpdate)
        .finally(() => {
            loading.value = false;
        });

    setCurrentUser(updated);
};
</script>

<template>
    <form @submit.prevent="submit">
        <h2>Modifier mon profil</h2>

        <div class="app-form_row">
            <input v-model="lastname" type="text" placeholder="Nom" />
        </div>

        <div class="app-form_row">
            <input v-model="firstname" type="text" placeholder="Prénom" />
        </div>

        <div class="app-form_row">
            <input v-model="email" type="email" placeholder="Email" />
        </div>

        <div class="app-form_row app-form_buttons">
            <button type="submit" :disabled="loading">
                {{ loading ? "..." : "Modifier" }}
            </button>
        </div>
    </form>
</template>
