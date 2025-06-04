<template>
  <div class="relative" v-click-outside="closeDropdown">
    <button @click="toggleDropdown" class="hover:text-red-500 flex items-center">
      Quốc gia <span class="ml-1" style="font-size: 0.6em;"> ▼</span>
    </button>
    <div v-if="show" class="absolute left-0 mt-2 w-64 bg-gray-800 rounded-lg shadow-lg p-6 grid grid-cols-2 gap-4 z-50">
      <router-link 
        v-for="country in countries" 
        :key="country.id"
        :to="{ name: 'country-movies', params: { slug: country.slug }}"
        class="cursor-pointer hover:text-red-400"
        @click="closeDropdown"
      >
        {{ country.name }}
      </router-link>
    </div>
  </div>
</template>

<script setup>
import axios from 'axios'
import { onMounted, ref } from 'vue'

const show = ref(false)
const countries = ref([])

const toggleDropdown = () => {
  show.value = !show.value
}

const closeDropdown = () => {
  show.value = false
}

const fetchCountries = async () => {
  try {
    const res = await axios.get('/api/countries')
    countries.value = res.data
  } catch (e) {
    console.error('Error fetching countries:', e)
    countries.value = []
  }
}

onMounted(fetchCountries)
</script> 