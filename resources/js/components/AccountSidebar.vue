<template>
  <aside class="w-full md:w-1/5 max-w-xs bg-[#232846] rounded-2xl shadow-lg p-6 flex flex-col items-center md:items-start min-h-[600px] ml-8 md:ml-16">
    <div class="font-bold text-2xl text-white mb-8">Quản lý tài khoản</div>
    <nav class="flex flex-col gap-2 w-full mb-8">
      <router-link to="/favorites" :class="['flex items-center gap-2 px-4 py-2 rounded-lg transition font-semibold', activeTab === 'favorites' ? 'text-yellow-400 bg-gray-900' : 'text-gray-300 hover:bg-gray-700']">
        <i class="fas fa-heart text-red-400"></i> Yêu thích
      </router-link>
      <router-link to="/list" :class="['flex items-center gap-2 px-4 py-2 rounded-lg transition font-semibold', activeTab === 'list' ? 'text-yellow-400 bg-gray-900' : 'text-gray-300 hover:bg-gray-700']">
        <i class="fas fa-plus text-blue-400"></i> Danh sách
      </router-link>
      <router-link to="/continue-watching" :class="['flex items-center gap-2 px-4 py-2 rounded-lg transition font-semibold', activeTab === 'continue-watching' ? 'text-yellow-400 bg-gray-900' : 'text-gray-300 hover:bg-gray-700']">
        <i class="fas fa-history text-yellow-400"></i> Xem tiếp
      </router-link>
      <router-link to="/account" :class="['flex items-center gap-2 px-4 py-2 rounded-lg transition font-semibold', activeTab === 'account' ? 'text-yellow-400 bg-gray-900' : 'text-gray-300 hover:bg-gray-700']">
        <i class="fas fa-user"></i> Tài khoản
      </router-link>
      <router-link to="/membership" :class="['flex items-center gap-2 px-4 py-2 rounded-lg transition font-semibold', activeTab === 'membership' ? 'text-yellow-400 bg-gray-900' : 'text-yellow-400 hover:bg-yellow-900']">
        <i class="fas fa-crown text-yellow-400"></i> Gói VIP
      </router-link>
    </nav>
    <div class="mt-auto flex flex-col items-center w-full">
      <img :src="avatarPreview || getAvatarUrl(user?.avatar) || '/default-avatar.png'" class="w-16 h-16 rounded-full object-cover border-2 border-yellow-400 shadow mb-2" />
      <div class="font-bold text-white flex items-center">
        {{ form.name }}
        <span v-if="isVip" class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-yellow-400 text-gray-900 animate-pulse">
          <i class="fas fa-crown mr-1 text-yellow-600"></i>VIP
        </span>
      </div>
      <div class="text-gray-400 text-sm mb-2">{{ form.email }}</div>
      <button @click="logout" class="flex items-center gap-2 text-gray-300 hover:text-red-400 mt-2">
        <i class="fas fa-sign-out-alt"></i> Thoát
      </button>
    </div>
  </aside>
</template>

<script setup>
import axios from 'axios'
import { onMounted, ref } from 'vue'

const props = defineProps({
  user: Object,
  avatarPreview: String,
  form: Object,
  getAvatarUrl: Function,
  logout: Function,
  activeTab: {
    type: String,
    default: ''
  }
})

const isVip = ref(false)

const fetchMembership = async () => {
  try {
    const token = localStorage.getItem('token')
    if (!token) {
      isVip.value = false
      return
    }
    const res = await axios.get('/api/user/membership', { headers: { Authorization: `Bearer ${token}` } })
    isVip.value = !!res.data.active
  } catch (e) {
    isVip.value = false
  }
}

onMounted(() => {
  fetchMembership()
})
</script> 