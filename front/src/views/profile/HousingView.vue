<script setup>
import { ref, inject } from "vue";
import { Housing } from "@/api/housing";
import { SECURITY_currentUser } from "@/providers/ProviderKeys";
import RedirectCard from "../../components/cards/RedirectCard.vue";
import {Api} from "../../api/api";

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
            filters: [{ property: "owner", value: currentUser?.id }],
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
    <section>
        <div v-if="housings?.length > 0">
            <h1>Ma liste de logements</h1>
            <ul class="app-card_list">
                <template v-for="housing in housings" :key="housings.id">
                    <RedirectCard
                        :redirect="`/housing/${renting?.housing?.slug}`"
                    >
                        <template #image>
                            <img
                                :src="
                                    housing?.media?.contentUrl === undefined
                                        ? '/image/housing/default.jpg'
                                        : Api.url + '/' +
                                          housing?.media?.contentUrl
                                "
                                :alt="housing?.slug + '-img'"
                            />
                        </template>

                        <h3>
                            <a :href="`/housing/${housing?.slug}`">{{
                                housing?.name
                            }}</a>
                        </h3>
                        <span
                            >Status:
                            {{ housing?.active ? "Actif" : "Innactif" }}</span
                        ><br />
                        <span>Prix: {{ housing?.price }} EUR</span>
                    </RedirectCard>
                </template>
            </ul>

            <br />

            <div>
                <div v-show="views['hydra:next']">
                    <button @click="handlePageChange(page + 1)">Suivant</button>
                </div>
                <div v-show="views['hydra:previous']">
                    <button @click="handlePageChange(page - 1)">
                        Précédent
                    </button>
                </div>
            </div>
        </div>
        <div v-else>
            <h2>Aucun logement trouvée</h2>
        </div>
    </section>
</template>
