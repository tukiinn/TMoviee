<template>
  <Swiper
    :slides-per-view="5"
    :space-between="24"
    :navigation="false"
    class="mySwiper"
  >
    <SwiperSlide v-for="(movie, idx) in movies" :key="movie.id">
      <TopMovieCard :movie="movie" :rank="idx + 1" />
    </SwiperSlide>
  </Swiper>
</template>

<script setup>
import 'swiper/css'
import 'swiper/css/navigation'
import { Swiper, SwiperSlide } from 'swiper/vue'
import TopMovieCard from './TopMovieCard.vue'

const props = defineProps({ movies: Array })
</script>

<style scoped>
.mySwiper {
  padding-bottom: 32px;
}
</style> 