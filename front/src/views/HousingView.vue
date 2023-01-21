<script setup>

import {Housing} from '../api/housing.js';
import {ref} from 'vue';
import { useRoute } from 'vue-router'

const housing = ref([]);
const { slug } = useRoute().params;

async function getData() {
    const filters = [
        {property: "slug", value: slug},
        {property: "rentings.status", value: false}
    ];
    const housingApi = new Housing();
    const response = await housingApi.findAll(1, 20, filters);
    housing.value = response['hydra:member'][0];
}

getData()

function rentThisHousing() {
    alert('test')
}

</script>

<template>
    <div v-if="housing">
        <h1 v-if="housing.name">{{ housing.name }}</h1>
        <img v-for="image in housing.media"
             :src="'https://localhost' + image.contentUrl"/>
        <h2 v-if="housing.price">Prix : {{ housing.price }} €</h2>

        <p v-if="housing.description">{{ housing.description }}</p>

        <h2 v-if="housing.Category">Catégorie : {{ housing.Category.name }}</h2>

        <h2 v-if="housing.latitude">Latitude : {{ housing.latitude }}</h2>
        <h2 v-if="housing.longitude">Latitude : {{ housing.longitude }}</h2>

        <h3 v-if="housing.longitude">Créé le : {{ new Date(housing.createdAt).toLocaleString() }}</h3>

        <button @click="rentThisHousing()">Réserver</button>
    </div>

    <p v-else>Aucun logement n'a été trouvé.</p>
</template>


