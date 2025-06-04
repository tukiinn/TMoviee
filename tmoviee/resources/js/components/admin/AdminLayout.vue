<template>
  <div class="flex h-screen bg-[#181c2f]">
    <AdminSidebar :user="user" :collapsed="isSidebarCollapsed" />
    <div class="flex-1 flex flex-col min-w-0">
      <AdminHeader :user="user" :collapsed="isSidebarCollapsed" @toggleSidebar="isSidebarCollapsed = !isSidebarCollapsed" />
      <main class="flex-1 bg-[#181c2f] min-w-0">
        <slot />
      </main>
      <footer class="text-center text-xs text-gray-400 py-4">
        Copyright © 2024
        <a href="https://www.bootstrapdash.com/" class="text-green-400 underline hover:text-green-300" target="_blank">BootstrapDash</a>.
        All rights reserved. Distributed by
        <a href="https://themewagon.com/" class="text-green-400 underline hover:text-green-300" target="_blank">ThemeWagon</a>
      </footer>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import AdminHeader from './AdminHeader.vue'
import AdminSidebar from './AdminSidebar.vue'

const user = ref({
  name: '',
  role: '',
  avatar: ''
})
const isSidebarCollapsed = ref(false)

onMounted(() => {
  const userData = JSON.parse(localStorage.getItem('user') || '{}')
  user.value = {
    name: userData.name || 'Henry Klein',
    role: userData.role || 'Gold Member',
    avatar: userData.avatar || '/images/avatar.jpg'
  }
})
</script> 