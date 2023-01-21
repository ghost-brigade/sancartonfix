<script setup>
import {onMounted, ref} from 'vue';
import { Category } from '@/api/category';

const category = ref({});
const city = ref('');

onMounted(async () => {
    const categoryApi = new Category();

    category.value = await categoryApi.findAll({
        orders: { property: "name", direction: "ASC" },
    }).then((response) => {
        return response["hydra:member"];
    });
});

const submit = () => {
    if (!category.value || !city.value) {
        return;
    }
    console.log(category.value);
    console.log(city.value);
};
</script>

<template>
    <form @submit.prevent="submit">
        <h2>Trouver mon logement</h2>

        <div class="app-form_row">
            <select v-model="category.value">
                <option :value="null" :disabled="true" selected>Choisir une catégorie</option>
                <option
                    v-for="category in category"
                    :key="category.id"
                    :value="category.id"
                >
                    {{ category.name }}
                </option>
            </select>
        </div>

        <div class="app-form_row">
            <select v-model="city">
                <option disabled value="">Sélectionnez votre ville</option>
                <option value="paris">Paris</option>
                <option value="lyon">Lyon</option>
                <option value="marseille">Marseille</option>
            </select>
        </div>

        <button type="submit">Rechercher</button>
    </form>
</template>
