<script setup>
import SearchForm from '../components/forms/SearchForm.vue';
import { Housing } from '../api/housing.js';
import { ref } from 'vue';
import Carousel from "../components/carousels/Carousel.vue";



const housings = ref([]);
async function getDataHousing() {
	const housingsApi = new Housing();

	const responseHousing = await housingsApi.findAll()
	housings.value = responseHousing['hydra:member'];
	console.log(housings.value);
}
getDataHousing()

</script>
<!-- 
<template>
	<div class="housing-grid">
		<div v-for="housing in housings" :key="housing.id" class="housing-card">
			<div v-for="media in housing.media">
				<carousel :slides="slides" :interval="1000" controls indicators></carousel>
				<img :src="'https://localhost'+media.contentUrl" :alt="housing.name">
			</div>
			<h3>{{ housing.name }}</h3>
			<p>{{ housing.location }}</p>
			<p>{{ housing.price }}</p>
		</div>
	</div>
</template> -->
<template>
	<carousel :slides="slides" :interval="1000" controls indicators></carousel>
</template>

  
<script>
export default {
	name: "App",
	components: { Carousel },
	data: () => ({
		housings: ref([]),
	}),

}

</script>
  


