<template>
  <div class="container mx-auto px-4 py-26">
    <!-- Filter Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
      <div>
        <h1 class="text-3xl font-bold mb-2">Tìm phim</h1>
        <p class="text-gray-400">Tìm kiếm và lọc phim theo thể loại, quốc gia, năm và chất lượng</p>
      </div>
      <div class="flex items-center space-x-4">
        <select v-model="sortBy" @change="applyFilters" class="bg-gray-800 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
          <option value="latest">Mới nhất</option>
          <option value="rating">Đánh giá cao</option>
          <option value="views">Xem nhiều</option>
        </select>
      </div>
    </div>

    <!-- Filter Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
      <!-- Sidebar Filters -->
      <div class="lg:col-span-1 space-y-6">
        <!-- Genre Filter -->
        <div class="bg-gray-800 p-4 rounded-lg">
          <h3 class="text-lg font-semibold mb-4 flex items-center justify-between">
            <span>Thể loại</span>
            <button v-if="selectedGenres.length" @click="selectedGenres = []" class="text-sm text-red-500 hover:text-red-400">
              Xóa tất cả
            </button>
          </h3>
          <div class="space-y-2 max-h-60 overflow-y-auto custom-scrollbar">
            <label v-for="genre in genres" :key="genre.id" class="flex items-center space-x-2 hover:bg-gray-700 p-2 rounded-lg transition-colors">
              <input 
                type="checkbox" 
                :value="genre.id" 
                v-model="selectedGenres"
                @change="applyFilters"
                class="form-checkbox h-4 w-4 text-red-600 rounded focus:ring-red-500"
              >
              <span>{{ genre.name }}</span>
            </label>
          </div>
        </div>

        <!-- Country Filter -->
        <div class="bg-gray-800 p-4 rounded-lg">
          <h3 class="text-lg font-semibold mb-4 flex items-center justify-between">
            <span>Quốc gia</span>
            <button v-if="selectedCountries.length" @click="selectedCountries = []" class="text-sm text-red-500 hover:text-red-400">
              Xóa tất cả
            </button>
          </h3>
          <div class="space-y-2 max-h-60 overflow-y-auto custom-scrollbar">
            <label v-for="country in countries" :key="country.id" class="flex items-center space-x-2 hover:bg-gray-700 p-2 rounded-lg transition-colors">
              <input 
                type="checkbox" 
                :value="country.id" 
                v-model="selectedCountries"
                @change="applyFilters"
                class="form-checkbox h-4 w-4 text-red-600 rounded focus:ring-red-500"
              >
              <span>{{ country.name }}</span>
            </label>
          </div>
        </div>

        <!-- Year Filter -->
        <div class="bg-gray-800 p-4 rounded-lg">
          <h3 class="text-lg font-semibold mb-4 flex items-center justify-between">
            <span>Năm phát hành</span>
            <button v-if="selectedYears.length" @click="selectedYears = []" class="text-sm text-red-500 hover:text-red-400">
              Xóa tất cả
            </button>
          </h3>
          <div class="space-y-2 max-h-60 overflow-y-auto custom-scrollbar">
            <label v-for="year in years" :key="year" class="flex items-center space-x-2 hover:bg-gray-700 p-2 rounded-lg transition-colors">
              <input 
                type="checkbox" 
                :value="year" 
                v-model="selectedYears"
                @change="applyFilters"
                class="form-checkbox h-4 w-4 text-red-600 rounded focus:ring-red-500"
              >
              <span>{{ year }}</span>
            </label>
          </div>
        </div>

        <!-- Quality Filter -->
        <div class="bg-gray-800 p-4 rounded-lg">
          <h3 class="text-lg font-semibold mb-4 flex items-center justify-between">
            <span>Chất lượng</span>
            <button v-if="selectedQualities.length" @click="selectedQualities = []" class="text-sm text-red-500 hover:text-red-400">
              Xóa tất cả
            </button>
          </h3>
          <div class="space-y-2">
            <label v-for="quality in qualities" :key="quality" class="flex items-center space-x-2 hover:bg-gray-700 p-2 rounded-lg transition-colors">
              <input 
                type="checkbox" 
                :value="quality" 
                v-model="selectedQualities"
                @change="applyFilters"
                class="form-checkbox h-4 w-4 text-red-600 rounded focus:ring-red-500"
              >
              <span>{{ quality }}</span>
            </label>
          </div>
        </div>

        <!-- Reset Filters Button -->
        <button 
          @click="resetFilters"
          class="w-full bg-gray-700 text-white py-3 rounded-lg hover:bg-gray-600 transition-colors duration-200"
        >
          Đặt lại bộ lọc
        </button>
      </div>

      <!-- Movie Grid -->
      <div class="lg:col-span-3">
        <!-- Loading State -->
        <div v-if="loading" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
          <div v-for="n in 8" :key="n" class="animate-pulse">
            <div class="aspect-[2/3] bg-gray-800 rounded-lg"></div>
            <div class="h-4 bg-gray-800 rounded mt-2 w-3/4"></div>
            <div class="h-4 bg-gray-800 rounded mt-2 w-1/2"></div>
          </div>
        </div>

        <!-- Movie Results -->
        <div v-else class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
          <div v-for="movie in movies" :key="movie.id" class="movie-card">
            <router-link :to="'/movie/' + movie.slug">
              <div class="relative aspect-[2/3] rounded-lg overflow-hidden group">
                <img 
                  :src="movie.poster" 
                  :alt="movie.title"
                  class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110"
                />
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent">
                  <div class="absolute bottom-0 left-0 right-0 p-4">
                    <h3 class="text-white font-semibold line-clamp-2">{{ movie.title }}</h3>
                    <p class="text-gray-300 text-sm mt-1">{{ movie.release_year }}</p>
                  </div>
                </div>
                <!-- Quality badge -->
                <div class="absolute top-2 right-2 bg-red-500 text-white text-xs px-2 py-1 rounded">
                  {{ movie.quality }}
                </div>
                <!-- Episode badge for series -->
                <div v-if="movie.is_series" class="absolute top-2 left-2 bg-blue-500 text-white text-xs px-2 py-1 rounded">
                  {{ movie.current_episode }}/{{ movie.total_episodes }}
                </div>
              </div>
            </router-link>
          </div>
        </div>

        <!-- No Results -->
        <div v-if="!loading && movies.length === 0" class="text-center py-12">
          <div class="text-gray-400 text-lg mb-4">
            <i class="fas fa-film text-4xl mb-4"></i>
            <p>Không tìm thấy phim phù hợp với bộ lọc</p>
          </div>
          <button 
            @click="resetFilters"
            class="text-red-500 hover:text-red-400"
          >
            Đặt lại bộ lọc
          </button>
        </div>

        <!-- Pagination -->
        <div v-if="movies.length > 0" class="mt-8 flex justify-center">
          <el-pagination
            background
            layout="prev, pager, next"
            :total="totalItems"
            :page-size="perPage"
            :current-page="currentPage"
            @current-change="changePage"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import axios from 'axios'
