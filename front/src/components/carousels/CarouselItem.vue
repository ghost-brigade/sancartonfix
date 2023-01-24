<template>
  <transition :name="transitionEffect">
    <div class="carousel-item" v-show="currentSlide === index" @mouseenter="$emit('mouseenter')"
      @mouseout="$emit('mouseout')">
      <img :src="'https://localhost'+imgUrl" />
    </div>
  </transition>
</template>


<script>
export default {
    props: {
        imgUrl: {
            type: String,
            required: true,
        },
        currentSlide: Number,
        index: Number,
        direction: String
    },
    computed: {
        transitionEffect() {
            return this.direction === "right" ? "slide-out": "slide-in";
    },
  },
};
</script>

<style scoped>
.carousel-item {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
}

.slide-in-enter-active,
.slide-in-leave-active,
.slide-out-enter-active,
.slide-out-leave-active {
  transition: all 0.3s ease-in-out;
}

.slide-in-enter-from {
  transform: translateX(-100%);
}

.slide-in-leave-to {
  transform: translateX(100%);
}

.slide-out-enter-from {
  transform: translateX(100%);
}

.slide-out-leave-to {
  transform: translateX(-100%);
}
</style>