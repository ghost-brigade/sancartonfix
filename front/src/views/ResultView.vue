<!-- <template>
  <div id="result">
    <div id="container-result">
      <div v-for="housing in housings" :key="housing.id" class="housings">
        <carousel :slides="housing.media" :interval="1000" controls indicators>
          <template v-for="(slide, index) in housing.media" v-slot:item="{ slide, currentSlide, index, direction }">
            <carousel-item :imgUrl="slide.contentUrl" :current-slide="currentSlide" :index="index"
              :direction="direction"></carousel-item>
          </template>
        </carousel>
        <h3>{{ housing.name }}</h3>
        <p>{{ housing.price }}€/nuit</p>
      </div>
    </div>
  </div>

</template> -->

<template>
  <div id="result">
    <div id="container-result">
      <div v-for="housing in housings" :key="housing.id" class="housings">
        <carousel :slides="housing.media" :interval="1000" controls indicators>
        </carousel>
        <h3>{{ housing.name }}</h3>
        <p>{{ housing.price }}€/nuit</p>
      </div>
    </div>
  </div>

</template>



<script>
import { defineComponent } from 'vue';
import { Housing } from '@/api/housing';
import Carousel from "../components/carousels/Carousel.vue";
import CarouselControls from "../components/carousels/CarouselControls.vue";
import CarouselIndicators from "../components/carousels/CarouselIndicators.vue";
import CarouselItem from "../components/carousels/CarouselItem.vue";

export default defineComponent({
  name: 'HousingCarousel',
  components: {
    Carousel,
    CarouselControls,
    CarouselIndicators,
    CarouselItem
  },
  props: {
    category: {
      type: String,
      required: true
    },
    city: {
      type: String,
      required: true
    }
  },
  data() {
    return {
      housings: []
    }
  },
  created() {
    this.getHousings()
  },
  methods: {
    async getHousings() {
      const housingApi = new Housing();
      const category = this.$route.params.category;
      console.log(category);
      const city = this.$route.params.city
      const filters = [
        // { property: "name", value: category.charAt(0).toUpperCase() + category.slice(1) },
      ];
      const response = await housingApi.findAll({page : 1, itemPerPage: 20, filters});
      this.housings = response['hydra:member'];
      console.log(this.housings);

    },
  }
})
</script>
