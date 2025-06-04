<template>
  <div class="movie-card group relative bg-gray-800 rounded-lg overflow-hidden transition-all duration-300">
    <!-- Movie Thumbnail -->
    <div class="relative aspect-[2/3]">
      <img 
        :src="movie.poster" 
        :alt="movie.title"
        class="w-full h-full object-cover"
      />
      <!-- Tag Phim Bộ/Phim Lẻ -->
      <div
        v-if="showTypeTag"
        class="absolute top-2 left-2 z-10 bg-blue-500 text-white px-2 py-1 rounded text-xs font-bold"
      >
        {{ movie.is_series ? 'Phim Bộ' : 'Phim Lẻ' }}
      </div>
      <!-- Episode badge for series (dưới tag loại phim nếu có) -->
      <div
        v-if="movie.is_series"
        :class="[
          'absolute left-2 bg-gray-900/80 px-2 py-1 rounded text-sm',
          showTypeTag ? 'top-10' : 'top-2'
        ]"
      >
        Tập {{ movie.current_episode || 1 }}
      </div>
      <!-- Overlay with actions -->
      <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center gap-4">
        <router-link 
          :to="{ name: 'movie', params: { slug: movie.slug }}"
          class="w-12 h-12 rounded-full bg-yellow-400 flex items-center justify-center hover:scale-110 transition-transform"
        >
          <i class="fa-solid fa-play text-gray-900"></i>
        </router-link>
        <button 
          @click="toggleFavorite"
          class="w-12 h-12 rounded-full bg-gray-700 flex items-center justify-center hover:scale-110 transition-transform"
        >
          <i :class="['fa-solid fa-heart', isFavorite ? 'text-red-500' : 'text-white']"></i>
        </button>
      </div>
      <!-- Quality badge -->
      <div v-if="movie.quality" class="absolute top-2 right-2 bg-gray-900/80 px-2 py-1 rounded text-sm">
        {{ movie.quality }}
      </div>
    </div>

    <!-- Movie Info -->
    <div class="p-3">
      <!-- Title -->
      <router-link
        :to="{ name: 'movie', params: { slug: movie.slug }}"
        class="text-lg font-semibold mb-2 line-clamp-1 transition-colors duration-200 hover:text-yellow-400 cursor-pointer"
      >
        {{ movie.title }}
      </router-link>
      
      <!-- Meta info -->
      <div class="flex items-center gap-2 mb-2">
        <span v-if="movie.release_year" class="text-sm text-gray-400">{{ movie.release_year }}</span>
        <span v-if="movie.imdb_rating" class="flex items-center gap-1 text-sm">
          <i class="fa-solid fa-star text-yellow-400"></i>
          {{ movie.imdb_rating }}
        </span>
        <span v-if="movie.age_rating" class="text-sm text-gray-400">{{ movie.age_rating }}</span>
      </div>

      <!-- Genres -->
      <div class="flex flex-wrap gap-1">
        <router-link 
          v-for="genre in movie.genres?.slice(0, 2)" 
          :key="genre.id"
          :to="{ name: 'genre', params: { slug: genre.slug }}"
          class="text-xs bg-gray-700 px-2 py-1 rounded-full hover:bg-gray-600 transition-colors"
        >
          {{ genre.name }}
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import axios from 'axios'
import { onMounted, ref } from 'vue'
import { toast } from 'vue3-toastify'

const props = defineProps({
  movie: {
    type: Object,
    required: true
  },
  showTypeTag: {
    type: Boolean,
    default: false
  }
})

const isFavorite = ref(false)

const fetchIsFavorite = async () => {
  if (!props.movie.id) return
  try {
    const token = localStorage.getItem('token')
    if (!token) {
      isFavorite.value = false
      return
    }
    const res = await axios.get(`/api/movies/${props.movie.id}/is-favorite`, {
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
      await axios.delete(`/api/movies/${props.movie.id}/favorite`, {
        headers: { Authorization: `Bearer ${token}` }
      })
      isFavorite.value = false
      toast.success('Đã xoá khỏi danh sách yêu thích!')
    } else {
      await axios.post(`/api/movies/${props.movie.id}/favorite`, {}, {
        headers: { Authorization: `Bearer ${token}` }
      })
      isFavorite.value = true
      toast.success('Đã thêm vào danh sách yêu thích!')
    }
  } catch (e) {
    toast.error('Có lỗi xảy ra!')
  }
}

onMounted(() => {
  fetchIsFavorite()
})
</script>

<style scoped>
.movie-card {
  transition: transform 0.2s;
}
</style> 