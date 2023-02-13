<script setup>
import {ref, reactive, inject} from "vue";
import {Renting} from "@/api/renting";
import {Housing} from "@/api/housing";
import {Report} from "@/api/report";
import moment from "../../utils/date";
import RedirectCard from "../../components/cards/RedirectCard.vue";
import { Api } from "@/api/api";
import Swal from 'sweetalert2'
import { SECURITY_currentUser } from "../../providers/ProviderKeys";

const rentings = ref({});
const housings = reactive([]);

const { currentUser } = inject(SECURITY_currentUser);

const items = ref(0);
const views = ref({});
const page = ref(1);
const message = ref("");

const rentingApi = new Renting();

async function getData() {

    const data = await rentingApi.findAll({
        page: page.value,
        itemsPerPage: 5,
        orders: { property: "createdAt", direction: "DESC" },
    });

    rentings.value = data["hydra:member"];
    items.value = data["hydra:totalItems"];
    views.value = data["hydra:view"];

    
    const housingApi = new Housing();

    try {
        const data = await housingApi.findAll({
            page: page.value,
            itemsPerPage: 5,
            filters: [{ property: "owner", value: currentUser?.id }],
            orders: { property: "createdAt", direction: "DESC" },
        });

        Object.assign(housings, data["hydra:member"]);
    } catch (error) {
        console.log(error);
    }
}

getData();

const cancelRenting = async (renting) => {
    console.log(renting)
    try {
        const rentingApi = new Renting();
        // await rentingApi.del(renting.id);

        const response = await rentingApi.remove(renting.id, false);
    console.log(response)
        if (response.ok) {
            // message.value = "La location " + renting.housing.name + " a bien été annulée";
            await Swal.fire({title: "Validation", text: "La location " + renting.housing.name + " a bien été annulée", icon: "success"});
        } else {
            const error = await response.json();
            await Swal.fire({title: "Erreur", text: error["hydra:description"], icon: "error"});
            // message.value = error["hydra:description"];
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

const signalElement = (renting, housing) => {
    const data = {
        "content": "User reported",
        "renting": renting['@id']
    };

    const reportApi = new Report();
    reportApi.create(data).then((response) => {
        Swal.fire({
            title: 'Signalement',
            text: 'Le signalement a bien été envoyé',
            icon: 'success',
            confirmButtonText: 'Ok'
        });
    });
}
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

    <section>
        <div v-if="rentings.length > 0">
            <h1>Mes logements réservés</h1>
            <ul class="app-card_list">
                <template v-for="housing in housings" :key="housings.id">
                    <p>{{ housing.name }}</p>
                    <template v-for="renting in housing?.rentings">
                        <p>Du {{ renting.dateStart }} au {{ renting.dateEnd }}</p>
                        <button @click="() => signalElement(renting, housing)">Signaler</button>
                    </template>
                </template>
            </ul>
        </div>
        <div v-else>
            <h2>Aucune réservation trouvée</h2>
        </div>
    </section>
</template>
