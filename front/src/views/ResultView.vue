<template>
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
        <!-- <p>{{ housing.latitude }}</p>
        <p>{{ housing.longitude }}</p> -->
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
      const housing = new Housing();
      const filters = [
        { property: "category", value: "Carton" },
      ];
      const response = await housing.findAll(1, 20, filters);
      this.housings = response['hydra:member'];
      console.log(this.housings);
      this.slides = this.housings.map(housing => {
        console.log(housing.media)
        return housing.media.map(media => {
          console.log(media.contentUrl)
          return {
            src: 'https://localhost' + media.contentUrl,
            alt: media.filePath
          }
        })
      })
    },

  }
})
</script>
