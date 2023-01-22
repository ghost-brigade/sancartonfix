<script setup>
import { ref, inject } from "vue";
import { Housing } from "@/api/housing";
import { SECURITY_currentUser } from "@/providers/ProviderKeys";
const { currentUser } = inject(SECURITY_currentUser);

const housings = ref({});
const items = ref(0);
const views = ref({});
const page = ref(1);

async function getData() {
    const housingApi = new Housing();

    try {
        const data = await housingApi.findAll({
            page: page.value,
            itemsPerPage: 5,
            filters: [{ property: 'owner', value: currentUser?.id }],
            orders: { property: "createdAt", direction: "DESC" },
        });

        housings.value = data["hydra:member"];
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
    <div v-if="housings?.length > 0">
        <h1>Ma liste de logements</h1>
        <br />
        <ul v-for="housing in housings" :key="housings.id">
            <li>
                <img
                    :src="housing?.media?.contentUrl === undefined
                            ? '/image/housing/default.jpg'
                            : 'https://localhost/' + housing?.media?.contentUrl
                    "
                    :alt="housing?.slug + '-img'"
                />
                <h3><a :href="`/housing/${housing?.slug}`">{{ housing?.name }}</a></h3>
                <span>Status: {{ housing?.active ? 'Actif' : 'Innactif' }}</span><br>
                <span>Prix: {{ housing?.price }} EUR</span>
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
        <h2>Aucune logement trouvée</h2>
    </div>
</template>
