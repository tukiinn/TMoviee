<template>
  <div class="container mx-auto px-4 py-8 pt-header">
    <div class="mb-8">
      <h1 class="text-2xl font-bold mb-2">Kết quả tìm kiếm: "{{ searchQuery }}"</h1>
      <p class="text-gray-400">Tìm thấy {{ total }} kết quả</p>
    </div>

    <!-- Loading state -->
    <div v-if="loading" class="flex justify-center items-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-red-500"></div>
    </div>

    <!-- No results -->
    <div v-else-if="!movies.length" class="text-center py-12">
      <div class="text-gray-400 text-lg mb-4">
        <i class="fas fa-search text-4xl mb-4"></i>
        <p>Không tìm thấy kết quả nào cho "{{ searchQuery }}"</p>
      </div>
      <p class="text-gray-500">Hãy thử tìm kiếm với từ khóa khác</p>
    </div>

    <!-- Results grid -->
    <div v-else class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-6">
      <div v-for="movie in movies" :key="movie.id" class="movie-card">
        <router-link :to="'/movie/' + movie.slug" class="block">
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

    <!-- Pagination -->
    <div v-if="total > 0" class="mt-8 flex justify-center">
      <el-pagination
        background
        layout="prev, pager, next"
        :total="total"
        :page-size="perPage"
        :current-page="currentPage"
        @current-change="handlePageChange"
      />
    </div>
  </div>
</template>

<script setup>
import axios from 'axios';
import { ref, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';

const route = useRoute();
const router = useRouter();
const movies = ref([]);
const loading = ref(false);
const total = ref(0);
const currentPage = ref(1);
const perPage = 20;
const searchQuery = ref('');

const fetchMovies = async () => {
  loading.value = true;
  try {
    const response = await axios.get('/api/movies/search', {
      params: {
        q: searchQuery.value,
        page: currentPage.value,
        per_page: perPage
      }
    });
    movies.value = response.data.data;
    total.value = response.data.total;
  } catch (error) {
    console.error('Error fetching search results:', error);
  } finally {
    loading.value = false;
  }
};

const handlePageChange = (page) => {
  currentPage.value = page;
  router.push({
    query: { ...route.query, page }
  });
};

watch(
  () => route.query.q,
  (newQuery) => {
    if (newQuery) {
      searchQuery.value = newQuery;
      currentPage.value = parseInt(route.query.page) || 1;
      fetchMovies();
    }
  },
  { immediate: true }
);

watch(
  () => route.query.page,
  (newPage) => {
    if (newPage) {
      currentPage.value = parseInt(newPage);
      fetchMovies();
    }
  }
);
</script>

<style scoped>
.movie-card {
  transition: transform 0.3s;
}
.movie-card:hover {
  transform: translateY(-5px);
}
</style> 