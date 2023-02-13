<script setup>
import {ref} from "vue";
import {Renting} from "@/api/renting";
import moment from "../../utils/date";
import RedirectCard from "../../components/cards/RedirectCard.vue";
import {Api} from "../../api/api";

const rentings = ref({});

const items = ref(0);
const views = ref({});
const page = ref(1);

const rentingApi = new Renting();

async function getData() {
    const data = await rentingApi.findAll({
        page: page.value,
        itemsPerPage: 5,
        orders: {property: "createdAt", direction: "DESC"},
    });

    rentings.value = data["hydra:member"];
    items.value = data["hydra:totalItems"];
    views.value = data["hydra:view"];
}

getData();

const cancelRenting = async (renting) => {
    console.log(renting)
    try {
        const rentingApi = new Renting();
        await rentingApi.del(renting.id);

        const response = await rentingApi.del(renting.id);

        if (response.ok) {
            message.value = "Le mot de passe a bien été modifié";
        } else {
            const error = await response.json();
            message.value = error.message;
        }
    } catch (err) {
        message.value =
            "Une erreur est survenue lors de la modification du mot de passe";
    }
    // getData();
};

const handlePageChange = (newPage) => {
    page.value = newPage;
    getData();
};
</script>

<template>
    <section>
        <div v-if="rentings.length > 0">
            <h1>Mes locations</h1>
            <ul class="app-card_list">
                <template v-for="renting in rentings" :key="renting.id">
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
                                :alt="renting?.housing?.slug + '-img'"
                            />
                        </template>

                        <h3>{{ renting?.housing?.name }}</h3>
                        <span
                        >Réservation du :
                            {{ moment(renting?.dateStart).format("DD/MM/YYYY") }} au
                            {{ moment(renting?.dateEnd).format("DD/MM/YYYY") }}</span
                        >
                    </RedirectCard>
                    <button @click="cancelRenting(renting)">Annuler</button>

                </template>
            </ul>

            <br/>

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
            <h2>Aucune location trouvée</h2>
        </div>
    </section>
</template>
