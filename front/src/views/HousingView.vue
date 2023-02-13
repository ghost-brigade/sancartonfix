<script setup>
import { Housing } from "@/api/housing";
import { ref, inject, reactive } from "vue";
import { useRoute, useRouter } from "vue-router";
import { DatePicker } from "v-calendar";
import "v-calendar/dist/style.css";
import { Api } from "@/api/api";
import { Renting } from "@/api/renting";
import { SECURITY_currentUser } from "@/providers/ProviderKeys";
import { Like } from "@/api/like";
import Swal from "sweetalert2";
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

const liked = reactive({ liked: false });

const likeApi = new Like();
const likeHousing = async () => {
    if (liked.id) {
        const newLike = await likeApi.create({
            "liked": true,
            "housing": "/housings/" + housing.value.id
        });
        Object.assign(liked, newLike);
    } else {
        const updatedLiked = await likeApi.update(liked.id, {
            "liked": !liked?.liked ?? false,
            "housing": "/housings/" + housing.value.id
        });
        Object.assign(liked, updatedLiked);
    }
}
const loadLiked = async () => {
    const filters = [
        { property: "housing", value: housing.value.id },
    ];

    const response = await likeApi.findAll({
        page: 1,
        itemsPerPage: 20,
        filters,
    });
    if (response["hydra:member"] && response["hydra:member"].length) {
        const dataFound = response["hydra:member"][0] ?? null;
        Object.assign(liked, dataFound);
    }
}


async function getData() {
    const filters = [
        { property: "slug", value: slug },
        //{ property: "rentings.status", value: false },
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
    isOwner();
    loadLiked();
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
            await Swal.fire({title: "Erreur", text: msg["hydra:description"], icon: "error"});
            // message.value = msg["hydra:description"];
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
        <h1 v-if="housing.name">{{ housing.name }}</h1>
        <img v-for="image in housing.media" :src="Api.url + '/' + image.contentUrl" />
        <h2 v-if="housing.price">Prix : {{ housing.price }} €</h2>

        <p v-if="housing.description">{{ housing.description }}</p>

        <h2 v-if="housing.Category">Catégorie : {{ housing.Category.name }}</h2>

        <h2 v-if="housing.latitude">Latitude : {{ housing.latitude }}</h2>
        <h2 v-if="housing.longitude">Latitude : {{ housing.longitude }}</h2>

        <h3 v-if="housing.longitude">
            Créé le : {{ new Date(housing.createdAt).toLocaleString() }}
        </h3>
        <RouterLink v-if="owner" :to="`/housing/update/${slug}`">
            <button>Modifier mon logement</button>
        </RouterLink>
        <template v-else>
            <DatePicker v-model="date" :disabled-dates="disabledDays" is-range @input="selectRange"
                @change="submitRange" />

            <div v-if="message" class="app-form_outside_message">
                {{ message }}
            </div>
            <div>
                <button @click="rentThisHousing()">Réserver</button>
                <button @click="likeHousing()">
                    {{ liked?.liked ? "Je n'aime plus" : "J'aime" }}
                </button>
            </div>

        </template>
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
