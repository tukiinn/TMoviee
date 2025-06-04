<template>
  <div class="container mx-auto px-4 py-8 pt-header">
    <div class="flex items-center justify-between mb-8">
      <div>
        <h1 class="text-3xl font-bold">{{ country?.name || 'Quốc gia' }}</h1>
        <p v-if="country?.description" class="text-gray-400 mt-2">{{ country.description }}</p>
      </div>
      <div class="flex items-center gap-4">
        <select 
          v-model="sortBy"
          class="bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white"
          @change="fetchData"
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
      <MovieCard 
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
import axios from 'axios'
import { onMounted, ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import MovieCard from './MovieCard.vue'

const route = useRoute()
const movies = ref([])
const country = ref(null)
const loading = ref(true)
const currentPage = ref(1)
const totalPages = ref(1)
const sortBy = ref('latest')

const fetchData = async () => {
  loading.value = true
  try {
    const [countryResponse, moviesResponse] = await Promise.all([
      axios.get(`/api/countries/${route.params.slug}`),
      axios.get(`/api/movies/country/${route.params.slug}`, {
        params: {
          page: currentPage.value,
          sort: sortBy.value
        }
      })
    ])
    country.value = countryResponse.data
    movies.value = moviesResponse.data.data
    totalPages.value = moviesResponse.data.last_page
  } catch (error) {
    console.error('Error fetching data:', error)
  } finally {
    loading.value = false
  }
}

const changePage = (page) => {
  currentPage.value = page
  fetchData()
}

// Watch for route changes
watch(
  () => route.params.slug,
  (newSlug) => {
    if (newSlug) {
      currentPage.value = 1 // Reset to first page
      fetchData()
    }
  }
)

onMounted(() => {
  fetchData()
})
</script> 