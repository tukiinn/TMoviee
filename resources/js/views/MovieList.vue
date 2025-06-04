<template>
  <div class="pt-header">
    <!-- Main Content -->
    <section class="flex-1">
      <div class="cards-grid-wrapper px-4 sm:px-8 cards-row wide mx-auto">
        <h1 class="text-3xl font-bold leading-none mb-2">{{ type === 'single' ? 'Phim Lẻ' : 'Phim Bộ' }}</h1>
        <div class="pb-4">
          <button
            class="flex items-center gap-2 bg-gray-800 text-white px-4 py-1 rounded-lg hover:bg-gray-700 leading-none"
            @click="showFilterBar = !showFilterBar"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 -mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707l-7 7V21a1 1 0 01-1.447.894l-4-2A1 1 0 017 19v-5.293l-7-7A1 1 0 013 4z" />
            </svg>
            Bộ lọc
          </button>
        </div>
            <!-- Filter Bar (Slide Down) -->
    <div v-if="showFilterBar" class="bg-[#232846] rounded-xl shadow-lg p-6 mb-8 w-full max-w-6xl relative mt-0 text-left mx-auto">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold text-white">Bộ lọc</h2>
        <button 
          @click="clearAllFilters"
          class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
          :disabled="!hasActiveFilters"
        >
          Xóa tất cả
        </button>
      </div>
      <div class="flex flex-wrap gap-4 items-center mb-3">
        <span class="font-semibold text-base text-white">Quốc gia:</span>
        <div class="flex flex-wrap gap-4 items-center">
          <button v-for="country in countries" :key="country.id" @click="selectCountry(country.id)"
            :class="['px-3 py-1 rounded-lg transition-colors', selectedCountries.includes(country.id) ? 'bg-yellow-400 text-gray-900 font-bold' : 'bg-transparent text-white hover:bg-gray-700']">
            {{ country.name }}
          </button>
        </div>
      </div>
      <div class="flex flex-wrap gap-4 items-center mb-3">
        <span class="font-semibold text-base text-white">Thể loại:</span>
        <div class="flex flex-wrap gap-4 items-center">
          <button v-for="genre in genres" :key="genre.id" @click="selectGenre(genre.id)"
            :class="['px-3 py-1 rounded-lg transition-colors', selectedGenres.includes(genre.id) ? 'bg-yellow-400 text-gray-900 font-bold' : 'bg-transparent text-white hover:bg-gray-700']">
            {{ genre.name }}
          </button>
        </div>
      </div>
      <div class="flex flex-wrap gap-4 items-center mb-3">
        <span class="font-semibold text-base text-white">Năm:</span>
        <div class="flex flex-wrap gap-4 items-center">
          <button v-for="year in years" :key="year" @click="selectYear(year)"
            :class="['px-3 py-1 rounded-lg transition-colors', selectedYears.includes(year) ? 'bg-yellow-400 text-gray-900 font-bold' : 'bg-transparent text-white hover:bg-gray-700']">
            {{ year }}
          </button>
        </div>
      </div>
      <div class="flex flex-wrap gap-4 items-center mb-3">
        <span class="font-semibold text-base text-white">Chất lượng:</span>
        <div class="flex flex-wrap gap-4 items-center">
          <button v-for="quality in qualities" :key="quality" @click="selectQuality(quality)"
            :class="['px-3 py-1 rounded-lg transition-colors', selectedQualities.includes(quality) ? 'bg-yellow-400 text-gray-900 font-bold' : 'bg-transparent text-white hover:bg-gray-700']">
            {{ quality }}
          </button>
        </div>
      </div>
      <div class="flex flex-wrap gap-4 items-center">
        <span class="font-semibold text-base text-white">Sắp xếp:</span>
        <div class="flex flex-wrap gap-4 items-center">
          <button v-for="option in sortOptions" :key="option.value" @click="sort = option.value; fetchMovies()"
            :class="['px-3 py-1 rounded-lg transition-colors', sort === option.value ? 'bg-yellow-400 text-gray-900 font-bold' : 'bg-transparent text-white hover:bg-gray-700']">
            {{ option.label }}
          </button>
        </div>
      </div>
    </div>
        <LoadingSpinner v-if="loading" />
        <template v-else>
          <div v-if="movies.length === 0" class="text-center py-8">
            <span class="text-gray-400">Chưa có phim nào.</span>
          </div>
          <!-- Responsive grid: 2 (mobile), 3 (sm), 4 (md), 6 (lg), 7 (xl) -->
          <div v-else class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-7 gap-10 fluid-gap">
            <MovieCard v-for="movie in movies" :key="movie.id" :movie="movie" :showTypeTag="false" />
          </div>
        </template>
        <!-- Pagination -->
        <div v-if="!loading && movies.length > 0 && totalPages > 1" class="mt-8 flex justify-center">
          <div class="flex space-x-2">
            <button 
              v-for="page in totalPages" 
              :key="page"
              @click="currentPage = page; fetchMovies()"
              :class="[
                'px-4 py-2 rounded-lg',
                currentPage === page 
                  ? 'bg-blue-600 text-white' 
                  : 'bg-gray-800 text-white hover:bg-gray-700'
              ]"
            >
              {{ page }}
            </button>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import LoadingSpinner from '@/components/LoadingSpinner.vue'
