<template>
  <div>
    <!-- Banner -->
    <div class="relative h-[700px] overflow-hidden">
      <transition :name="slideDirection === 'ltr' ? 'banner-fade-slide-ltr' : 'banner-fade-slide-rtl'" mode="out-in">
        <div class="banner-bg" :key="selectedBannerIndex"
          :style="`background: url('${currentBanner.banner || fallbackBanner}') center/cover no-repeat; position:absolute; inset:0; z-index:0;`">
          <!-- Overlay chỉ blur nhẹ ở các góc -->
          <div class="pointer-events-none absolute inset-0">
            <div class="absolute inset-0"
              style="background:radial-gradient(circle at 0% 0%,rgba(30,41,59,0.5) 0%,transparent 60%),
                             radial-gradient(circle at 100% 0%,rgba(30,41,59,0.5) 0%,transparent 60%),
                             radial-gradient(circle at 0% 100%,rgba(30,41,59,0.5) 0%,transparent 60%),
                             radial-gradient(circle at 100% 100%,rgba(30,41,59,0.5) 0%,transparent 60%);filter:blur(8px);">
            </div>
            <div class="absolute inset-0 bg-gradient-to-t from-gray-900/70 to-transparent"></div>
          </div>
        </div>
      </transition>
      <!-- Gradient chuyển tiếp ở cuối banner -->
      <div class="absolute left-0 right-0 bottom-0 h-20 z-20 pointer-events-none bg-gradient-to-b from-transparent to-[#181f2a]"></div>
      <div class="relative z-10 banner-content h-full flex items-end">
        <transition :name="infoDirection === 'ltr' ? 'banner-fade-slide-ltr' : 'banner-fade-slide-rtl'" mode="out-in">
          <div class="banner-info max-w-2xl pb-16 px-8" :key="'info-' + selectedBannerIndex">
            <transition :name="infoDirection === 'ltr' ? 'slide-title-ltr' : 'slide-title-rtl'" mode="out-in">
              <h1 class="banner-title text-4xl font-bold mb-4" :key="currentBanner.id || selectedBannerIndex">{{ currentBanner.title }}</h1>
            </transition>
            <div class="banner-meta flex items-center gap-2 mb-2">
              <span v-if="currentBanner.sub_title" class="banner-sub text-lg text-gray-300">{{ currentBanner.sub_title }}</span>
            </div>
            <div class="banner-tags flex flex-wrap gap-2 mb-2">
              <router-link 
                v-if="currentBanner.imdb_rating"
                :to="{ name: 'movie-filter', query: { rating: currentBanner.imdb_rating }}"
                class="tag imdb bg-yellow-400 text-gray-900 font-bold px-3 py-1 rounded-full text-sm hover:bg-yellow-500 hover:scale-105 transition-all duration-300"
              >
                IMDb {{ currentBanner.imdb_rating }}
              </router-link>
              <router-link 
                v-if="currentBanner.quality"
                :to="{ name: 'movie-filter', query: { quality: currentBanner.quality }}"
                class="tag quality bg-gray-700 px-3 py-1 rounded-full text-sm hover:bg-gray-600 hover:scale-105 transition-all duration-300"
              >
                {{ currentBanner.quality }}
              </router-link>
              <router-link 
                v-if="currentBanner.age_rating"
                :to="{ name: 'movie-filter', query: { age: currentBanner.age_rating }}"
                class="tag age bg-gray-700 px-3 py-1 rounded-full text-sm hover:bg-gray-600 hover:scale-105 transition-all duration-300"
              >
                {{ currentBanner.age_rating }}
              </router-link>
              <router-link 
                v-if="currentBanner.release_year"
                :to="{ name: 'movie-filter', query: { year: currentBanner.release_year }}"
                class="tag year bg-gray-700 px-3 py-1 rounded-full text-sm hover:bg-gray-600 hover:scale-105 transition-all duration-300"
              >
                {{ currentBanner.release_year }}
              </router-link>
              <span v-if="currentBanner.is_series" class="tag part bg-gray-700 px-3 py-1 rounded-full text-sm">Phần {{ currentBanner.is_series }}</span>
              <span v-if="currentBanner.current_episode" class="tag episode bg-gray-700 px-3 py-1 rounded-full text-sm">Tập {{ currentBanner.current_episode }}</span>
            </div>
            <div class="banner-genres flex flex-wrap gap-2 mb-2">
              <router-link 
                v-for="g in currentBanner.genres || []" 
                :key="g.id"
                :to="{ name: 'genre', params: { slug: g.slug }}"
                class="genre bg-white/10 px-3 py-1 rounded-full text-sm hover:bg-white/20 hover:scale-105 transition-all duration-300"
              >
                {{ g.name }}
              </router-link>
            </div>
            <p class="banner-desc text-gray-300 mb-6 line-clamp-3">
              {{ currentBanner.description }}
            </p>
            <div class="banner-actions flex items-center gap-6 mb-4">
              <!-- Nút play tròn vàng -->
              <router-link 
                v-if="currentBanner.slug"
                :to="{ name: 'movie', params: { slug: currentBanner.slug }}"
                class="w-16 h-16 aspect-square rounded-full flex items-center justify-center bg-gradient-to-br from-yellow-300 via-yellow-400 to-yellow-500 shadow-lg hover:scale-105 transition group"
              >
                <i class="fa-solid fa-play text-gray-900 text-2xl"></i>
              </router-link>
              <!-- Nhóm nút yêu thích + info -->
              <div 
                class="flex items-center rounded-full border border-gray-500 overflow-hidden bg-transparent transition-all duration-200 group"
                style="min-width: 110px;"
                @mouseover="groupHover = true" @mouseleave="groupHover = false"
                :class="groupHover ? 'border-white shadow-lg' : ''"
              >
                <button
                  v-if="currentBanner.id"
                  @click="toggleBannerFavorite"
                  class="w-14 h-14 flex items-center justify-center bg-transparent hover:bg-gray-700 transition group"
                >
                  <i :class="['fa-solid fa-heart text-2xl transition', isBannerFavorite ? 'text-red-500' : 'text-white']"></i>
                </button>
                <div class="w-px h-10 bg-gray-500"></div>
                <router-link
                  v-if="currentBanner.slug"
                  :to="{ name: 'movie', params: { slug: currentBanner.slug }}"
                  class="w-14 h-14 flex items-center justify-center bg-transparent hover:bg-gray-700 transition"
                >
                  <i class="fa-solid fa-circle-info text-2xl text-white"></i>
                </router-link>
              </div>
            </div>
          </div>
        </transition>
        <!-- Mini banner thumbnails -->
        <div class="absolute right-8 bottom-16 flex gap-2 z-20">
          <button v-for="(movie, idx) in featuredMovies.slice(0, 5)" :key="movie.id"
            class="w-20 h-12 rounded-lg overflow-hidden border-2 transition-all duration-200"
            :class="selectedBannerIndex === idx ? 'border-yellow-400 scale-110 shadow-lg' : 'border-transparent opacity-70 hover:opacity-100'"
            @click="changeBanner(idx)">
            <img :src="movie.banner || fallbackBanner" :alt="movie.title" class="w-full h-full object-cover" />
          </button>
        </div>
      </div>
    </div>

    <!-- Top 10 phim bộ hôm nay -->
    <div class="bg-[#181f2a]">
    <section class="container mx-auto px-4 py-10">
      <h2 class="text-2xl font-bold mb-8">Top 10 phim bộ hôm nay</h2>
      <Top10Carousel :movies="top10Series" />
    </section>
    </div>

    <!-- Bọc các section bằng div nền tối -->
    <div class="bg-[#181f2a]">
      <!-- Latest Movies -->
      <section class="container mx-auto px-4 py-12">
        <h2 class="text-2xl font-bold mb-8">Phim mới cập nhật</h2>
        <div v-if="loading">
          <span class="text-gray-400">Đang tải phim mới...</span>
        </div>
        <div v-else-if="latestMovies.length > 0" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
          <movie-card 
            v-for="movie in latestMovies" 
            :key="movie.id"
            :movie="movie"
            :showTypeTag="true"
          />
        </div>
        <div v-else>
          <span class="text-gray-400">Không có phim mới.</span>
        </div>
      </section>

      <!-- Featured Movies -->
      <section class="container mx-auto px-4 py-12">
        <h2 class="text-2xl font-bold mb-8">Phim nổi bật</h2>
        <div v-if="loading">
          <span class="text-gray-400">Đang tải phim nổi bật...</span>
        </div>
        <div v-else-if="featuredMovies.length > 0" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
          <movie-card 
            v-for="movie in featuredMovies" 
            :key="movie.id"
            :movie="movie"
            :showTypeTag="true"
          />
        </div>
        <div v-else>
          <span class="text-gray-400">Không có phim nổi bật.</span>
        </div>
      </section>
    </div>
  </div>
