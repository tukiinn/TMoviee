<template>
  <div class="min-h-screen bg-gray-900 pt-20">
    <!-- Movie Info Section -->
    <div class="container mx-auto px-4 py-8">
      <div v-if="loading" class="flex justify-center items-center min-h-[400px]">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
      </div>
      
      <template v-else-if="movie">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Main Content -->
          <div class="lg:col-span-2">
            <!-- Movie Header -->
            <div class="flex flex-col md:flex-row gap-8 mb-8">
              <!-- Poster -->
              <div class="w-full md:w-1/3">
                <img 
                  :src="movie?.poster" 
                  :alt="movie?.title"
                  class="w-full rounded-lg shadow-lg"
                >
                <!-- Watch Now Button -->
                <router-link 
                  :to="{ name: 'watch', params: { slug: movie?.slug }}"
                  class="mt-4 w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-lg flex items-center justify-center gap-2 transition-colors duration-200"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                  </svg>
                  Xem phim ngay
                </router-link>
                <!-- Favorite Button -->
                <button
  v-if="movie"
  @click="toggleFavorite"
  class="mt-4 w-full flex items-center justify-center gap-2 py-3 px-6 rounded-lg font-bold transition-colors duration-200 border border-red-500 hover:bg-gray-700 hover:text-white text-red-500 bg-gray-900"
>
  <i
    :class="[
      'fa-heart text-2xl transition',
      isFavorite ? 'fa-solid text-red-500' : 'fa-regular text-red-500'
    ]"
  ></i>
  <span>{{ isFavorite ? 'Đã yêu thích' : 'Yêu thích' }}</span>
