<script setup>
import { onMounted, ref } from "vue";
import { Category } from "@/api/category";
import router from "@/router";

const categories = ref([]);
// const selectedCategory = ref(null);
const city = ref("");

// onMounted(async () => {
//     const categoryApi = new Category();
//     categories.value = await categoryApi.findAll({
//         orders: { property: "name", direction: "ASC" },
//     }).then((response) => {
//         return response["hydra:member"];
//     });
//     console.log(categories.value);
// });

const submit = () => {
    if (!categories.value || !city.value) {
        return;
    }
    router.push({
        name: "result",
        params: { category: categories.value, city: city.value },
    });
};
</script>

<template>
    <form @submit.prevent="submit">
        <h2>Trouver mon logement</h2>
        <!-- <div class="app-form_row">
            <select v-if="categories.value.length" v-model="selectedCategory">
                <option :value="null" :disabled="true" selected>Choisir une catégorie</option>
                <option v-for="category in categories.value" :key="category.id" :value="category">
                    {{ category }}
                </option>
            </select>
        </div> -->

        <div class="app-form_row">
            <select v-model="categories">
                <option disabled value="">Sélectionnez une catégorie</option>
                <option value="carton">Carton</option>
                <option value="tente">Tente</option>
                <option value="banc">Banc</option>
                <option value="ascenseur">Ascenseur</option>
                <option value="divers">Divers</option>
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