</template>

<script setup>
import axios from 'axios'
import { computed, onMounted, ref, watch } from 'vue'
import { toast } from 'vue3-toastify'
import MovieCard from '../components/movies/MovieCard.vue'
import Top10Carousel from '../components/Top10Carousel.vue'

const latestMovies = ref([])
const featuredMovies = ref([])
const top10Series = ref([])
const loading = ref(true)
const fallbackBanner = '/banner-fallback.jpg'
const selectedBannerIndex = ref(0)
const slideDirection = ref('ltr')
const infoDirection = ref('rtl')
const isBannerFavorite = ref(false)
const groupHover = ref(false)

const currentBanner = computed(() => {
  if (!featuredMovies.value.length) return {}
  return featuredMovies.value[selectedBannerIndex.value] || featuredMovies.value[0]
})

const changeBanner = (idx) => {
  if (idx > selectedBannerIndex.value) {
    slideDirection.value = 'ltr'
    infoDirection.value = 'rtl'
  } else {
    slideDirection.value = 'rtl'
    infoDirection.value = 'ltr'
  }
  selectedBannerIndex.value = idx
}

const fetchMovies = async () => {
  loading.value = true
  try {
    const [latestResponse, featuredResponse, top10Response] = await Promise.all([
      axios.get('/api/movies/latest'),
      axios.get('/api/movies/featured'),
      axios.get('/api/movies/top10-series')
    ])
    latestMovies.value = latestResponse.data
    featuredMovies.value = featuredResponse.data
    top10Series.value = top10Response.data
    selectedBannerIndex.value = 0
    slideDirection.value = 'ltr'
    infoDirection.value = 'rtl'
  } catch (error) {
    console.error('Error fetching movies:', error)
  } finally {
    loading.value = false
  }
}

