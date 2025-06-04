<template>
  <div class="flex flex-col md:flex-row gap-8 pt-header">
    <!-- Sidebar -->
    <AccountSidebar :user="user" :avatar-preview="avatarPreview" :form="form" :get-avatar-url="getAvatarUrl" :logout="logout" active-tab="account" />

    <!-- Form chính giữa, avatar lớn ở đầu form -->
    <section class="flex-1 flex flex-col">
      <div class="max-w-2xl w-full">
        <h1 class="text-3xl font-bold mb-8">Tài khoản</h1>
        <!-- Block Membership -->
        <div v-if="membershipLoading" class="mb-6"><LoadingSpinner /></div>
        <div v-else class="mb-6">
          <div v-if="membership.active" class="bg-gradient-to-r from-yellow-400 to-yellow-200 rounded-xl p-5 flex items-center gap-4 shadow-lg">
            <div>
              <div class="font-bold text-lg text-gray-900 flex items-center gap-2">
                <i class="fas fa-crown text-yellow-500"></i> Gói: <span class="uppercase">{{ membership.package?.name }}</span>
              </div>
              <div class="text-gray-800 mt-1">Còn lại: <span class="font-semibold">{{ Math.round(membership.days_left) }}</span> ngày</div>
              <div class="text-gray-700 text-sm">Hạn đến: {{ formatDate(membership.end_at) }}</div>
            </div>
          </div>
          <div v-else class="bg-gray-800 rounded-xl p-5 text-gray-300 flex items-center gap-4 shadow">
            <i class="fas fa-user-lock text-2xl text-gray-500"></i>
            <div>
              <div class="font-bold text-lg">Bạn chưa đăng ký gói VIP</div>
              <div class="text-sm">Đăng ký để trải nghiệm đặc quyền không giới hạn!</div>
            </div>
          </div>
        </div>
        <!-- End Block Membership -->
        <LoadingSpinner v-if="loading" />
        <template v-else>
          <div class="text-gray-400 mb-6">Cập nhật thông tin tài khoản</div>
          <form @submit.prevent="updateProfile" class="space-y-4">
            <div class="flex flex-col items-center mb-6 relative">
              <div class="relative group">
                <img
                  :src="avatarPreview || getAvatarUrl(user?.avatar) || '/default-avatar.png'"
                  class="w-28 h-28 rounded-full object-cover border-4 border-yellow-400 shadow-xl transition"
                />
                <!-- Overlay khi hover -->
                <label class="absolute inset-0 flex items-center justify-center bg-opacity-0 group-hover:bg-opacity-20 rounded-full cursor-pointer transition">
                  <svg class="w-8 h-8 text-yellow-300 opacity-0 group-hover:opacity-100 transition" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M4 5a2 2 0 012-2h8a2 2 0 012 2v10a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm2-1a1 1 0 00-1 1v10a1 1 0 001 1h8a1 1 0 001-1V5a1 1 0 00-1-1H6zm4 3a2 2 0 110 4 2 2 0 010-4zm-3 7a5 5 0 016 0H7z"/>
                  </svg>
                  <input
                    type="file"
                    accept="image/*"
                    @change="onAvatarChange"
                    class="hidden"
                  />
                </label>
              </div>
              <div class="text-gray-300 text-base mt-2">Đổi ảnh đại diện</div>
              <div class="text-xs text-gray-500 mt-2 text-center">Chỉ chấp nhận ảnh JPG, PNG, tối đa 2MB.</div>
            </div>
            <div>
              <label class="block text-gray-400 mb-1">Email</label>
              <input v-model="form.email" type="email" class="w-full px-4 py-2 rounded bg-gray-900 text-white border border-gray-700 focus:outline-none focus:ring-2 focus:ring-red-500" />
            </div>
            <div>
              <label class="block text-gray-400 mb-1">Tên hiển thị</label>
              <input v-model="form.name" type="text" class="w-full px-4 py-2 rounded bg-gray-900 text-white border border-gray-700 focus:outline-none focus:ring-2 focus:ring-red-500" />
            </div>
            <div>
              <label class="block text-gray-400 mb-1">Giới tính</label>
              <div class="flex items-center space-x-6">
                <label class="inline-flex items-center">
                  <input type="radio" class="form-radio text-red-500" value="male" v-model="form.gender" />
                  <span class="ml-2">Nam</span>
                </label>
                <label class="inline-flex items-center">
                  <input type="radio" class="form-radio text-red-500" value="female" v-model="form.gender" />
                  <span class="ml-2">Nữ</span>
                </label>
                <label class="inline-flex items-center">
                  <input type="radio" class="form-radio text-red-500" value="other" v-model="form.gender" />
                  <span class="ml-2">Không xác định</span>
                </label>
              </div>
            </div>
            <button type="submit" :disabled="loading" class="bg-yellow-400 hover:bg-yellow-300 text-gray-900 font-bold py-2 px-6 rounded-lg transition flex items-center justify-center min-w-[120px]">
              <svg v-if="loading" class="animate-spin h-5 w-5 mr-2 text-gray-900" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
              </svg>
              <span>{{ loading ? 'Đang cập nhật...' : 'Cập nhật' }}</span>
            </button>
            <div>
              <span class="text-gray-400">Đổi mật khẩu, nhấn vào <span class="text-yellow-400 cursor-pointer hover:underline" @click="showPasswordModal = true">đây</span></span>
            </div>

          </form>
        </template>
      </div>
      <div v-if="showPasswordModal" class="fixed inset-0 z-50 flex items-center justify-center backdrop-blur-md">
        <div class="bg-[#181c2a] rounded-2xl shadow-2xl w-full max-w-xl flex flex-col relative animate-fade-in p-0 overflow-hidden">
          <button @click="showPasswordModal = false" class="absolute top-4 right-4 text-gray-400 hover:text-red-500 text-2xl">×</button>
          <div class="flex flex-col items-center justify-center w-full p-8">
            <h2 class="text-2xl font-bold mb-2 text-white">Đổi mật khẩu</h2>
            <form @submit.prevent="changePassword" class="space-y-4 w-full max-w-md mx-auto mt-4">
              <div>
                <input v-model="passwordForm.current_password" type="password" placeholder="Mật khẩu hiện tại" class="w-full px-4 py-3 rounded-lg bg-gray-800 text-white focus:outline-none focus:ring-2 focus:ring-yellow-400 placeholder-gray-400" required />
              </div>
              <div>
                <input v-model="passwordForm.password" type="password" placeholder="Mật khẩu mới" class="w-full px-4 py-3 rounded-lg bg-gray-800 text-white focus:outline-none focus:ring-2 focus:ring-yellow-400 placeholder-gray-400" required />
              </div>
              <div>
                <input v-model="passwordForm.password_confirmation" type="password" placeholder="Nhập lại mật khẩu mới" class="w-full px-4 py-3 rounded-lg bg-gray-800 text-white focus:outline-none focus:ring-2 focus:ring-yellow-400 placeholder-gray-400" required />
              </div>
              <button type="submit" :disabled="passwordLoading" class="w-full py-3 rounded-lg font-bold text-lg bg-yellow-400 hover:bg-yellow-300 text-gray-900 transition flex items-center justify-center min-w-[120px]">
                <svg v-if="passwordLoading" class="animate-spin h-5 w-5 mr-2 text-gray-900" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
                <span>{{ passwordLoading ? 'Đang đổi...' : 'Xác nhận' }}</span>
              </button>
            </form>
          </div>
        </div>
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

