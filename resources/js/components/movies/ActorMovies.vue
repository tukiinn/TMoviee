<template>
  <div class="container mx-auto px-4 py-8 pt-header">
    <div class="flex items-center justify-between mb-8">
      <div>
        <h1 class="text-3xl font-bold">{{ actor?.name || 'Diễn viên' }}</h1>
        <div v-if="actor" class="mt-4 flex items-center gap-4">
          <img 
            v-if="actor.avatar" 
            :src="actor.avatar" 
            :alt="actor.name"
            class="w-32 h-32 rounded-full object-cover"
          >
          <div class="flex-1">
            <p v-if="actor.biography" class="text-gray-400 mb-2">{{ actor.biography }}</p>
            <div class="flex items-center gap-4 text-sm text-gray-400">
              <span v-if="actor.birth_date">
                Sinh ngày: {{ new Date(actor.birth_date).toLocaleDateString() }}
              </span>
              <span v-if="actor.birth_place">
                Nơi sinh: {{ actor.birth_place }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
      <div v-for="n in 12" :key="n" class="animate-pulse">
        <div class="aspect-[2/3] bg-gray-800 rounded-lg"></div>
      </div>
    </div>

    <!-- Movies Grid -->
    <div v-else-if="actor?.movies?.length > 0" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
      <MovieCard 
        v-for="movie in actor.movies" 
        :key="movie.id" 
        :movie="movie"
      />
    </div>

    <!-- Empty State -->
    <div v-else class="text-center py-12">
      <p class="text-gray-400">Không tìm thấy phim nào.</p>
    </div>
  </div>
</template>

<script setup>
import axios from 'axios'
import { onMounted, ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import MovieCard from './MovieCard.vue'

const route = useRoute()
const actor = ref(null)
const loading = ref(true)

const fetchData = async () => {
  loading.value = true
  try {
    const response = await axios.get(`/api/actors/${route.params.slug}`)
    actor.value = response.data
  } catch (error) {
    console.error('Error fetching data:', error)
  } finally {
    loading.value = false
  }
}

// Watch for route changes
watch(
  () => route.params.slug,
  (newSlug) => {
    if (newSlug) {
      fetchData()
    }
  }
)

onMounted(() => {
  fetchData()
})
</script> 