const fetchBannerIsFavorite = async () => {
  if (!currentBanner.value?.id) return
  try {
    const token = localStorage.getItem('token')
    if (!token) {
      isBannerFavorite.value = false
      return
    }
    const res = await axios.get(`/api/movies/${currentBanner.value.id}/is-favorite`, {
      headers: { Authorization: `Bearer ${token}` }
    })
    isBannerFavorite.value = res.data.favorite
  } catch (e) {
    isBannerFavorite.value = false
  }
}

const toggleBannerFavorite = async () => {
  const token = localStorage.getItem('token')
  if (!token) {
    toast.error('Bạn cần đăng nhập để sử dụng tính năng này!')
    return
  }
  try {
    if (isBannerFavorite.value) {
      await axios.delete(`/api/movies/${currentBanner.value.id}/favorite`, {
        headers: { Authorization: `Bearer ${token}` }
      })
      isBannerFavorite.value = false
      toast.success('Đã xoá khỏi danh sách yêu thích!')
    } else {
      await axios.post(`/api/movies/${currentBanner.value.id}/favorite`, {}, {
        headers: { Authorization: `Bearer ${token}` }
      })
      isBannerFavorite.value = true
      toast.success('Đã thêm vào danh sách yêu thích!')
    }
  } catch (e) {
    toast.error('Có lỗi xảy ra!')
  }
}

watch(currentBanner, () => {
  fetchBannerIsFavorite()
})

onMounted(() => {
  fetchMovies()
})
</script>

<style scoped>
.banner-fade-slide-ltr-enter-active, .banner-fade-slide-ltr-leave-active {
  transition: all 0.5s cubic-bezier(0.4,0,0.2,1);
}
.banner-fade-slide-ltr-enter-from {
  opacity: 0;
  transform: translateX(60px);
}
.banner-fade-slide-ltr-leave-to {
  opacity: 0;
  transform: translateX(-60px);
}
.banner-fade-slide-rtl-enter-active, .banner-fade-slide-rtl-leave-active {
  transition: all 0.5s cubic-bezier(0.4,0,0.2,1);
}
.banner-fade-slide-rtl-enter-from {
  opacity: 0;
  transform: translateX(-60px);
}
.banner-fade-slide-rtl-leave-to {
  opacity: 0;
  transform: translateX(60px);
}
.slide-title-ltr-enter-active, .slide-title-ltr-leave-active {
  transition: all 0.5s cubic-bezier(0.4,0,0.2,1);
}
.slide-title-ltr-enter-from {
  opacity: 0;
  transform: translateX(-80px);
}
.slide-title-ltr-leave-to {
  opacity: 0;
  transform: translateX(80px);
}
.slide-title-rtl-enter-active, .slide-title-rtl-leave-active {
  transition: all 0.5s cubic-bezier(0.4,0,0.2,1);
}
.slide-title-rtl-enter-from {
  opacity: 0;
  transform: translateX(80px);
}
.slide-title-rtl-leave-to {
  opacity: 0;
  transform: translateX(-80px);
}
</style> 