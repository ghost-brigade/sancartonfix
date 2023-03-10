<script setup>
import { ref, watch } from 'vue';
import { GoogleMap, Marker } from "vue3-google-map";
import { Housing } from "@/api/housing";
import CategoriesOptions from './CategoriesOptions.vue';
import { useRouter } from "vue-router";
import Swal from 'sweetalert2'

const $router = useRouter();
const props = defineProps({
    housing: {
        type: Object,
        required: false,
        default: {},
    },
    type: {
        type: String,
        required: true,
    },
});
const name = ref(props.housing?.name ?? "");
const description = ref(props.housing?.description ?? "");
const latitude = ref(props.housing?.latitude ?? 0);
const longitude = ref(props.housing?.longitude ?? 0);
const price = ref(props.housing?.price ?? 1);
const category = ref(props.housing?.category ?? null);
watch(props.housing, () => {
    name.value = props.housing?.name ?? "";
    description.value = props.housing?.description ?? "";
    latitude.value = props.housing?.latitude ?? 0;
    longitude.value = props.housing?.longitude ?? 0;
    price.value = props.housing?.price ?? 1;
    category.value = props.housing?.category ?? null;
});
const housingAPI = new Housing();
const submit = async () => {
    const data = {
        name: name.value,
        description: description.value,
        latitude: parseFloat(latitude.value).toFixed(7),
        longitude: parseFloat(longitude.value).toFixed(7),
        price: parseFloat(price.value).toFixed(2),
        category: `/categories/${category.value}`,
    }

    if (props.type === 'create') {
        const newHousing = await housingAPI.create(data);
        $router.push(`/housing/${newHousing.slug}`);
        await Swal.fire({title: "Validation", text: "Votre logement a bien été créé", icon: "success"});

    } else if (props.type === 'update') {
        const updatedHousing = await housingAPI.update(props.housing.id, data);
        $router.push(`/housing/${updatedHousing.slug}`);
        await Swal.fire({title: "Validation", text: "Votre logement a bien été modifié", icon: "success"});
    }
}
const API_KEY = import.meta.env.VITE_MAPS_API_KEY;
const selectOnMap = (event) => {
    // Float with 7 decimals
    latitude.value = event.latLng.lat();
    longitude.value = event.latLng.lng();
}
</script>
<template>
    <form @submit.prevent="submit">
        <div class="app-form_row">
            <label for="name">Nom du logement</label>
            <input type="text" id="name" v-model="name" placeholder="Nom du logement" />
        </div>

        <div class="app-form_row">
            <label for="description">Description du logement</label>
            <textarea id="description" v-model="description" placeholder="Description du logement"></textarea>
        </div>
        <div class="app-form_row">
            <label for="price">Prix</label>
            <input type="number" id="price" v-model="price" placeholder="Prix" />
        </div>
        <div class="app-form_row">
            <label for="category">Catégorie</label>
            <select id="category" v-model="category">
                <CategoriesOptions />
            </select>
        </div>
        <GoogleMap :api-key="API_KEY"
            style="width: 100%; height: 500px"
            :center="{ lat: 48.85, lng: 2.35 }"
            :zoom="14"
            @click="selectOnMap"
        >
            <Marker v-if="longitude && latitude" :options="{ position:  { lat: latitude, lng: longitude } }" />
        </GoogleMap>
        <div v-show="longitude && latitude" style="margin-top: 1rem; margin-bottom: 1rem">
            Localisation : <br/>
            Latitude : {{ latitude }}
            Longitude : {{ longitude }}
        </div>
        <button type="submit">{{ props.type === 'create' ? 'Créer' : 'Modifier' }} le logement</button>
    </form>
</template>
