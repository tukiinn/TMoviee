<template>
  <div class="relative" v-click-outside="closeDropdown">
    <button @click="toggleDropdown" class="hover:text-red-500 flex items-center">
      Thể loại  <span class="ml-1" style="font-size: 0.6em;"> ▼</span>
    </button>
    <div v-if="show" class="absolute left-0 mt-2 w-[600px] bg-gray-800 rounded-lg shadow-lg p-8 grid grid-cols-4 gap-4 z-50">
      <router-link 
        v-for="genre in genres" 
        :key="genre.id"
        :to="{ name: 'genre', params: { slug: genre.slug }}"
        class="cursor-pointer hover:text-red-400"
        @click="closeDropdown"
      >
        {{ genre.name }}
      </router-link>
    </div>
  </div>
</template>

<script setup>
import axios from 'axios'
import { onMounted, ref } from 'vue'

const show = ref(false)
const genres = ref([])

const toggleDropdown = () => {
  show.value = !show.value
}

const closeDropdown = () => {
  show.value = false
}

const fetchGenres = async () => {
  try {
    const res = await axios.get('/api/genres')
    genres.value = res.data
  } catch (e) {
    console.error('Error fetching genres:', e)
    genres.value = []
  }
}

onMounted(fetchGenres)
</script> 