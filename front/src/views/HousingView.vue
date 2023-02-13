<template>
    <div id="body-details">
        <div class="content-details">
            <div class="content" v-if="housing">
                <div v-if="disabledDays.length">
                    <div class="dropdown">
                        <button class="dropdown-button" @click="showList = !showList">
                            Indisponibilités
                            <i :class="showList ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                        </button>
                        <div v-if="showList" class="dropdown-list">
                            <div v-for="(period, index) in disabledDays" :key="index">
                                <p>Logement indisponible du {{ formatDate(period.start) }} au {{
                                    formatDate(period.end)
                                }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <h1 v-if="housing.name">{{ housing.name }}</h1>
                <div id="container-pictures-details">
                    <img class="picture-details" v-for="image in housing.media" :key="image.id"
                        :src="'https://localhost' + image.contentUrl" />
                </div>


                <h2 v-if="housing.price">Prix : {{ housing.price }} €</h2>

                <p v-if="housing.description">{{ housing.description }}</p>

                <h2 v-if="housing.category">Catégorie : {{ housing.category.name }} </h2>

                <h2 v-if="housing.latitude && housing.longitude">Position GPS : {{ housing.latitude }} {{
                    housing.longitude
                }}</h2>


                <h3 v-if="housing.longitude">
                    Créé le : {{ new Date(housing.createdAt).toLocaleString() }}
                </h3>

                <DatePicker v-model="date" :disabled-dates="disabledDays" is-range @input="selectRange"
                    @change="submitRange" />

                <button @click="rentThisHousing()">Réserver</button>
            </div>

            <p v-else>Aucun logement n'a été trouvé.</p>
        </div>
    </div>
</template>

<script setup>
import { Housing } from "@/api/housing";
import { ref } from "vue";
import { useRoute } from "vue-router";
import { DatePicker } from "v-calendar";
import "v-calendar/dist/style.css";


const { params } = useRoute();
const slug = ref(params.slug);

const housing = ref(null);
const disabledDays = ref([]);
const date = ref(null);
const showList = ref(false);



async function getData() {
    const filters = [
        { property: "slug", value: slug.value },
        { property: "rentings.status", value: false },
    ];

    const housingApi = new Housing();
    const response = await housingApi.findAll({ page: 1, itemsPerPage: 20, filters });
    housing.value = response["hydra:member"][0] ?? null;

    if (housing.value) {
        disabledDays.value = housing.value.rentings.map((renting) => ({
            start: new Date(renting.dateStart),
            end: new Date(renting.dateEnd),
        }));
    }

}

getData();

function formatDate(date) {
    const formattedDate = new Intl.DateTimeFormat("fr-FR").format(date);
    return formattedDate.replace(/\//g, "/20");
}
function rentThisHousing() {
    alert("test");
}

function selectRange(date) {
    console.log(`Selected range: ${date.start} - ${date.end}`);
}

function submitRange() {
    console.log(`Submitted range: ${date.value.start} - ${date.value.end}`);
}
</script>

<script>
export default {
    name: "HousingView",
    setup() {
        return {
            housing,
            disabledDays,
            date,
            rentThisHousing,
            selectRange,
            submitRange,
            unavailablePeriods,
            formatDate,
        };
    },
};
</script>

<style scoped>
.dropdown {
  position: relative;
}

.dropdown-button {
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: none;
  border: none;
  font-size: 1.2rem;
  padding: 1rem;
  width: 100%;
  text-align: left;
  cursor: pointer;
}

.dropdown-button i {
  margin-left: 1rem;
  transform: translateY(2px);
  transition: transform 0.3s;
}

.dropdown-button i.fa-chevron-up {
  transform: rotate(180deg) translateY(-2px);
}

.dropdown-list {
  position: absolute;
  top: 100%;
  left: 0;
  background-color: white;
  padding: 1rem;
  border: 1px solid #ccc;
  width: 100%;
  z-index: 1;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.dropdown-list p {
  margin: 0;
  margin-bottom: 1rem;
}
</style>
