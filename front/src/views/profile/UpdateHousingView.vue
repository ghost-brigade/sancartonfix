<script setup>
import HousingForm from '../../components/forms/HousingForm.vue';
import { Housing } from "@/api/housing";
import { ref, reactive } from "vue";
import { useRoute } from "vue-router";
import "v-calendar/dist/style.css";
const housing = reactive({});
const date = ref(null);
const { slug } = useRoute().params;
console.log(slug);
async function getData() {
    const filters = [
        { property: "slug", value: slug },
    ];

    const housingApi = new Housing();
    const response = await housingApi.findAll({
        page: 1,
        itemsPerPage: 20,
        filters,
    });

    Object.assign(housing, response["hydra:member"][0] ?? {});
    console.log(housing);
}
getData();
</script>
<template>
    <section>
        <h1>Modifier un logement</h1>
        <HousingForm :type="'update'" :housing="housing"/>
    </section>
</template>