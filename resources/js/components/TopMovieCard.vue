<template>
  <div class="flex flex-col items-center w-full max-w-[300px] mx-auto">
    <!-- Card poster/banner -->
    <div
      class="relative w-full aspect-[2/3] bg-gray-800 rounded-2xl overflow-hidden shadow-xl group transition-all duration-300 hover:border-4 hover:border-yellow-400 hover:shadow-2xl mb-4"
      :class="''"
    >
      <img :src="movie.poster" :alt="movie.title" class="w-full h-full object-cover rounded-2xl transition-all duration-300 group-hover:brightness-110 group-hover:scale-105" />
      <!-- Badge -->
      <div class="absolute bottom-2 left-2 flex gap-2">
        <span v-if="movie.is_sub" class="bg-blue-600 text-xs font-bold px-2 py-1 rounded text-white shadow">PD. {{ movie.is_sub }}</span>
        <span v-if="movie.is_dubbed" class="bg-green-600 text-xs font-bold px-2 py-1 rounded text-white shadow">TM. {{ movie.is_dubbed }}</span>
      </div>
    </div>
    <!-- Số thứ tự lớn và thông tin phim -->
    <div class="flex flex-row items-start w-full mt-2">
      <div class="flex-shrink-0 mr-6">
        <span class="text-6xl font-extrabold text-yellow-200 opacity-90 drop-shadow-lg select-none leading-none" style="font-style: italic;">{{ rank }}</span>
      </div>
      <div class="flex flex-col justify-center w-full min-w-0">
        <router-link
          :to="{ name: 'movie', params: { slug: movie.slug }}"
          class="text-lg font-bold text-white mb-1 truncate transition-colors duration-200 hover:text-yellow-400 active:text-yellow-500 cursor-pointer"
          style="text-decoration: none;"
        >
          {{ movie.title }}
        </router-link>
        <p class="text-sm text-gray-400 mb-1 truncate">{{ movie.original_title }}</p>
        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-gray-300 mb-0.5">
          <span v-if="movie.age_rating" class="">{{ movie.age_rating }}</span>
          <span v-if="movie.season">• Phần {{ movie.season }}</span>
          <span v-if="movie.episode">• Tập {{ movie.episode }}</span>
          <span v-if="movie.imdb_rating" class="flex items-center gap-1">• <i class="fa-solid fa-star text-yellow-400"></i> {{ movie.imdb_rating }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  movie: Object,
  rank: Number
})
</script> 