<script setup>
import { onMounted, ref } from "vue";
import { Category } from "@/api/category";
import router from "@/router";

const categories = ref([]);

const submit = () => {
    if (!categories.value ) {
        return;
    }
    router.push({
        name: "result",
        params: { category: categories.value},
    });
};
</script>

<template>
    <form @submit.prevent="submit">
        <h2>Trouver mon logement</h2>
        <div class="app-form_row">
            <select v-model="categories">
                <option disabled value="">Sélectionnez une catégorie</option>
                <option value="carton">Carton</option>
                <option value="tente">Tente</option>
                <option value="banc">Banc</option>
            </select>
        </div>

        <button type="submit">Rechercher</button>
    </form>
</template>
