<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="bg-[#181c2a] rounded-2xl shadow-2xl w-full max-w-2xl flex overflow-hidden animate-fade-in relative">
      <!-- Toast notification -->
      <transition name="toast-fade">
        <div v-if="toast.message" :class="['toast', toast.type === 'success' ? 'toast-success' : 'toast-error']">
          {{ toast.message }}
        </div>
      </transition>
      <!-- Logo/Info bên trái -->
      <div class="hidden md:flex flex-col items-center justify-center w-1/2 bg-[#232846] p-8">
        <img src="/images/logo/tmoviee.png" alt="Logo" class="w-28 h-28 mb-4">
        <div class="text-lg text-gray-300">Phim hay của Tú</div>
      </div>
      <!-- Form bên phải -->
      <div class="flex-1 p-8 flex flex-col justify-center relative">
        <button @click="$emit('close')" class="absolute top-4 right-4 text-gray-400 hover:text-red-500 text-2xl">×</button>
        <h2 class="text-2xl font-bold mb-2 text-white">Tạo tài khoản mới</h2>
        <div class="mb-6 text-gray-400 text-sm">
          Nếu bạn đã có tài khoản, <span class="text-yellow-400 hover:underline font-semibold cursor-pointer" @click="$emit('show-login')">đăng nhập</span>
        </div>
        <form @submit.prevent="register">
          <div class="mb-4">
            <input v-model="name" type="text" placeholder="Tên hiển thị" class="w-full px-4 py-3 rounded-lg bg-gray-800 text-white focus:outline-none focus:ring-2 focus:ring-yellow-400 placeholder-gray-400" required>
          </div>
          <div class="mb-4">
            <input v-model="email" type="email" placeholder="Email" class="w-full px-4 py-3 rounded-lg bg-gray-800 text-white focus:outline-none focus:ring-2 focus:ring-yellow-400 placeholder-gray-400" required>
          </div>
          <div class="mb-4">
            <input v-model="password" type="password" placeholder="Mật khẩu" class="w-full px-4 py-3 rounded-lg bg-gray-800 text-white focus:outline-none focus:ring-2 focus:ring-yellow-400 placeholder-gray-400" required>
          </div>
          <div class="mb-6">
            <input v-model="password_confirmation" type="password" placeholder="Nhập lại mật khẩu" class="w-full px-4 py-3 rounded-lg bg-gray-800 text-white focus:outline-none focus:ring-2 focus:ring-yellow-400 placeholder-gray-400" required>
          </div>
          <button type="submit" class="w-full py-3 rounded-lg font-bold text-lg bg-yellow-400 hover:bg-yellow-300 text-gray-900 transition" :disabled="loading">Đăng ký</button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import axios from 'axios'
import { h, ref } from 'vue'
import { useRouter } from 'vue-router'
import { toast } from 'vue3-toastify'

const router = useRouter()
const name = ref('')
const email = ref('')
const password = ref('')
const password_confirmation = ref('')
const error = ref('')
const loading = ref(false)

const register = async () => {
  error.value = ''
  loading.value = true
  try {
    const response = await axios.post('/api/register', {
      name: name.value,
      email: email.value,
      password: password.value,
      password_confirmation: password_confirmation.value
    })
    
    if (response.data.token) {
      localStorage.setItem('token', response.data.token)
      localStorage.setItem('user', JSON.stringify(response.data.user))
      localStorage.setItem('showSuccessToast', 'Đăng ký thành công!')
      window.location.reload()
    } else {
      toast.error('Đăng ký thất bại: Không nhận được token', {
        icon: h('span', { style: 'color:#ef4444;font-size:1.5rem;margin-right:8px;display:flex;alignItems:center;' }, '❗')
      })
    }
  } catch (err) {
    console.error('Registration error:', err)
    if (err.response?.data?.errors) {
      const firstError = Object.values(err.response.data.errors)[0][0]
      toast.error(firstError, {
        icon: h('span', { style: 'color:#ef4444;font-size:1.5rem;margin-right:8px;display:flex;alignItems:center;' }, '❗')
      })
    } else {
      toast.error(err.response?.data?.message || 'Đăng ký thất bại', {
        icon: h('span', { style: 'color:#ef4444;font-size:1.5rem;margin-right:8px;display:flex;alignItems:center;' }, '❗')
      })
    }
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
.toast {
  position: fixed;
  top: 24px;
  left: 50%;
  transform: translateX(-50%);
  min-width: 280px;
  max-width: 90vw;
  padding: 1rem 2rem;
  border-radius: 12px;
  font-weight: 600;
  font-size: 0.95rem;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.25);
  z-index: 9999;
  text-align: center;
  backdrop-filter: blur(8px);
  border: 1px solid rgba(255, 255, 255, 0.1);
}
.toast-success {
  background: linear-gradient(135deg, #22c55e, #16a34a);
  color: white;
}
.toast-error {
  background: linear-gradient(135deg, #ef4444, #dc2626);
  color: white;
}
.toast-fade-enter-active, .toast-fade-leave-active {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.toast-fade-enter-from, .toast-fade-leave-to {
  opacity: 0;
  transform: translate(-50%, -20px) scale(0.95);
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