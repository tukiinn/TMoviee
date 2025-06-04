<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="bg-[#181c2a] rounded-2xl shadow-2xl w-full max-w-2xl flex overflow-hidden animate-fade-in">
      <!-- Logo/Info bên trái -->
      <div class="hidden md:flex flex-col items-center justify-center w-1/2 bg-[#232846] p-8">
        <img src="/images/logo/tmoviee.png" alt="Logo" class="w-28 h-28 mb-4">
        <div class="text-lg text-gray-300">Phim hay của Tú</div>
      </div>
      <!-- Form bên phải -->
      <div class="flex-1 p-8 flex flex-col justify-center relative">
        <button @click="$emit('close')" class="absolute top-4 right-4 text-gray-400 hover:text-red-500 text-2xl">×</button>
        <h2 class="text-2xl font-bold mb-2 text-white">Đăng nhập</h2>
        <div class="mb-6 text-gray-400 text-sm">
          Nếu bạn chưa có tài khoản, <span class="text-yellow-400 hover:underline font-semibold cursor-pointer" @click="$emit('show-register')">đăng ký ngay</span>
        </div>
        <form @submit.prevent="handleLogin">
          <div class="mb-4">
            <input v-model="email" type="email" placeholder="Email" class="w-full px-4 py-3 rounded-lg bg-gray-800 text-white focus:outline-none focus:ring-2 focus:ring-yellow-400 placeholder-gray-400" required>
          </div>
          <div class="mb-6">
            <input v-model="password" type="password" placeholder="Mật khẩu" class="w-full px-4 py-3 rounded-lg bg-gray-800 text-white focus:outline-none focus:ring-2 focus:ring-yellow-400 placeholder-gray-400" required>
          </div>
          <button type="submit" class="w-full py-3 rounded-lg font-bold text-lg bg-yellow-400 hover:bg-yellow-300 text-gray-900 transition">Đăng nhập</button>
        </form>
        <div v-if="error" class="text-red-500 text-sm mt-4 text-center">{{ error }}</div>
        <div class="mt-6 text-center">
          <span class="text-gray-400 hover:text-yellow-400 text-sm cursor-pointer" @click="$emit('show-forgot')">Quên mật khẩu?</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useAuthStore } from '@/stores/auth'
import { h, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { toast } from 'vue3-toastify'

const emit = defineEmits(['close', 'show-register', 'show-forgot'])
const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()
const email = ref('')
const password = ref('')
const error = ref('')
const loading = ref(false)

const handleLogin = async () => {
  error.value = ''
  loading.value = true
  try {
    const response = await authStore.login({
      email: email.value,
      password: password.value
    })
    
    if (response.data.user) {
      localStorage.setItem('showSuccessToast', 'Đăng nhập thành công!')
      
      if (route.query['ban-hay-dang-nhap']) {
        router.replace({ query: { ...route.query, 'ban-hay-dang-nhap': undefined } })
      }
      
      // Reload page for non-admin users
      if (response.data.user.role !== 'admin') {
        window.location.reload()
      }
    }
  } catch (err) {
    error.value = err.response?.data?.message || 'Đăng nhập thất bại'
    toast.error('Email hoặc mật khẩu không đúng', {
      icon: h('span', { style: 'color:#ef4444;font-size:1.5rem;margin-right:8px;display:flex;alignItems:center;' }, '❗')
    })
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.2s;
}
@keyframes fadeIn {
  from { opacity: 0; transform: scale(0.96); }
  to { opacity: 1; transform: scale(1); }
}
@media (max-width: 900px) {
  .max-w-2xl { max-width: 95vw; }
}
@media (max-width: 768px) {
  .max-w-2xl { max-width: 99vw; }
  .md\:flex { display: none !important; }
  .flex-1 { width: 100% !important; }
  .p-8 { padding: 1.2rem !important; }
}
</style> 