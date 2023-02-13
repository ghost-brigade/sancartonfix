
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
const housings = ref([]);
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
    const likedData = await likeApi.findAll(1, 20, [{ property: "author", value: currentUser.id }, { property: "liked", value: true }]);

    if (likedData["hydra:member"]) {
        const housingIds = [];
        if (likedData["hydra:member"]) {
            likedData["hydra:member"].forEach(like => {
                const housing = like.housing;
                housingIds.push(housing.split("/").pop());
            });
        }
        Object.assign(housingLinks, housingIds);
    } else { return }

    const housingApi = new Housing();

    try {
        const data = await housingApi.findAll(1, 1000, []);
        housings.value = data["hydra:member"].filter(housing => housingLinks.includes(housing.id));
        items.value = housings.value?.length;
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
                    <RouterLink :to="`/housing/${housing.slug}`">
                        {{ housing.name }}
                    </RouterLink>
                </h3>
                <p>{{ housing.price }}€/nuit</p>
            </div>
            <template v-if="!housings?.length">
                <p>Vous n'avez pas encore aimé de logement</p>
            </template>
        </div>
    </div>

</template>
