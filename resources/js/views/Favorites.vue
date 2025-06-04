<template>
  <div class="flex flex-col md:flex-row gap-8 pt-header">
    <!-- Sidebar -->
    <AccountSidebar :user="user" :avatar-preview="avatarPreview" :form="form" :get-avatar-url="getAvatarUrl" :logout="logout" active-tab="favorites" />

    <!-- Main Content -->
    <section class=" flex flex-col">
      <div class="max-w-5xl mx-auto">
        <h1 class="text-2xl font-bold mb-6">Phim yêu thích</h1>
        <LoadingSpinner v-if="loading" />
        <template v-else>
          <div v-if="favorites.length === 0" class="text-gray-400">Bạn chưa có phim yêu thích nào.</div>
          <div v-else class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <router-link
              v-for="movie in favorites"
              :key="movie.id"
              :to="{ name: 'movie', params: { slug: movie.slug } }"
              class="block rounded-xl overflow-hidden shadow-lg bg-gray-800 transition-transform duration-300 hover:shadow-2xl hover:-translate-y-1 border border-gray-700 group min-w-[180px] max-w-[260px] mx-auto"
            >
              <div class="relative">
                <MovieCard :movie="movie" />
                <span v-if="movie.quality === 'HD'" class="absolute top-2 right-2 bg-red-600 text-white text-xs font-bold px-2 py-1 rounded shadow">HD</span>
                <span v-if="!movie.season" class="absolute top-2 left-2 bg-blue-600 text-white text-xs font-bold px-2 py-1 rounded shadow">Phim lẻ</span>
              </div>
            </router-link>
          </div>
        </template>
      </div>
    </section>
  </div>
</template>

<script setup>
import AccountSidebar from '@/components/AccountSidebar.vue'
import LoadingSpinner from '@/components/LoadingSpinner.vue'
import axios from 'axios'
import { onMounted, ref } from 'vue'
import { toast } from 'vue3-toastify'
import MovieCard from '../components/MovieCard.vue'

const favorites = ref([])
const loading = ref(true)

const user = ref(null)
const avatarPreview = ref('')
const form = ref({
  name: '',
  email: '',
  gender: ''
})

const fetchUser = async () => {
  try {
    const token = localStorage.getItem('token')
    const res = await axios.get('/api/user/me', { headers: { Authorization: `Bearer ${token}` } })
    user.value = res.data
    form.value.name = user.value.name
    form.value.email = user.value.email
    form.value.gender = user.value.gender || ''
    avatarPreview.value = ''
  } catch (e) {
    toast.error('Không thể lấy thông tin tài khoản!')
  }
}

const getAvatarUrl = (avatar) => {
  if (!avatar) return '/default-avatar.png';
  if (avatar.startsWith('/images/avatars')) {
    return window.location.origin + avatar;
  }
  return avatar;
}

const logout = () => {
  localStorage.removeItem('token')
  localStorage.removeItem('user')
  window.location.reload()
}

const fetchFavorites = async () => {
  loading.value = true
  try {
    const token = localStorage.getItem('token')
    const res = await axios.get('/api/favorites', { headers: { Authorization: `Bearer ${token}` } })
    favorites.value = res.data
  } catch (e) {
    favorites.value = []
    toast.error('Không thể lấy danh sách yêu thích!')
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  fetchUser()
  fetchFavorites()
})
</script> 