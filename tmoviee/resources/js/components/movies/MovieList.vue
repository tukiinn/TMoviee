<template>
  <div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-8">
      <h1 class="text-3xl font-bold">{{ title }}</h1>
      <div class="flex items-center gap-4">
        <select 
          v-model="sortBy"
          class="bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white"
          @change="fetchMovies"
        >
          <option value="latest">Mới nhất</option>
          <option value="rating">Đánh giá cao</option>
          <option value="views">Xem nhiều</option>
        </select>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
      <div v-for="n in 12" :key="n" class="animate-pulse">
        <div class="aspect-[2/3] bg-gray-800 rounded-lg"></div>
      </div>
    </div>

    <!-- Movies Grid -->
    <div v-else-if="movies.length > 0" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
      <movie-card 
        v-for="movie in movies" 
        :key="movie.id" 
        :movie="movie"
      />
    </div>

    <!-- Empty State -->
    <div v-else class="text-center py-12">
      <p class="text-gray-400">Không tìm thấy phim nào.</p>
    </div>

    <!-- Pagination -->
    <div v-if="totalPages > 1" class="mt-8 flex justify-center">
      <div class="flex gap-2">
        <button 
          v-for="page in totalPages" 
          :key="page"
          :class="[
            'px-4 py-2 rounded-lg',
            currentPage === page 
              ? 'bg-red-600 text-white' 
              : 'bg-gray-700 text-gray-300 hover:bg-gray-600'
          ]"
          @click="changePage(page)"
        >
          {{ page }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import MovieCard from './MovieCard.vue'

const props = defineProps({
  title: {
    type: String,
    required: true
  },
  fetchMovies: {
    type: Function,
    required: true
  }
})

const movies = ref([])
const loading = ref(true)
const currentPage = ref(1)
const totalPages = ref(1)
const sortBy = ref('latest')

const loadMovies = async () => {
  loading.value = true
  try {
    const response = await props.fetchMovies(currentPage.value, sortBy.value)
    movies.value = response.data
    totalPages.value = response.last_page
  } catch (error) {
    console.error('Error loading movies:', error)
  } finally {
    loading.value = false
  }
}

const changePage = (page) => {
  currentPage.value = page
  loadMovies()
}

onMounted(() => {
  loadMovies()
})
</script> 