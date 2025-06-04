<template>
  <header class="w-full flex items-center justify-between bg-[#23243a] py-4 shadow">
    <div class="flex items-center gap-4">
      <button @click="$emit('toggleSidebar')" class="text-white text-2xl focus:outline-none hover:text-yellow-400 transition-colors">
        ☰
      </button>
      <div class="relative group">
        <div class="relative flex items-center">
          <input 
            type="text" 
            v-model="searchQuery"
            @keyup.enter="handleSearch"
            placeholder="Tìm kiếm..." 
            class="w-80 bg-gray-800/50 text-gray-100 pl-12 pr-4 py-2.5 rounded-xl outline-none border border-gray-700 focus:border-yellow-400 transition-all duration-300 placeholder-gray-400 text-sm" 
          />
          <div class="absolute left-4 text-gray-400 group-focus-within:text-yellow-400 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </div>
          <select 
            v-model="searchType"
            class="absolute right-2 top-1/2 -translate-y-1/2 bg-gray-800 text-white border-none focus:outline-none cursor-pointer text-sm px-2 py-1 rounded-lg hover:bg-gray-700 transition-colors"
          >
            <option value="movies">Phim</option>
            <option value="actors">Diễn viên</option>
            <option value="directors">Đạo diễn</option>
          </select>
        </div>
        <!-- Search suggestions dropdown -->
        <div v-if="searchQuery && showSuggestions" class="absolute top-full left-0 right-0 mt-2 bg-gray-800 rounded-xl border border-gray-700 shadow-xl overflow-hidden z-50">
          <div class="p-2">
            <div class="text-xs text-gray-400 px-3 py-1.5">Tìm kiếm {{ searchType === 'movies' ? 'phim' : searchType === 'actors' ? 'diễn viên' : 'đạo diễn' }}</div>
            <button 
              @click="handleSearch"
              class="w-full text-left px-3 py-2 rounded-lg hover:bg-gray-700 transition-colors text-sm text-white flex items-center gap-2"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
              "{{ searchQuery }}"
            </button>
          </div>
        </div>
      </div>
    </div>
    <div class="flex items-center gap-4 ml-0 pe-6">
      <button class="text-white hover:text-yellow-400 transition-colors">
        <i class="el-icon-bell"></i>
      </button>
      <button class="text-white hover:text-yellow-400 transition-colors">
        <i class="el-icon-message"></i>
      </button>
      <div class="flex items-center gap-3">
        <img :src="user.avatar" class="w-10 h-10 rounded-full object-cover border-2 border-green-400 hover:border-yellow-400 transition-colors" />
        <span class="text-white font-semibold">{{ user.name }}</span>
      </div>
      <button @click="logout" class="text-red-500 hover:text-red-400 text-2xl transition-colors">
        <i class="fas fa-sign-out-alt"></i>
      </button>
    </div>
  </header>
</template>

<script setup>
import { onMounted, onUnmounted, ref, watch } from 'vue'
import { useRouter } from 'vue-router'
import { toast } from 'vue3-toastify'

const props = defineProps(['user', 'collapsed'])
const router = useRouter()
const searchQuery = ref('')
const searchType = ref('movies')
const showSuggestions = ref(false)

// Show suggestions when typing
watch(searchQuery, (newValue) => {
  showSuggestions.value = newValue.trim().length > 0
})

// Hide suggestions when clicking outside
const handleClickOutside = (event) => {
  const searchContainer = event.target.closest('.group')
  if (!searchContainer) {
    showSuggestions.value = false
  }
}

// Add click outside listener
onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})

function handleSearch() {
  if (!searchQuery.value.trim()) return

  const query = searchQuery.value.trim()
  const type = searchType.value

  // Navigate to the appropriate admin page with search query
  switch (type) {
    case 'movies':
      router.push({ name: 'admin.movies', query: { search: query } })
      break
    case 'actors':
      router.push({ name: 'admin.actors', query: { search: query } })
      break
    case 'directors':
      router.push({ name: 'admin.directors', query: { search: query } })
      break
  }
  
  showSuggestions.value = false
}

function logout() {
  localStorage.removeItem('token')
  localStorage.removeItem('user')
  toast.success('Đăng xuất thành công!')
  window.location.href = '/'
}
</script>

<style scoped>
header {
  z-index: 10;
}

select {
  appearance: none;
  background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='white' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
  background-repeat: no-repeat;
  background-position: right 0.5rem center;
  background-size: 1em;
  padding-right: 2rem;
}

/* Custom scrollbar for suggestions */
::-webkit-scrollbar {
  width: 8px;
}

::-webkit-scrollbar-track {
  background: #1a1b2e;
  border-radius: 4px;
}

::-webkit-scrollbar-thumb {
  background: #4a4b5c;
  border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
  background: #5a5b6c;
}
</style> 