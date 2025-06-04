<template>
  <AdminLayout>
    <div class="flex-1 p-4  min-h-screen">
      <div class="p-6 pt-header min-h-screen">
        <div v-if="loading" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
          <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-yellow-400 border-solid"></div>
        </div>
        <div class="flex items-center justify-between mb-6">
          <h1 class="text-2xl font-bold">Quản lý Membership</h1>
          <button @click="openAdd" class="bg-yellow-400 hover:bg-yellow-300 text-gray-900 font-bold px-5 py-2 rounded-lg shadow flex items-center">
            <i class="fas fa-plus mr-2"></i> Thêm gói mới
          </button>
        </div>
        <!-- Table -->
        <div class="bg-[#23262f] rounded-xl shadow p-4 overflow-x-auto">
          <table class="min-w-full text-sm">
            <thead>
              <tr class="text-left text-gray-400 border-b border-gray-700">
                <th class="py-2">Ảnh</th>
                <th class="py-2">Tên gói</th>
                <th class="py-2">Giá</th>
                <th class="py-2">Thời hạn</th>
                <th class="py-2">Trạng thái</th>
                <th class="py-2">Hành động</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="pkg in packages" :key="pkg.id" class="border-b border-gray-800 hover:bg-gray-800">
                <td class="py-2">
                  <img :src="pkg.image || '/default-avatar.png'" class="w-14 h-14 object-cover rounded-lg border" />
                </td>
                <td class="py-2 font-bold">{{ pkg.name }}</td>
                <td class="py-2">{{ formatPrice(pkg.price) }}đ</td>
                <td class="py-2">{{ pkg.duration_days }} ngày</td>
                <td class="py-2">
                  <span :class="pkg.is_active ? 'text-green-400' : 'text-gray-400'">
                    {{ pkg.is_active ? 'Hiện' : 'Ẩn' }}
                  </span>
                </td>
                <td class="py-2">
                  <button @click="openEdit(pkg)" class="px-3 py-1 rounded bg-blue-500 text-white hover:bg-blue-600 mr-2"><i class="fas fa-edit"></i></button>
                  <button @click="confirmDelete(pkg)" class="px-3 py-1 rounded bg-red-500 text-white hover:bg-red-600"><i class="fas fa-trash"></i></button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Modal Thêm/Sửa -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
          <div class="bg-[#23262f] rounded-xl shadow-xl w-full max-w-lg p-8 relative overflow-y-auto max-h-[90vh] hide-scrollbar">
            <button @click="closeModal" class="absolute top-4 right-4 text-gray-400 hover:text-red-500 text-2xl">×</button>
            <h2 class="text-xl font-bold mb-4">{{ editId ? 'Sửa' : 'Thêm' }} gói VIP</h2>
            <form @submit.prevent="submitForm" class="space-y-4">
              <div>
                <label class="block text-gray-400 mb-1">Tên gói</label>
                <input v-model="form.name" type="text" class="w-full px-4 py-2 rounded bg-gray-900 text-white border border-gray-700 focus:outline-none" required />
              </div>
              <div>
                <label class="block text-gray-400 mb-1">Giá (VNĐ)</label>
                <input v-model.number="form.price" type="number" min="0" class="w-full px-4 py-2 rounded bg-gray-900 text-white border border-gray-700 focus:outline-none" required />
              </div>
              <div>
                <label class="block text-gray-400 mb-1">Thời hạn (ngày)</label>
                <input v-model.number="form.duration_days" type="number" min="1" class="w-full px-4 py-2 rounded bg-gray-900 text-white border border-gray-700 focus:outline-none" required />
              </div>
              <div>
                <label class="block text-gray-400 mb-1">Mô tả</label>
                <textarea v-model="form.description" class="w-full px-4 py-2 rounded bg-gray-900 text-white border border-gray-700 focus:outline-none"></textarea>
              </div>
              <div>
                <label class="block text-gray-400 mb-1">Tính năng (mỗi dòng 1 tính năng)</label>
                <textarea v-model="featuresInput" class="w-full px-4 py-2 rounded bg-gray-900 text-white border border-gray-700 focus:outline-none" rows="3"></textarea>
              </div>
              <div>
                <label class="block text-gray-400 mb-1">Ảnh minh hoạ</label>
                <input type="file" accept="image/*" @change="onImageChange" class="block w-full text-gray-300" />
                <div v-if="imagePreview" class="mt-2">
                  <img :src="imagePreview" class="w-24 h-24 object-cover rounded border" />
                </div>
                <div v-else-if="form.image && typeof form.image === 'string'" class="mt-2">
                  <img :src="form.image" class="w-24 h-24 object-cover rounded border" />
                </div>
              </div>
              <div>
                <label class="inline-flex items-center gap-2">
                  <input type="checkbox" v-model="form.is_active" class="form-checkbox text-yellow-400 rounded" />
                  <span class="text-gray-300">Kích hoạt</span>
                </label>
              </div>
              <div class="flex justify-end gap-2 mt-4">
                <button type="button" @click="closeModal" class="px-4 py-2 rounded bg-gray-700 text-white hover:bg-gray-600">Huỷ</button>
                <button type="submit" class="px-4 py-2 rounded bg-yellow-400 text-gray-900 font-bold hover:bg-yellow-300">Lưu</button>
              </div>
            </form>
          </div>
        </div>

        <!-- Modal xác nhận xoá -->
        <div v-if="showDelete" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
          <div class="bg-[#23262f] rounded-xl shadow-xl w-full max-w-sm p-8 relative flex flex-col items-center">
            <h2 class="text-xl font-bold mb-4 text-red-400">Xác nhận xoá?</h2>
            <p class="text-gray-300 mb-6">Bạn có chắc muốn xoá gói <span class="font-bold">{{ deleteTarget?.name }}</span>?</p>
            <div class="flex gap-4">
              <button @click="showDelete = false" class="px-4 py-2 rounded bg-gray-700 text-white hover:bg-gray-600">Huỷ</button>
              <button @click="deletePackage" class="px-4 py-2 rounded bg-red-500 text-white font-bold hover:bg-red-600">Xoá</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/components/admin/AdminLayout.vue'
