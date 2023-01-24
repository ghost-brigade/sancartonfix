<script setup>

import { ref } from 'vue';

const category = ref('');
const city = ref('');
const $rooter = useRouter();

const submit = () => {
    if (!category.value || !city.value) {
        return;
    }
    console.log(category.value);
    console.log(city.value);
}

</script>

<template>
    <form @submit.prevent="submit">
        <h2>Trouver mon logement</h2>
        
        <div class="app-form_row">
            <select v-model="category">
                <option disabled value="">Sélectionnez votre type de logement</option>
                <option value="carton">Carton</option>
                <option value="banc">Banc</option>
                <option value="tente">Tente</option>
            </select>
        </div>

        <div class="app-form_row">
            <select v-model="city">
                <option  disabled value="">Sélectionnez votre ville</option>
                <option value="paris">Paris</option>
                <option value="lyon">Lyon</option>
                <option value="marseille">Marseille</option>
            </select>
        </div>

        <button type="submit">Rechercher</button>
    </form>
</template>