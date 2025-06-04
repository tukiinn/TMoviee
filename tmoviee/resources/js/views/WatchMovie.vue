<template>
  <div class="min-h-screen bg-gray-900 pt-header">
    <!-- Dialog tiếp tục xem -->
    <div v-if="showContinueDialog" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-gray-800 rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-xl font-bold text-white mb-4">Tiếp tục xem phim?</h3>
        <p class="text-gray-300 mb-6">
          Bạn đã xem đến {{ Math.floor(watchProgress.current_time / 60) }}:{{ Math.floor(watchProgress.current_time % 60).toString().padStart(2, '0') }}
        </p>
        <div class="flex gap-4">
          <button 
            @click="continueFromLastPosition"
            class="flex-1 bg-red-600 text-white py-2 rounded-lg hover:bg-red-700 transition-colors"
          >
            Tiếp tục xem
          </button>
          <button 
            @click="startFromBeginning"
            class="flex-1 bg-gray-700 text-white py-2 rounded-lg hover:bg-gray-600 transition-colors"
          >
            Xem lại từ đầu
          </button>
        </div>
      </div>
    </div>

    <div class="container mx-auto px-4 py-6">
      <div class="flex flex-col lg:flex-row gap-8">
        <!-- Main Content -->
        <div class="flex-1 min-w-0">
          <!-- Movie Title -->
          <h2 class="text-xl font-bold mb-2 text-white">Xem phim {{ movie?.title }}</h2>

          <!-- Player -->
          <div class="mb-4">
            <div class="aspect-video w-full rounded-2xl overflow-hidden bg-black shadow-2xl relative">
              <iframe
                v-if="videoUrl"
                :src="videoUrl"
                allowfullscreen
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                class="absolute inset-0 w-full h-full border-0 bg-black"
                @load="onIframeLoad"
              ></iframe>
            </div>
          </div>

          <!-- Movie Info -->
          <div class="flex flex-col md:flex-row gap-6 mb-4">
            <div class="flex gap-4 flex-1">
              <img :src="movie?.poster" :alt="movie?.title" class="w-24 h-36 object-cover rounded-lg shadow-md hidden md:block" />
              <div>
                <div class="flex items-center gap-2 mb-2">
                  <span v-if="movie?.age_rating" class="bg-gray-700 text-white px-2 py-1 rounded text-xs">{{ movie.age_rating }}</span>
                  <span v-if="movie?.quality" class="bg-gray-700 text-white px-2 py-1 rounded text-xs">{{ movie.quality }}</span>
                  <span v-if="movie?.imdb_rating" class="bg-yellow-400 text-gray-900 font-bold px-2 py-1 rounded text-xs">IMDb {{ movie.imdb_rating }}</span>
                  <span v-if="movie?.release_year" class="bg-gray-700 text-white px-2 py-1 rounded text-xs">{{ movie.release_year }}</span>
                  <span v-if="movie?.duration" class="bg-gray-700 text-white px-2 py-1 rounded text-xs">{{ formatDuration(movie.duration) }}</span>
                </div>
                <h1 class="text-2xl font-bold text-white mb-2">{{ movie?.title }}</h1>
                <div class="flex flex-wrap gap-2 mb-2">
                  <router-link
                    v-for="genre in movie?.genres || []"
                    :key="genre.id"
                    :to="{ name: 'genre', params: { slug: genre.slug } }"
                    class="bg-gray-800 text-white px-2 py-1 rounded text-xs hover:bg-gray-700 transition-colors"
                  >
                    {{ genre.name }}
                  </router-link>
                </div>
                <div class="text-gray-300 text-sm mb-2 line-clamp-2">{{ movie?.description }}</div>
                <router-link :to="{ name: 'movie', params: { slug: movie?.slug }}" class="text-blue-400 hover:underline text-xs">Thông tin phim &gt;</router-link>
              </div>
            </div>
          </div>

          <!-- Tabs: Info / Comments -->
          <div class="bg-gray-800 rounded-lg p-4 mb-6">
            <div class="flex gap-4 border-b border-gray-700 mb-4">
              <button
                :class="['pb-2 font-semibold', activeTab === 'info' ? 'text-white border-b-2 border-red-600' : 'text-gray-400']"
                @click="activeTab = 'info'"
              >Thông tin phim</button>
              <button
                :class="['pb-2 font-semibold', activeTab === 'comments' ? 'text-white border-b-2 border-red-600' : 'text-gray-400']"
                @click="activeTab = 'comments'"
              >Bình luận</button>
            </div>
            <div v-if="activeTab === 'info'">
              <div class="text-gray-300 text-sm">{{ movie?.description }}</div>
            </div>
            <div v-else>
              <CommentSection v-if="movie" :movie-slug="movie.slug" @loading="commentLoading = $event" />
            </div>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="w-full lg:w-[320px] flex-shrink-0 flex flex-col gap-6">
          <!-- Actors -->
          <div class="bg-gray-800 rounded-lg p-4">
            <h3 class="text-lg font-semibold text-white mb-4">Diễn viên</h3>
            <div class="flex flex-wrap gap-4">
              <router-link
                v-for="actor in movie?.actors || []"
                :key="actor.id"
                :to="{ name: 'actor-movies', params: { slug: actor.slug } }"
                class="flex flex-col items-center w-20 hover:scale-105 transition-transform"
              >
                <img :src="actor.avatar || 'https://ui-avatars.com/api/?name=' + actor.name" :alt="actor.name" class="w-16 h-16 rounded-full object-cover mb-2 border-2 border-gray-700" />
                <span class="text-xs text-white text-center line-clamp-2">{{ actor.name }}</span>
              </router-link>
            </div>
          </div>
          <!-- Recommendations -->
          <div class="bg-gray-800 rounded-lg p-4">
            <h3 class="text-lg font-semibold text-white mb-4">Đề xuất cho bạn</h3>
            <div class="flex flex-col gap-4">
              <div v-for="rec in recommendations" :key="rec.id" class="flex gap-3 items-center">
                <router-link :to="{ name: 'movie', params: { slug: rec.slug }}" class="flex-shrink-0">
                  <img :src="rec.poster" :alt="rec.title" class="w-12 h-16 object-cover rounded-lg" />
                </router-link>
                <div class="flex-1 min-w-0">
                  <router-link :to="{ name: 'movie', params: { slug: rec.slug }}" class="text-white font-semibold text-sm hover:text-red-500 line-clamp-2">{{ rec.title }}</router-link>
                  <div class="text-xs text-gray-400">{{ rec.release_year }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import CommentSection from '@/components/CommentSection.vue'
import axios from 'axios'
import { onMounted, ref, watch } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const movie = ref(null)
const watchProgress = ref(null)
const showContinueDialog = ref(false)
const recommendations = ref([])
const videoUrl = ref('')
const activeTab = ref('info')
const commentLoading = ref(false)
const videoDuration = ref(0)

// Format thời lượng phim
const formatDuration = (seconds) => {
  if (!seconds) return '00:00'
  const hours = Math.floor(seconds / 3600)
  const minutes = Math.floor((seconds % 3600) / 60)
  const remainingSeconds = Math.floor(seconds % 60)
  
  if (hours > 0) {
    return `${hours}:${minutes.toString().padStart(2, '0')}:${remainingSeconds.toString().padStart(2, '0')}`
  }
  return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`
}

// Lấy thông tin phim
const fetchMovie = async () => {
  try {
    const response = await axios.get(`/api/movies/${route.params.slug}`)
    movie.value = response.data
    if (movie.value.video_url) {
      videoUrl.value = movie.value.video_url
    }
    await fetchRecommendations()
  } catch (error) {
    console.error('Error fetching movie:', error)
  }
}

// Lấy tiến độ xem
const fetchWatchProgress = async () => {
  if (!movie.value) return
  
  // Lấy từ localStorage
  const watchData = JSON.parse(localStorage.getItem(`watch_${movie.value.id}`) || 'null')
  if (watchData) {
    console.log('Found watch progress in localStorage:', watchData)
    watchProgress.value = watchData
    if (watchProgress.value.current_time > 0) {
      showContinueDialog.value = true
    }
  }

  // Nếu có token, lấy từ server
  try {
    const token = localStorage.getItem('token')
    if (token) {
      const res = await axios.get(`/api/watch-history/${movie.value.id}`, {
        headers: { Authorization: `Bearer ${token}` }
      })
      if (res.data) {
        console.log('Watch progress from server:', res.data)
        watchProgress.value = res.data
        if (watchProgress.value.current_time > 0) {
          showContinueDialog.value = true
        }
      }
    }
  } catch (e) {
    console.error('Error fetching watch progress from server:', e)
  }
}

// Lấy phim đề xuất
const fetchRecommendations = async () => {
  try {
    const response = await axios.get(`/api/movies/${route.params.slug}/related`)
    recommendations.value = response.data
  } catch (error) {
    console.error('Error fetching recommendations:', error)
  }
}

// Lưu tiến độ xem
const saveWatchProgress = async (currentTime, duration) => {
  if (!movie.value) return

  const watchData = {
    movie_id: movie.value.id,
    current_time: currentTime,
    duration: duration,
    last_watched_at: new Date().toISOString()
  }
  
  // Lưu cho phim hiện tại
  localStorage.setItem(`watch_${movie.value.id}`, JSON.stringify(watchData))
  
  // Lưu vào danh sách phim đã xem
  const watchHistory = JSON.parse(localStorage.getItem('watch_history') || '[]')
  const existingIndex = watchHistory.findIndex(item => item.movie_id === movie.value.id)
  
  if (existingIndex !== -1) {
    watchHistory[existingIndex] = watchData
  } else {
    watchHistory.push(watchData)
  }
  
  // Giới hạn số lượng phim trong lịch sử (50 phim)
  if (watchHistory.length > 50) {
    watchHistory.shift()
  }
  
  localStorage.setItem('watch_history', JSON.stringify(watchHistory))

  // Nếu có token, lưu lên server
  try {
    const token = localStorage.getItem('token')
    if (token) {
      await axios.post('/api/watch-history', watchData, {
        headers: { Authorization: `Bearer ${token}` }
      })
    }
  } catch (e) {
    console.error('Error saving watch progress to server:', e)
  }
}

// Tiếp tục từ vị trí đã xem
const continueFromLastPosition = () => {
  if (watchProgress.value) {
    const iframe = document.querySelector('iframe')
    if (iframe) {
      iframe.contentWindow.postMessage({
        type: 'seek',
        time: watchProgress.value.current_time
      }, '*')
    }
  }
  showContinueDialog.value = false
}

// Xem lại từ đầu
const startFromBeginning = () => {
  const iframe = document.querySelector('iframe')
  if (iframe) {
    iframe.contentWindow.postMessage({
      type: 'seek',
      time: 0
    }, '*')
  }
  showContinueDialog.value = false
}

// Lắng nghe sự kiện từ iframe
window.addEventListener('message', (event) => {
  try {
    const data = event.data
    if (data && typeof data === 'object') {
      if (data.currentTime !== undefined && data.duration !== undefined) {
        saveWatchProgress(data.currentTime, data.duration)
      }
    }
  } catch (e) {
    console.error('Error processing message:', e)
  }
})

// Lưu tiến độ trước khi rời trang
window.addEventListener('beforeunload', () => {
  if (watchProgress.value) {
    saveWatchProgress(watchProgress.value.current_time, watchProgress.value.duration)
  }
})

// Xử lý khi iframe load xong
const onIframeLoad = () => {
  const iframe = document.querySelector('iframe')
  if (iframe) {
    // Gửi message để lấy thời lượng video
    iframe.contentWindow.postMessage({
      type: 'getDuration'
    }, '*')
  }
}

// Lắng nghe message từ iframe
window.addEventListener('message', (event) => {
  try {
    const data = event.data
    if (data && typeof data === 'object') {
      if (data.type === 'duration' && data.duration) {
        videoDuration.value = data.duration
        // Cập nhật thời lượng vào movie object
        if (movie.value) {
          movie.value.duration = data.duration
        }
      }
    }
  } catch (e) {
    console.error('Error processing message:', e)
  }
})

// Xử lý URL Opstream
const getOpstreamUrl = (url) => {
  try {
    const urlObj = new URL(url)
    if (urlObj.hostname.includes('opstream') || urlObj.hostname.includes('opstream14')) {
      return url
    }
    return url
  } catch (error) {
    console.error('Error parsing URL:', error)
    return url
  }
}

watch([() => route.params.slug], async () => {
  await fetchMovie()
  await fetchWatchProgress()
  await fetchRecommendations()
})

onMounted(async () => {
  await fetchMovie()
  await fetchWatchProgress()
  await fetchRecommendations()
})
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style> 