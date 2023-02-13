<script setup>
import { Housing } from "@/api/housing";
import { ref } from "vue";
import { useRoute } from "vue-router";
import { DatePicker } from "v-calendar";
import "v-calendar/dist/style.css";

const housing = ref([]);
const disabledDays = ref([]);
const date = ref(null);

const { slug } = useRoute().params;

async function getData() {
    const filters = [
        { property: "slug", value: slug },
        { property: "rentings.status", value: false },
    ];

    const housingApi = new Housing();
    const response = await housingApi.findAll({
        page: 1,
        itemsPerPage: 20,
        filters,
    });
    housing.value = response["hydra:member"][0] ?? null;

    disabledDays.value = housing.value.rentings.map((renting) => {
        console.log(renting);
        return {
            start: new Date(renting.dateStart),
            end: new Date(renting.dateEnd),
        };
    });
    console.log(disabledDays.value);
}

getData();

function rentThisHousing() {
    alert("test");
}
</script>

<template>
    <div v-for="a in disabledDays.value">
        <p>{{ a }}</p>
    </div>
    <div v-if="housing">
        <h1 v-if="housing.name">{{ housing.name }}</h1>
        <img
            v-for="image in housing.media"
            :src="import.meta.env.VITE_API_URL + '/' + image.contentUrl"
        />
        <h2 v-if="housing.price">Prix : {{ housing.price }} €</h2>

        <p v-if="housing.description">{{ housing.description }}</p>

        <h2 v-if="housing.Category">Catégorie : {{ housing.Category.name }}</h2>

        <h2 v-if="housing.latitude">Latitude : {{ housing.latitude }}</h2>
        <h2 v-if="housing.longitude">Latitude : {{ housing.longitude }}</h2>

        <h3 v-if="housing.longitude">
            Créé le : {{ new Date(housing.createdAt).toLocaleString() }}
        </h3>

        <DatePicker
            v-model="date"
            :disabled-dates="disabledDays"
            is-range
            @input="selectRange"
            @change="submitRange"
        />

        <button @click="rentThisHousing()">Réserver</button>
    </div>

    <p v-else>Aucun logement n'a été trouvé.</p>
</template>

<script>
export default {
    name: "HousingView",
    setup() {
        const date = ref(null);
        return { date };
    },
    // data() {
    //     return {
    //         disabledDays: []
    //     }
    // },
    // mounted() {
    //
    // },
    methods: {
        selectRange(date) {
            console.log(`Selected range: ${date.start} - ${date.end}`);
        },
        submitRange() {
            console.log(
                `Submitted range: ${this.date.start} - ${this.date.end}`
            );
        },
    },
};
</script>
