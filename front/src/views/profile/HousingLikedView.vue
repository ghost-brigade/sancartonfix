
<script setup>
import { ref, onMounted, defineComponent, inject, reactive } from "vue";

import { Housing } from "@/api/housing";
import Carousel from "../../components/carousels/Carousel.vue";
import CarouselControls from "../../components/carousels/CarouselControls.vue";
import CarouselIndicators from "../../components/carousels/CarouselIndicators.vue";
import CarouselItem from "../../components/carousels/CarouselItem.vue";
import { useRoute } from "vue-router";
import { Like } from "@/api/like";
import { SECURITY_currentUser } from "@/providers/ProviderKeys";

const { currentUser } = inject(SECURITY_currentUser);
const housings = ref({});
const items = ref(0);
const views = ref({});
const page = ref(1);
const { category } = useRoute().params;
const housingLinks = reactive([]);


defineComponent(
    {
        name: "HousingCarousel",
        components: {
            Carousel,
            CarouselControls,
            CarouselIndicators,
            CarouselItem,
        },
    }
);

async function getData() {
    const likeApi = new Like();
    const likedData = await likeApi.findAll(1, 20, [{ property: "author", value: currentUser.id }]);
    console.log(likedData);
    if (likedData["hydra:member"]) {
        const housingIds = [];
        if (likedData["hydra:member"]) {
            likedData["hydra:member"].forEach(like => {
                const housing = like.housing;
                housingIds.push(housing.split("/").pop());
            });
        }
        Object.assign(housingLinks, housingIds);
        console.log(likedHousingsIds);
    } else { return }

    const housingApi = new Housing();

    try {
        const data = await housingApi.findAll({
            page: page.value,
            itemsPerPage: 10,
            filters: [
                { property: "category.name", value: category.charAt(0).toUpperCase() + category.slice(1) },
                { property: "active", value: true },
                { property: "id", value: likedData["hydra:member"].map((like) => like.housing.id) }
            ],
            orders: { property: "createdAt", direction: "DESC" },
        });

        housings.value = data["hydra:member"];
        items.value = data["hydra:totalItems"];
        views.value = data["hydra:view"];
    } catch (error) {
        console.log(error);
    }
}

onMounted(() => {
    getData();
});

const handlePageChange = (newPage) => {
    page.value = newPage;
    getData();
};
</script>
<template>
    <div id="result">
        <div id="container-result">
            <div v-for="housing in housings" :key="housing.id" class="housings">
                <carousel :slides="housing.media" :interval="1000" controls indicators>
                </carousel>
                <h3>
                    <a v-bind:href="'/housing/' + housing.slug">{{
                        housing.name
                    }}</a>
                </h3>
                <p>{{ housing.price }}€/nuit</p>
            </div>
        </div>
    </div>

    <ul>
        <template v-for="housingLink in housingLinks">
            <li>
                <RouterLink :to="`/housing/${housingLink}`">
                    {{ housingLink }}
                </RouterLink>
            </li>
        </template>
    </ul>

    <br>

    <section style="display: flex; justify-content: center; min-width: 100%;">
        <div>
            <div v-show="views['hydra:next']">
                <button @click="handlePageChange(page + 1)">Next</button>
            </div>
            <div v-show="views['hydra:previous']">
                <button @click="handlePageChange(page - 1)">
                    Previous
                </button>
            </div>
        </div>
    </section>

</template>