import { onMounted, ref } from 'vue'

// Data
const genres = ref([])
const countries = ref([])
const movies = ref([])
const loading = ref(false)
const currentPage = ref(1)
const perPage = 20
const totalItems = ref(0)

// Filter states
const selectedGenres = ref([])
const selectedCountries = ref([])
const selectedYears = ref([])
const selectedQualities = ref([])
const sortBy = ref('latest')

// Years range
const years = Array.from({length: 24}, (_, i) => new Date().getFullYear() - i)

// Quality options
const qualities = ['HD', 'FullHD', '4K', 'BluRay']

// Fetch initial data
const fetchGenres = async () => {
  try {
    const res = await axios.get('/api/genres')
    genres.value = res.data
  } catch (error) {
    console.error('Error fetching genres:', error)
  }
}

const fetchCountries = async () => {
  try {
    const res = await axios.get('/api/countries')
    countries.value = res.data
  } catch (error) {
    console.error('Error fetching countries:', error)
  }
}

const fetchMovies = async () => {
  loading.value = true
  try {
    const params = {
      page: currentPage.value,
      per_page: perPage,
      genres: selectedGenres.value,
      countries: selectedCountries.value,
      years: selectedYears.value,
      qualities: selectedQualities.value,
      sort: sortBy.value
    }
    const res = await axios.get('/api/movies/filter', { params })
    movies.value = res.data.data
    totalItems.value = res.data.total
  } catch (error) {
    console.error('Error fetching movies:', error)
  } finally {
    loading.value = false
  }
}

// Methods
const applyFilters = () => {
  currentPage.value = 1
  fetchMovies()
}

const resetFilters = () => {
  selectedGenres.value = []
  selectedCountries.value = []
  selectedYears.value = []
  selectedQualities.value = []
  sortBy.value = 'latest'
  currentPage.value = 1
  fetchMovies()
}

const changePage = (page) => {
  currentPage.value = page
  fetchMovies()
}

// Lifecycle
onMounted(() => {
  fetchGenres()
  fetchCountries()
  fetchMovies()
})
</script>

<style scoped>
.movie-card {
  transition: transform 0.2s;
}
.movie-card:hover {
  transform: translateY(-5px);
}

.custom-scrollbar {
  scrollbar-width: thin;
  scrollbar-color: #4B5563 #1F2937;
}

.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: #1F2937;
  border-radius: 3px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background-color: #4B5563;
  border-radius: 3px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background-color: #6B7280;
}
</style> 