</button>

              </div>

              <!-- Info -->
              <div class="w-full md:w-2/3">
                <h1 class="text-3xl font-bold mb-4">{{ movie?.title }}</h1>
                
                <!-- Movie Meta -->
                <div class="flex flex-wrap gap-4 mb-6">
                  <span v-if="movie?.imdb_rating" class="bg-yellow-400 text-gray-900 font-bold px-3 py-1 rounded-full text-sm">
                    IMDb {{ movie.imdb_rating }}
                  </span>
                  <span v-if="movie?.quality" class="bg-gray-700 text-white px-3 py-1 rounded-full text-sm">
                    {{ movie.quality }}
                  </span>
                  <span v-if="movie?.release_year" class="bg-gray-700 text-white px-3 py-1 rounded-full text-sm">
                    {{ movie.release_year }}
                  </span>
                  <span v-if="movie?.duration" class="bg-gray-700 text-white px-3 py-1 rounded-full text-sm">
                    {{ movie.duration }} phút
                  </span>
                </div>

                <!-- Description -->
                <div class="bg-gray-800 rounded-lg p-6 mb-8">
                  <h2 class="text-xl font-semibold mb-4">Nội dung phim</h2>
                  <p class="text-gray-300 leading-relaxed">{{ movie?.description }}</p>
                </div>

                <!-- Trailer Section -->
                <div v-if="movie?.trailer_url" class="bg-gray-800 rounded-lg p-6 mb-8">
                  <h2 class="text-xl font-semibold mb-4">Trailer phim</h2>
                  <div class="relative w-full" style="padding-top: 56.25%">
                    <iframe
                      :src="getEmbedUrl(movie.trailer_url)"
                      class="absolute top-0 left-0 w-full h-full rounded-lg"
                      frameborder="0"
                      allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                      allowfullscreen
                    ></iframe>
                  </div>
                </div>

                <!-- Episodes (for series) -->
                <div v-if="movie?.is_series" class="bg-gray-800 rounded-lg p-6 mb-8">
                  <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold">Danh sách tập phim</h2>
                    <div class="flex items-center gap-2">
                      <span class="text-gray-400">Tổng số tập:</span>
                      <span class="font-medium">{{ movie.total_episodes }}</span>
                    </div>
                  </div>

                  <!-- Season Tabs -->
                  <div class="flex gap-2 mb-6 overflow-x-auto pb-2">
                    <button 
                      v-for="season in seasons" 
                      :key="season.number"
                      @click="activeSeason = season.number"
                      :class="[
                        'px-4 py-2 rounded-lg font-medium transition-colors duration-200',
                        activeSeason === season.number 
                          ? 'bg-red-600 text-white' 
                          : 'bg-gray-700 text-gray-300 hover:bg-gray-600'
                      ]"
                    >
                      Mùa {{ season.number }}
                    </button>
                  </div>

                  <!-- Episodes Grid -->
                  <div class="space-y-4">
                    <div v-for="episode in currentSeasonEpisodes" :key="episode.id" 
                      class="bg-gray-700 rounded-lg overflow-hidden hover:bg-gray-600 transition-colors duration-200">
                      <router-link 
                        :to="{ name: 'watch', params: { slug: movie.slug, season: episode.season, episode: episode.episode_number }}" 
                        class="block"
                      >
                        <div class="flex items-center gap-4 p-4">
                          <!-- Thumbnail -->
                          <div class="relative w-40 h-24 flex-shrink-0">
                            <img 
                              :src="episode.thumbnail || movie.poster" 
                              :alt="episode.title" 
                              class="w-full h-full object-cover rounded"
                            >
                            <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center">
                              <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                              </svg>
                            </div>
                          </div>

                          <!-- Info -->
                          <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                              <span class="text-sm font-medium text-gray-400">Tập {{ episode.episode_number }}</span>
                              <span class="text-sm text-gray-400">•</span>
                              <span class="text-sm text-gray-400">{{ episode.duration }} phút</span>
                            </div>
                            <h4 class="font-medium text-lg mb-1 line-clamp-1">{{ episode.title }}</h4>
                            <p class="text-sm text-gray-400 line-clamp-2">{{ episode.description }}</p>
                          </div>
                        </div>
                      </router-link>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Sidebar -->
          <div class="lg:col-span-1">
            <!-- Movie Details -->
            <div class="bg-gray-800 rounded-lg p-6 mb-6">
              <h2 class="text-xl font-semibold mb-4">Thông tin phim</h2>
              <div class="space-y-4">
                <!-- Director -->
                <div v-if="movie?.director">
                  <h3 class="text-gray-400 mb-2">Đạo diễn</h3>
                  <router-link 
                    :to="{ name: 'director-movies', params: { slug: movie.director.slug }}"
                    class="text-white hover:text-red-500 transition-colors duration-200"
                  >
                    {{ movie.director.name }}
                  </router-link>
                </div>

                <!-- Actors -->
                <div v-if="movie?.actors?.length">
                  <h3 class="text-gray-400 mb-2">Diễn viên</h3>
                  <div class="flex flex-wrap gap-2">
                    <router-link
                      v-for="actor in movie.actors"
                      :key="actor.id"
                      :to="{ name: 'actor-movies', params: { slug: actor.slug }}"
                      class="bg-gray-700 hover:bg-gray-600 text-white px-3 py-1 rounded-full text-sm transition-colors duration-200"
                    >
                      {{ actor.name }}
                      <span v-if="actor.pivot?.character_name" class="text-gray-400 ml-1">
                        ({{ actor.pivot.character_name }})
                      </span>
                    </router-link>
                  </div>
                </div>

                <!-- Genres -->
                <div v-if="movie?.genres?.length">
                  <h3 class="text-gray-400 mb-2">Thể loại</h3>
                  <div class="flex flex-wrap gap-2">
                    <router-link
                      v-for="genre in movie.genres"
                      :key="genre.id"
                      :to="{ name: 'genre', params: { slug: genre.slug }}"
                      class="bg-gray-700 hover:bg-gray-600 text-white px-3 py-1 rounded-full text-sm transition-colors duration-200"
                    >
                      {{ genre.name }}
                    </router-link>
                  </div>
                </div>

                <!-- Country -->
                <div v-if="movie?.country">
                  <h3 class="text-gray-400 mb-2">Quốc gia</h3>
                  <router-link 
                    :to="{ name: 'country-movies', params: { slug: movie.country.slug }}"
                    class="text-white hover:text-red-500 transition-colors duration-200"
                  >
                    {{ movie.country.name }}
                  </router-link>
                </div>
              </div>
            </div>

            <!-- Related Movies -->
            <div class="bg-gray-800 rounded-lg p-6">
              <h2 class="text-xl font-semibold mb-4">Phim liên quan</h2>
              <div class="space-y-4">
                <div v-for="relatedMovie in relatedMovies" :key="relatedMovie.id" class="flex gap-4">
                  <router-link :to="{ name: 'movie', params: { slug: relatedMovie.slug }}" class="flex-shrink-0">
                    <img :src="relatedMovie.poster" :alt="relatedMovie.title" class="w-24 h-36 object-cover rounded-lg">
                  </router-link>
                  <div>
                    <router-link 
                      :to="{ name: 'movie', params: { slug: relatedMovie.slug }}"
                      class="font-semibold hover:text-red-500 transition-colors duration-200"
                    >
                      {{ relatedMovie.title }}
                    </router-link>
                    <p class="text-sm text-gray-400">{{ relatedMovie.release_year }}</p>
                    <div class="flex gap-2 mt-2">
                      <span class="text-yellow-400">★ {{ relatedMovie.imdb_rating }}</span>
                      <span class="text-gray-400">{{ relatedMovie.quality }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <CommentSection :movie-slug="movie.slug" />
      </template>

      <div v-else class="text-center py-12">
        <h2 class="text-2xl font-semibold text-gray-300">Không tìm thấy phim</h2>
        <p class="text-gray-400 mt-2">Phim bạn đang tìm kiếm không tồn tại hoặc đã bị xóa.</p>
        <router-link to="/" class="inline-block mt-4 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
          Quay về trang chủ
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import CommentSection from '@/components/CommentSection.vue'
import axios from 'axios'
import { computed, onMounted, ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import { toast } from 'vue3-toastify'

const route = useRoute()
const movie = ref(null)
const relatedMovies = ref([])
const episodes = ref([])
const activeSeason = ref(1)
const isFavorite = ref(false)
const loading = ref(true)

const seasons = computed(() => {
  if (!episodes.value.length) return []
  
  const seasonsMap = {}
  episodes.value.forEach(episode => {
    if (!seasonsMap[episode.season]) {
      seasonsMap[episode.season] = {
        number: episode.season,
        episodes: []
      }
    }
    seasonsMap[episode.season].episodes.push(episode)
  })
  
  return Object.values(seasonsMap).sort((a, b) => a.number - b.number)
})

const currentSeasonEpisodes = computed(() => {
  const season = seasons.value.find(s => s.number === activeSeason.value)
  return season ? season.episodes : []
})

// Function to convert YouTube URL to embed URL
const getEmbedUrl = (url) => {
  if (!url) return ''
  
  // If it's already an embed URL, return as is
  if (url.includes('youtube.com/embed/')) return url
  
  // Handle different YouTube URL formats
  const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/
  const match = url.match(regExp)
  
  if (match && match[2].length === 11) {
    return `https://www.youtube.com/embed/${match[2]}`
  }
  
  return url
}

const fetchMovie = async () => {
  loading.value = true
  try {
    const response = await axios.get(`/api/movies/${route.params.slug}`)
    movie.value = response.data
    
    if (movie.value?.is_series) {
      const episodesResponse = await axios.get(`/api/movies/${route.params.slug}/episodes`)
      episodes.value = episodesResponse.data
    }
  } catch (error) {
    console.error('Error fetching movie:', error)
    movie.value = null
  } finally {
    loading.value = false
  }
}

const fetchRelatedMovies = async () => {
  try {
    const response = await axios.get(`/api/movies/${route.params.slug}/related`)
    relatedMovies.value = response.data
  } catch (error) {
    console.error('Error fetching related movies:', error)
  }
}

const fetchIsFavorite = async () => {
  if (!movie.value) return
  try {
    const token = localStorage.getItem('token')
    if (!token) {
      isFavorite.value = false
      return
    }
    const res = await axios.get(`/api/movies/${movie.value.id}/is-favorite`, {
      headers: { Authorization: `Bearer ${token}` }
    })
    isFavorite.value = res.data.favorite
  } catch (e) {
    isFavorite.value = false
  }
}

const toggleFavorite = async () => {
  const token = localStorage.getItem('token')
  if (!token) {
    toast.error('Bạn cần đăng nhập để sử dụng tính năng này!')
    return
  }
  try {
    if (isFavorite.value) {
      await axios.delete(`/api/movies/${movie.value.id}/favorite`, {
        headers: { Authorization: `Bearer ${token}` }
      })
      isFavorite.value = false
      toast.success('Đã xoá khỏi danh sách yêu thích!')
    } else {
      await axios.post(`/api/movies/${movie.value.id}/favorite`, {}, {
        headers: { Authorization: `Bearer ${token}` }
      })
      isFavorite.value = true
      toast.success('Đã thêm vào danh sách yêu thích!')
    }
  } catch (e) {
    toast.error('Có lỗi xảy ra!')
  }
}

watch(() => route.params.slug, () => {
  fetchMovie()
  fetchRelatedMovies()
})

watch(() => movie.value, () => {
  fetchIsFavorite()
})

onMounted(() => {
  fetchMovie()
  fetchRelatedMovies()
  // Kiểm tra trạng thái yêu thích khi vào trang
  watch(
    () => movie.value,
    () => fetchIsFavorite(),
    { immediate: true }
  )
})
</script>

<style scoped>
video::-webkit-media-controls-panel {
  background-color: rgba(0, 0, 0, 0.5);
}
</style> 