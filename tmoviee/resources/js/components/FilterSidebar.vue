<template>
  <div class="fixed inset-0 flex z-50">
    <!-- Overlay -->
    <div class="fixed inset-0 bg-neutral-900/20 z-40" @click="$emit('close')"></div>
    <!-- Sidebar -->
    <aside class="relative w-full max-w-xs min-w-[320px] bg-[#232846] p-6 overflow-y-auto shadow-3xl border border-white/10 ring-2 ring-yellow-400/10 z-50">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold">Bộ lọc</h2>
        <button @click="$emit('close')" class="text-gray-400 hover:text-red-500 text-2xl">&times;</button>
      </div>
      <!-- Genre Filter -->
      <div class="mb-6">
        <h3 class="text-lg font-semibold mb-3">Thể loại</h3>
        <div class="flex flex-wrap gap-2">
          <label v-for="genre in genres" :key="genre.id" class="flex items-center gap-2 bg-gray-800 px-3 py-1 rounded-lg cursor-pointer">
            <input type="checkbox" :value="genre.id" v-model="localSelectedGenres" class="form-checkbox h-4 w-4 text-red-600 rounded focus:ring-red-500">
            <span>{{ genre.name }}</span>
          </label>
        </div>
      </div>
      <!-- Country Filter -->
      <div class="mb-6">
        <h3 class="text-lg font-semibold mb-3">Quốc gia</h3>
        <div class="flex flex-wrap gap-2">
          <label v-for="country in countries" :key="country.id" class="flex items-center gap-2 bg-gray-800 px-3 py-1 rounded-lg cursor-pointer">
            <input type="checkbox" :value="country.id" v-model="localSelectedCountries" class="form-checkbox h-4 w-4 text-red-600 rounded focus:ring-red-500">
            <span>{{ country.name }}</span>
          </label>
        </div>
      </div>
      <!-- Year Filter -->
      <div class="mb-6">
        <h3 class="text-lg font-semibold mb-3">Năm phát hành</h3>
        <div class="flex flex-wrap gap-2">
          <label v-for="year in years" :key="year" class="flex items-center gap-2 bg-gray-800 px-3 py-1 rounded-lg cursor-pointer">
            <input type="checkbox" :value="year" v-model="localSelectedYears" class="form-checkbox h-4 w-4 text-red-600 rounded focus:ring-red-500">
            <span>{{ year }}</span>
          </label>
        </div>
      </div>
      <!-- Quality Filter -->
      <div class="mb-6">
        <h3 class="text-lg font-semibold mb-3">Chất lượng</h3>
        <div class="flex flex-wrap gap-2">
          <label v-for="quality in qualities" :key="quality" class="flex items-center gap-2 bg-gray-800 px-3 py-1 rounded-lg cursor-pointer">
            <input type="checkbox" :value="quality" v-model="localSelectedQualities" class="form-checkbox h-4 w-4 text-red-600 rounded focus:ring-red-500">
            <span>{{ quality }}</span>
          </label>
        </div>
      </div>
      <!-- Sort Filter -->
      <div class="mb-6">
        <h3 class="text-lg font-semibold mb-3">Sắp xếp</h3>
        <select v-model="localSort" class="bg-gray-800 text-white px-4 py-2 rounded-lg w-full">
          <option value="latest">Mới nhất</option>
          <option value="rating">Đánh giá cao</option>
          <option value="views">Xem nhiều</option>
        </select>
      </div>
      <!-- Apply Button -->
      <button @click="apply" class="w-full bg-red-600 text-white py-3 rounded-lg hover:bg-red-700 transition-colors duration-200 font-semibold text-lg mt-4">Áp dụng bộ lọc</button>
    </aside>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
const props = defineProps({
  genres: Array,
  countries: Array,
  years: Array,
  qualities: Array,
  selectedGenres: Array,
  selectedCountries: Array,
  selectedYears: Array,
  selectedQualities: Array,
  sort: String
})

const emit = defineEmits(['close', 'apply'])

const localSelectedGenres = ref([...props.selectedGenres])
const localSelectedCountries = ref([...props.selectedCountries])
const localSelectedYears = ref([...props.selectedYears])
const localSelectedQualities = ref([...props.selectedQualities])
const localSort = ref(props.sort)

watch(() => props.selectedGenres, val => { localSelectedGenres.value = [...val] })
watch(() => props.selectedCountries, val => { localSelectedCountries.value = [...val] })
watch(() => props.selectedYears, val => { localSelectedYears.value = [...val] })
watch(() => props.selectedQualities, val => { localSelectedQualities.value = [...val] })
watch(() => props.sort, val => { localSort.value = val })

const apply = () => {
  emit('apply', {
    genres: localSelectedGenres.value,
    countries: localSelectedCountries.value,
    years: localSelectedYears.value,
    qualities: localSelectedQualities.value,
    sort: localSort.value
  })
}
</script>

<style scoped>
.fixed {
  z-index: 1000;
}
</style> 