import MovieCard from '@/components/movies/MovieCard.vue'
import axios from 'axios'
import { ElMessage } from 'element-plus'
import debounce from 'lodash/debounce'
import { computed, onMounted, ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const props = defineProps({
  type: {
    type: String,
    required: true
  }
})

const route = useRoute()
const router = useRouter()
const movies = ref([])
const sort = ref('latest')
const sortOptions = [
  { value: 'latest', label: 'Mới nhất' },
  { value: 'rating', label: 'Điểm IMDb' },
  { value: 'views', label: 'Lượt xem' }
]
const currentPage = ref(1)
const totalPages = ref(1)
const loading = ref(true)
const showFilterBar = ref(false)

// Filter states
const genres = ref([])
const countries = ref([])
const years = Array.from({length: 12}, (_, i) => new Date().getFullYear() - i)
const qualities = ['HD', 'FullHD', '4K', 'BluRay']
const selectedGenres = ref([])
const selectedCountries = ref([])
const selectedYears = ref([])
const selectedQualities = ref([])

const fetchGenres = async () => {
  try {
    const res = await axios.get('/api/genres')
    genres.value = res.data
  } catch (error) {
    genres.value = []
  }
}
const fetchCountries = async () => {
  try {
    const res = await axios.get('/api/countries')
    countries.value = res.data
  } catch (error) {
    countries.value = []
  }
}

// Computed properties
const hasActiveFilters = computed(() => {
  return selectedGenres.value.length > 0 ||
    selectedCountries.value.length > 0 ||
    selectedYears.value.length > 0 ||
    selectedQualities.value.length > 0
})

const fetchMovies = async () => {
  try {
    loading.value = true;
    const params = {
      page: currentPage.value,
      sort: sort.value
    };

    if (selectedGenres.value.length > 0) {
      params.genres = selectedGenres.value;
    }
    if (selectedCountries.value.length > 0) {
      params.countries = selectedCountries.value;
    }
    if (selectedYears.value.length > 0) {
      params.years = selectedYears.value;
    }
    if (selectedQualities.value.length > 0) {
      params.qualities = selectedQualities.value;
    }

    console.log('Filter params:', params);

    const response = await axios.get(`/api/movies/${props.type}`, { params });
    console.log('API Response:', response.data);
    movies.value = response.data.data;
    totalPages.value = response.data.last_page;
    currentPage.value = response.data.current_page;

    // Update URL with current filters
    const queryParams = new URLSearchParams();
    if (currentPage.value > 1) queryParams.set('page', currentPage.value);
    if (sort.value !== 'latest') queryParams.set('sort', sort.value);
    if (selectedGenres.value.length > 0) queryParams.set('genres', selectedGenres.value.join(','));
    if (selectedCountries.value.length > 0) queryParams.set('countries', selectedCountries.value.join(','));
    if (selectedYears.value.length > 0) queryParams.set('years', selectedYears.value.join(','));
    if (selectedQualities.value.length > 0) queryParams.set('qualities', selectedQualities.value.join(','));

    const newUrl = `/${props.type}${queryParams.toString() ? '?' + queryParams.toString() : ''}`;
    window.history.pushState({}, '', newUrl);
  } catch (error) {
    console.error('Error fetching movies:', error);
    ElMessage.error('Có lỗi xảy ra khi tải danh sách phim');
  } finally {
    loading.value = false;
  }
};

// Debounced version of fetchMovies
const debouncedFetchMovies = debounce(fetchMovies, 300)

// Function to update URL with current filters
const updateURL = () => {
  const query = {
    page: currentPage.value > 1 ? currentPage.value : undefined,
    sort: sort.value !== 'latest' ? sort.value : undefined
  }

  // Chuyển đổi Proxy Array thành Array thường trước khi join
  if (selectedGenres.value.length > 0) {
    query.genres = [...selectedGenres.value].join(',')
  }
  if (selectedCountries.value.length > 0) {
    query.countries = [...selectedCountries.value].join(',')
  }
  if (selectedYears.value.length > 0) {
    query.years = [...selectedYears.value].join(',')
  }
  if (selectedQualities.value.length > 0) {
    query.qualities = [...selectedQualities.value].join(',')
  }

  // Remove undefined values
  Object.keys(query).forEach(key => query[key] === undefined && delete query[key])

  // Update URL without triggering a page reload
  const path = `/${props.type}`
  router.replace({ path, query })
}

// Update filter functions
function selectGenre(id) {
  const index = selectedGenres.value.indexOf(id)
  if (index === -1) {
    selectedGenres.value.push(id)
  } else {
    selectedGenres.value.splice(index, 1)
  }
  currentPage.value = 1
  updateURL()
  debouncedFetchMovies()
}

function selectCountry(id) {
  const index = selectedCountries.value.indexOf(id)
  if (index === -1) {
    selectedCountries.value.push(id)
  } else {
    selectedCountries.value.splice(index, 1)
  }
  currentPage.value = 1
  updateURL()
  debouncedFetchMovies()
}

function selectYear(year) {
  const index = selectedYears.value.indexOf(year)
  if (index === -1) {
    selectedYears.value.push(year)
  } else {
    selectedYears.value.splice(index, 1)
  }
  currentPage.value = 1
  updateURL()
  debouncedFetchMovies()
}

function selectQuality(q) {
  const index = selectedQualities.value.indexOf(q)
  if (index === -1) {
    selectedQualities.value.push(q)
  } else {
    selectedQualities.value.splice(index, 1)
  }
  currentPage.value = 1
  updateURL()
  debouncedFetchMovies()
}

// Clear all filters
function clearAllFilters() {
  selectedGenres.value = []
  selectedCountries.value = []
  selectedYears.value = []
  selectedQualities.value = []
  sort.value = 'latest'
  currentPage.value = 1
  updateURL()
  debouncedFetchMovies()
}

// Watch for route changes
watch(() => route.query, (newQuery) => {
  if (newQuery.page) {
    currentPage.value = parseInt(newQuery.page)
  }
  if (newQuery.sort) {
    sort.value = newQuery.sort
  }
  if (newQuery.genres) {
    selectedGenres.value = newQuery.genres.split(',').map(Number)
  }
  if (newQuery.countries) {
    selectedCountries.value = newQuery.countries.split(',').map(Number)
  }
  if (newQuery.years) {
    selectedYears.value = newQuery.years.split(',').map(Number)
  }
  if (newQuery.qualities) {
    selectedQualities.value = newQuery.qualities.split(',')
  }
  debouncedFetchMovies()
}, { immediate: true })

onMounted(() => {
  fetchGenres()
  fetchCountries()
  fetchMovies()
})
</script> 

<style scoped>
.fluid-gap {
  gap: clamp(4px, 0.8vw, 12px);
}
.cards-row.wide {
  max-width: 1900px;
}
.transition-colors {
  transition: all 0.2s ease-in-out;
}
</style> 