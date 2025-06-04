<template>
  <div
    :class="[
      'bg-[#23243a] h-screen flex flex-col shadow-lg transition-all duration-500 ease-in-out',
      collapsed ? 'w-[80px]' : 'w-[260px]'
    ]"
  >
    <!-- Logo -->
    <div class="flex items-center justify-center py-6 transition-all duration-500">
      <template v-if="collapsed">
        <span class="text-3xl font-bold tracking-widest text-white">T</span>
      </template>
      <template v-else>
        <img src="/images/logo/tmoviee2.png" alt="Logo" class="h-13 object-contain transition-all duration-500" />
      </template>
    </div>
    <!-- User Info (bố cục ngang) -->
    <div class="flex items-center gap-3 px-4 mb-4 relative">
      <div class="relative">
        <img :src="user.avatar" :class="['rounded-full object-cover transition-all duration-500 avatar-square', collapsed ? 'w-8 h-8' : 'w-9 h-9']" />
        <span class="absolute bottom-0 right-0 -translate-x-1/4 w-3 h-3 bg-green-400 border-2 border-[#23243a] rounded-full"></span>
      </div>
      <div class="flex-1 min-w-0">
        <div v-show="!collapsed" class="text-white font-semibold leading-tight transition-opacity duration-500" :class="{'opacity-0': collapsed, 'opacity-100': !collapsed}">{{ user.name }}</div>
        <div v-show="!collapsed" class="text-xs text-gray-400 transition-opacity duration-500" :class="{'opacity-0': collapsed, 'opacity-100': !collapsed}">{{ user.role }}</div>
      </div>
      <div class="flex items-center h-full relative">
        <span v-show="!collapsed" class="flex flex-col gap-1 ml-2 cursor-pointer transition-opacity duration-500" :class="{'opacity-0': collapsed, 'opacity-100': !collapsed}" @click.stop="toggleMenu">
          <span class="w-1 h-1 bg-gray-400 rounded-full block"></span>
          <span class="w-1 h-1 bg-gray-400 rounded-full block"></span>
          <span class="w-1 h-1 bg-gray-400 rounded-full block"></span>
        </span>
        <!-- Popup menu -->
        <transition name="fade">
          <div
            v-if="showMenu"
            ref="menuRef"
            class="absolute top-8 right-0 z-50 w-48 bg-[#23243a] rounded-xl shadow-lg py-2"
            @click.stop
          >
            <div class="flex items-center gap-3 px-4 py-3 hover:bg-gray-800 cursor-pointer">
              <span class="w-8 h-8 flex items-center justify-center rounded-full bg-black/40">
                <i class="el-icon-setting text-cyan-400 text-lg"></i>
              </span>
              <span class="text-white">Account settings</span>
            </div>
            <div class="flex items-center gap-3 px-4 py-3 hover:bg-gray-800 cursor-pointer">
              <span class="w-8 h-8 flex items-center justify-center rounded-full bg-black/40">
                <i class="el-icon-info text-purple-400 text-lg"></i>
              </span>
              <span class="text-white">Change Password</span>
            </div>
            <div class="flex items-center gap-3 px-4 py-3 hover:bg-gray-800 cursor-pointer">
              <span class="w-8 h-8 flex items-center justify-center rounded-full bg-black/40">
                <i class="el-icon-date text-green-400 text-lg"></i>
              </span>
              <span class="text-white">To-do list</span>
            </div>
          </div>
        </transition>
      </div>
    </div>
    <!-- Navigation Title -->
    <div v-show="!collapsed" class="px-6 py-2 text-xs font-bold text-white/80 tracking-wide mb-2 transition-opacity duration-500" :class="{'opacity-0': collapsed, 'opacity-100': !collapsed}">
      <span class="bg-blue-600 text-white px-2 py-1 rounded">Panel Admin</span>
    </div>
    <!-- Menu -->
    <nav class="flex-1 flex flex-col gap-1">
      <SidebarItem :icon="HomeFilled" color="text-blue-400" label="Dashboard" :collapsed="collapsed" to="/admin" />
      <SidebarItem :icon="Film" color="text-purple-400" label="Quản lý phim" :collapsed="collapsed" to="/admin/movies" />
      <SidebarItem :icon="User" color="text-yellow-400" label="Quản lý diễn viên" :collapsed="collapsed" to="/admin/actors" />
      <SidebarItem :icon="UserFilled" color="text-green-400" label="Quản lý đạo diễn" :collapsed="collapsed" to="/admin/directors" />
      <SidebarItem :icon="UserFilled" color="text-cyan-400" label="Quản lý gói VIP" :collapsed="collapsed" to="/admin/membership-packages" />
    </nav>
  </div>
</template>

<script setup>
import { Film, HomeFilled, User, UserFilled } from '@element-plus/icons-vue'
import { onBeforeUnmount, onMounted, ref } from 'vue'
import SidebarItem from './SidebarItem.vue'
const props = defineProps(['user', 'collapsed'])
const showMenu = ref(false)
const menuRef = ref(null)
function toggleMenu() { showMenu.value = !showMenu.value }
function closeMenu(e) {
  if (showMenu.value && menuRef.value && !menuRef.value.contains(e.target)) {
    showMenu.value = false
  }
}
onMounted(() => {
  document.addEventListener('click', closeMenu)
})
onBeforeUnmount(() => {
  document.removeEventListener('click', closeMenu)
})
</script>

<style scoped>
.router-link-exact-active {
  color: #ffd04b !important;
  background: #111827 !important;
}
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.2s;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
.avatar-square {
  aspect-ratio: 1 / 1;
  height: auto;
  max-width: 100%;
  max-height: 100%;
  display: block;
}
</style> 