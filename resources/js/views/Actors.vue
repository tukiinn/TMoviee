<template>
  <div class="container mx-auto px-4 py-8 pt-header">
    <h1 class="text-3xl font-bold mb-8">Diễn viên</h1>

    <!-- Loading State -->
    <div v-if="loading" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
      <div v-for="n in 12" :key="n" class="animate-pulse">
        <div class="aspect-square bg-gray-800 rounded-lg"></div>
      </div>
    </div>

    <!-- Actors Grid -->
    <div v-else-if="actors.length > 0" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
      <router-link 
        v-for="actor in actors" 
        :key="actor.id"
        :to="{ name: 'actor-movies', params: { slug: actor.slug }}"
        class="group"
      >
        <div class="aspect-square rounded-lg overflow-hidden bg-gray-800">
          <img 
            v-if="actor.avatar" 
            :src="actor.avatar" 
            :alt="actor.name"
            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
          >
          <div v-else class="w-full h-full flex items-center justify-center text-gray-400">
            <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
          </div>
        </div>
        <div class="mt-2">
          <h3 class="font-semibold group-hover:text-red-500 transition-colors">{{ actor.name }}</h3>
          <p v-if="actor.birth_date" class="text-sm text-gray-400">
            {{ new Date(actor.birth_date).getFullYear() }}
          </p>
        </div>
      </router-link>
    </div>

    <!-- Empty State -->
    <div v-else class="text-center py-12">
      <p class="text-gray-400">Không tìm thấy diễn viên nào.</p>
    </div>
  </div>
</template>

<script setup>
import axios from 'axios'
import { onMounted, ref } from 'vue'

const actors = ref([])
const loading = ref(true)

const fetchActors = async () => {
  loading.value = true
  try {
    const res = await axios.get('/api/actors')
    actors.value = res.data.filter(actor => actor && actor.id && actor.slug && actor.name)
  } catch (error) {
    console.error('Error fetching actors:', error)
    actors.value = []
  } finally {
    loading.value = false
  }
}

onMounted(fetchActors)
</script> 