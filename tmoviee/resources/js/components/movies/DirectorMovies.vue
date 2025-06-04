<template>
  <div class="min-h-screen bg-gray-900 pt-20">
    <div class="container mx-auto px-4 py-8">
      <!-- Director Info -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold mb-4">{{ director?.name }}</h1>
        <div class="bg-gray-800 rounded-lg p-6">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Avatar -->
            <div class="md:col-span-1">
              <img 
                v-if="director?.avatar"
                :src="director.avatar"
                :alt="director.name"
                class="w-full rounded-lg"
              >
              <div v-else class="w-full aspect-[3/4] bg-gray-700 rounded-lg flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
              </div>
            </div>
            <!-- Info -->
            <div class="md:col-span-2">
              <div class="space-y-4">
                <div v-if="director?.birth_date">
                  <h3 class="text-gray-400">Ngày sinh</h3>
                  <p>{{ formatDate(director.birth_date) }}</p>
                </div>
                <div v-if="director?.birth_place">
                  <h3 class="text-gray-400">Nơi sinh</h3>
                  <p>{{ director.birth_place }}</p>
                </div>
                <div v-if="director?.biography">
                  <h3 class="text-gray-400">Tiểu sử</h3>
                  <p class="text-gray-300">{{ director.biography }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Movies Grid -->
      <div>
        <h2 class="text-2xl font-bold mb-6">Phim đã đạo diễn</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
          <div v-for="movie in movies" :key="movie.id" class="movie-card">
            <router-link :to="{ name: 'movie', params: { slug: movie.slug }}">
              <div class="relative aspect-[2/3] bg-gray-800 rounded-lg overflow-hidden">
                <img :src="movie.poster" :alt="movie.title" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-black bg-opacity-40 opacity-0 hover:opacity-100 transition-opacity">
                  <div class="absolute bottom-0 left-0 right-0 p-4 text-white">
                    <h3 class="font-bold">{{ movie.title }}</h3>
                    <p class="text-sm">{{ movie.release_year }}</p>
                  </div>
                </div>
              </div>
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import axios from 'axios'
import { onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const director = ref(null)
const movies = ref([])

const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('vi-VN', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const fetchDirector = async () => {
  try {
    const response = await axios.get(`/api/directors/${route.params.slug}`)
    director.value = response.data
  } catch (error) {
    console.error('Error fetching director:', error)
  }
}

const fetchMovies = async () => {
  try {
    const response = await axios.get(`/api/directors/${route.params.slug}/movies`)
    movies.value = response.data
  } catch (error) {
    console.error('Error fetching movies:', error)
  }
}

onMounted(() => {
  fetchDirector()
  fetchMovies()
})
</script>

<style scoped>
.movie-card {
  transition: transform 0.2s;
}
.movie-card:hover {
  transform: scale(1.05);
}
</style> 