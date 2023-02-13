<script setup>
import { Housing } from "@/api/housing";
import { ref, inject } from "vue";
import { SECURITY_currentUser } from "@/providers/ProviderKeys";
import { useRoute, useRouter } from "vue-router";
import { DatePicker } from "v-calendar";
import "v-calendar/dist/style.css";
import { Api } from "@/api/api";
import { Renting } from "@/api/renting";

const { currentUser } = inject(SECURITY_currentUser);
const housing = ref([]);
const disabledDays = ref([]);
const date = ref(null);

const message = ref(null);
const router = useRouter();
const { slug } = useRoute().params;
const owner = ref(false);
const isOwner = () => {
    owner.value = housing.value.owner === `/users/${currentUser?.id}`;
}

async function getData() {
    const filters = [
        { property: "slug", value: slug },
        // { property: "rentings.status", value: false },
    ];

    const housingApi = new Housing();
    const response = await housingApi.findAll({
        page: 1,
        itemsPerPage: 20,
        filters,
    });
    housing.value = response["hydra:member"][0] ?? null;

    disabledDays.value = housing.value.rentings.map((renting) => {
        return {
            start: new Date(renting.dateStart),
            end: new Date(renting.dateEnd),
        };
    });
    console.log(disabledDays.value);
    isOwner();
}

getData();

async function rentThisHousing() {
    console.log(date.value);

    const renting = {
        dateStart: date.value.start,
        dateEnd: date.value.end,
        housing: "/housings/" + housing.value.id,
    };

    try {
        const rentingApi = new Renting();
        const response = await rentingApi.create(renting);

        if (response.status === 201) {
            router.push("/");
        } else {
            const msg = await response.json();
            message.value = msg["hydra:description"];
        }
    } catch (e) {
        console.log(e);
    }
}
</script>

<template>
    <div v-for="a in disabledDays.value">
        <p>{{ a }}</p>
    </div>

    <div v-if="housing">
        <RouterLink v-if="owner" :to="`/housing/update/${slug}`">
            <button>Modifier mon logement</button>
        </RouterLink>
        
        <template v-else>
        <h1 v-if="housing.name">{{ housing.name }}</h1>
        <img
            v-for="image in housing.media"
            :src="Api.url + '/' + image.contentUrl"
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
        
        <div v-if="message" class="app-form_outside_message">
            {{ message }}
        </div>
        <button @click="rentThisHousing()">Réserver</button>
        </div
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