const user = ref(null)
const avatarPreview = ref('')
const form = ref({
  name: '',
  email: '',
  gender: '',
  password: '',
  password_confirmation: '',
  current_password: ''
})
const loading = ref(false)
const showPasswordModal = ref(false)
const passwordLoading = ref(false)
const passwordForm = ref({
  current_password: '',
  password: '',
  password_confirmation: ''
})
const membership = ref({ active: false });
const membershipLoading = ref(true);

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

const onAvatarChange = (e) => {
  const file = e.target.files[0]
  if (file) {
    form.value.avatar = file
    avatarPreview.value = URL.createObjectURL(file)
  }
}

const getAvatarUrl = (avatar) => {
  if (!avatar) return '/default-avatar.png';
  if (avatar.startsWith('/images/avatars')) {
    return window.location.origin + avatar;
  }
  return avatar;
}

const updateProfile = async () => {
  loading.value = true
  try {
    const token = localStorage.getItem('token')
    const payload = new FormData()
    payload.append('name', form.value.name)
    payload.append('email', form.value.email)
    payload.append('gender', form.value.gender)
    if (form.value.avatar) payload.append('avatar', form.value.avatar)
    const res = await axios.post('/api/user/update', payload, {
      headers: { Authorization: `Bearer ${token}`, 'Content-Type': 'multipart/form-data' }
    })
    toast.success(res.data.message || 'Cập nhật thành công!')
    fetchUser()
    localStorage.setItem('user', JSON.stringify(res.data.user))
    form.value.avatar = null
    avatarPreview.value = ''
  } catch (e) {
    toast.error(e.response?.data?.message || 'Cập nhật thất bại!')
  } finally {
    loading.value = false
  }
}

const changePassword = async () => {
  passwordLoading.value = true
  try {
    const token = localStorage.getItem('token')
    const payload = new FormData()
    payload.append('current_password', passwordForm.value.current_password)
    payload.append('password', passwordForm.value.password)
    payload.append('password_confirmation', passwordForm.value.password_confirmation)
    payload.append('name', form.value.name)
    payload.append('email', form.value.email)
    payload.append('gender', form.value.gender)
    if (form.value.avatar) payload.append('avatar', form.value.avatar)
    const res = await axios.post('/api/user/update', payload, {
      headers: { Authorization: `Bearer ${token}`, 'Content-Type': 'multipart/form-data' }
    })
    toast.success(res.data.message || 'Đổi mật khẩu thành công!')
    showPasswordModal.value = false
    passwordForm.value.current_password = ''
    passwordForm.value.password = ''
    passwordForm.value.password_confirmation = ''
  } catch (e) {
    toast.error(e.response?.data?.message || 'Đổi mật khẩu thất bại!')
  } finally {
    passwordLoading.value = false
  }
}

const logout = () => {
  localStorage.removeItem('token')
  localStorage.removeItem('user')
  window.location.reload()
}

const fetchMembership = async () => {
  try {
    const token = localStorage.getItem('token');
    const res = await axios.get('/api/user/membership', { headers: { Authorization: `Bearer ${token}` } });
    membership.value = res.data;
  } catch (e) {
    membership.value = { active: false };
  } finally {
    membershipLoading.value = false;
  }
};

const formatDate = (date) => {
  if (!date) return '';
  const d = new Date(date);
  const day = d.getDate().toString().padStart(2, '0');
  const month = (d.getMonth() + 1).toString().padStart(2, '0');
  const year = d.getFullYear();
  return `${day}/${month}/${year}`;
};

onMounted(() => {
  fetchUser()
  fetchMembership()
})
</script>

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.2s;
}
@keyframes fadeIn {
  from { opacity: 0; transform: scale(0.96); }
  to { opacity: 1; transform: scale(1); }
}
</style> 