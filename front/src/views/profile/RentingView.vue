<script setup>
import { ref } from "vue";
import { Renting } from "@/api/renting";
import moment from "../../utils/date";

const rentings = ref({});

const items = ref(0);
const views = ref({});
const page = ref(1);

async function getData() {
    const rentingApi = new Renting();

    try {
        const data = await rentingApi.findAll({
            page: page.value,
            itemsPerPage: 5,
            orders: { property: "createdAt", direction: "DESC" },
        }).then((response) => {
            if(response.ok) {
                return response.json();
            }
            throw new Error("Une erreur est survenue lors de la récupération des données");
        });

        rentings.value = data["hydra:member"];
        items.value = data["hydra:totalItems"];
        views.value = data["hydra:view"];
    } catch (error) {
        console.log(error);
    }
}

getData();

const handlePageChange = (newPage) => {
    page.value = newPage;
    getData();
};
</script>

<template>
    <div v-if="rentings.length > 0">
        <h1>Mes locations</h1>
        <br />
        <ul v-for="renting in rentings" :key="renting.id">
            <li>
                <img
                    :src="renting?.housing?.media?.contentUrl === undefined
                            ? '/image/housing/default.jpg'
                            : 'https://localhost/' + renting?.housing?.media?.contentUrl
                    "
                    :alt="renting?.housing?.slug + '-img'"
                />
                <h3><a :href="`/housing/${renting?.housing?.slug}`">{{ renting?.housing?.name }}</a></h3>
                <span>Réservation du : {{ moment(renting?.dateStart).format("LL") }} au {{ moment(renting?.dateEnd).format("LL") }}</span>
            </li>
            <br />
        </ul>

        <br />

        <div>
            <div v-if="views['hydra:next']">
                <button @click="handlePageChange(page + 1)">Next</button>
            </div>
            <div v-if="views['hydra:previous']">
                <button @click="handlePageChange(page - 1)">Previous</button>
            </div>
        </div>
    </div>
    <div v-else>
        <h2>Aucune location trouvée</h2>
    </div>
</template>