import axios from 'axios'
import { onMounted, ref } from 'vue'
import { toast } from 'vue3-toastify'

const packages = ref([])
const loading = ref(false)
const showModal = ref(false)
const showDelete = ref(false)
const editId = ref(null)
const deleteTarget = ref(null)
const form = ref({
    name: '',
    price: 0,
    duration_days: 1,
    description: '',
    features: [],
    is_active: true,
    image: ''
})
const featuresInput = ref('')
const imageFile = ref(null)
const imagePreview = ref('')

const fetchPackages = async () => {
    loading.value = true
    try {
        const token = localStorage.getItem('token')
        const res = await axios.get('/api/admin/membership-packages', { headers: { Authorization: `Bearer ${token}` } })
        packages.value = res.data
    } catch (e) {
        toast.error('Không thể tải danh sách gói!')
    } finally {
        loading.value = false
    }
}

const openAdd = () => {
    editId.value = null
    form.value = { name: '', price: 0, duration_days: 1, description: '', features: [], is_active: true, image: '' }
    featuresInput.value = ''
    imageFile.value = null
    imagePreview.value = ''
    showModal.value = true
}

const openEdit = (pkg) => {
    editId.value = pkg.id
    form.value = { ...pkg, is_active: !!pkg.is_active }
    featuresInput.value = (pkg.features || []).join('\n')
    imageFile.value = null
    imagePreview.value = ''
    showModal.value = true
}

const closeModal = () => {
    showModal.value = false
}

const onImageChange = (e) => {
    const file = e.target.files[0]
    if (file) {
        imageFile.value = file
        imagePreview.value = URL.createObjectURL(file)
    }
}

const submitForm = async () => {
    const token = localStorage.getItem('token')
    const data = new FormData()
    data.append('name', form.value.name)
    data.append('price', form.value.price)
    data.append('duration_days', form.value.duration_days)
    data.append('description', form.value.description)
    data.append('is_active', form.value.is_active ? 1 : 0)
    // Tính năng
    const featuresArr = featuresInput.value.split('\n').map(f => f.trim()).filter(f => f)
    data.append('features', JSON.stringify(featuresArr))
    if (imageFile.value) {
        data.append('image', imageFile.value)
    }
    // Nếu sửa mà không upload ảnh mới, giữ nguyên link cũ
    if (editId.value && !imageFile.value && typeof form.value.image === 'string') {
        data.append('image', form.value.image)
    }
    try {
        if (editId.value) {
            await axios.post(`/api/admin/membership-packages/${editId.value}?_method=PUT`, data, {
                headers: { Authorization: `Bearer ${token}`, 'Content-Type': 'multipart/form-data' }
            })
            toast.success('Cập nhật thành công!')
        } else {
            await axios.post('/api/admin/membership-packages', data, {
                headers: { Authorization: `Bearer ${token}`, 'Content-Type': 'multipart/form-data' }
            })
            toast.success('Thêm mới thành công!')
        }
        showModal.value = false
        fetchPackages()
    } catch (e) {
        toast.error('Lưu thất bại!')
    }
}

const confirmDelete = (pkg) => {
    deleteTarget.value = pkg
    showDelete.value = true
}

const deletePackage = async () => {
    const token = localStorage.getItem('token')
    try {
        await axios.delete(`/api/admin/membership-packages/${deleteTarget.value.id}`, {
            headers: { Authorization: `Bearer ${token}` }
        })
        toast.success('Đã xoá!')
        showDelete.value = false
        fetchPackages()
    } catch (e) {
        toast.error('Xoá thất bại!')
    }
}

const formatPrice = (price) => {
    return new Intl.NumberFormat('vi-VN', { style: 'decimal', maximumFractionDigits: 0 }).format(price)
}

onMounted(() => {
    fetchPackages()
})
</script>

<style scoped>
/* Ẩn scrollbar nhưng vẫn cuộn được */
.hide-scrollbar {
  scrollbar-width: none; /* Firefox */
  -ms-overflow-style: none; /* IE 10+ */
}
.hide-scrollbar::-webkit-scrollbar {
  display: none; /* Chrome, Safari, Opera */
}
</style> 