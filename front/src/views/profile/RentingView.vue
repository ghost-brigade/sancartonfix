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
const message = ref("");

const rentingApi = new Renting();
import Swal from 'sweetalert2'

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
        // await rentingApi.del(renting.id);

        const response = await rentingApi.remove(renting.id, false);

        if (response.ok) {
            message.value = "La location " + renting.housing.name + " a bien été annulée";
            Swal.fire({
                title: 'Validation',
                text: "La location " + renting.housing.name + " a bien été annulée",
                icon: 'success',
            });
        } else {
            const error = await response.json();
            Swal.fire({
                title: 'Erreur',
                text: error["hydra:description"],
                icon: 'error',
            });
            message.value = error["hydra:description"];
        }
    } catch (err) {
        console.log(err)

        message.value = err.message;
    }
    await getData();
};

const handlePageChange = (newPage) => {
    page.value = newPage;
    getData();
};
</script>

<template>
    <section>
        <div v-if="message">{{ message }}</div>

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

                        <template #actions>
                            <button @click="cancelRenting(renting)">Annuler</button>
                        </template>
                    </RedirectCard>

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
