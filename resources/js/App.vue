<template>
  <div class="min-h-screen bg-gray-900 text-white flex flex-col">
    <!-- Header -->
    <header v-if="!isAdminRoute" :class="['main-header', { 'scrolled': isScrolled }]">
      <nav class="container mx-auto px-4 py-4 flex justify-between items-center nav-content">
        <div class="flex items-center space-x-8">
          <!-- Hamburger button (mobile only) -->
          <button class="xl:hidden mr-2" @click="toggleMobileMenu">
            <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <line x1="4" y1="6" x2="20" y2="6" />
              <line x1="4" y1="12" x2="20" y2="12" />
              <line x1="4" y1="18" x2="20" y2="18" />
            </svg>
          </button>
          <router-link to="/" class="logo-link">
            <img src="/images/logo/tmoviee2.png" alt="Logo" class="h-14 w-14 object-contain">
          </router-link>
          <div class="search-bar hidden xl:flex">
            <span class="search-icon">
              <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="11" cy="11" r="8" />
                <line x1="21" y1="21" x2="16.65" y2="16.65" />
              </svg>
            </span>
            <input 
              type="search" 
              v-model="searchQuery"
              @keyup.enter="handleSearch"
              placeholder="Tìm kiếm phim, diễn viên" 
              class="search-input"
            >
          </div>
          <div class="hidden xl:flex space-x-6 relative menu-group">
            <GenreDropdown />
            <router-link to="/movies" class="hover:text-red-500">Tìm phim</router-link>
            <router-link to="/single" class="hover:text-red-500">Phim Lẻ</router-link>
            <router-link to="/series" class="hover:text-red-500">Phim Bộ</router-link>
            <CountryDropdown />
            <router-link to="/actors" class="hover:text-red-500">Diễn viên</router-link>
          </div>
        </div>
        <div class="flex items-center space-x-4">
          <template v-if="isLoggedIn">
            <div class="relative" v-click-outside="closeMenu">
              <button @click="menuOpen = !menuOpen" class="flex items-center space-x-2 bg-gray-800 px-4 py-2 rounded-full focus:outline-none">
                <img :src="getAvatarUrl(userAvatar)" class="w-8 h-8 rounded-full object-cover border-2 border-white" />
                <span>{{ userEmail }}
                  <span v-if="isVip" class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-yellow-400 text-gray-900 animate-pulse">
                    <i class="fas fa-crown mr-1 text-yellow-600"></i>VIP
                  </span>
                </span>
                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path d="M19 9l-7 7-7-7" />
                </svg>
              </button>
              <div v-if="menuOpen" class="absolute right-0 mt-2 w-56 bg-gray-800 rounded-lg shadow-lg z-50 py-2">
                <div class="px-4 py-2 border-b border-gray-700">
                  <div class="text-gray-400 text-sm">Chào,</div>
                  <div class="font-bold text-white">{{ userEmail }}</div>
                </div>
                <router-link to="/favorites" class="menu-item">
                  <i class="fas fa-heart mr-2 text-red-400"></i> Yêu thích
                </router-link>
                <router-link to="/list" class="menu-item">
                  <i class="fas fa-plus mr-2 text-blue-400"></i> Danh sách
                </router-link>
                <router-link to="/continue-watching" class="menu-item">
                  <i class="fas fa-history mr-2 text-yellow-400"></i> Xem tiếp
                </router-link>
                <router-link to="/account" class="menu-item">
                  <i class="fas fa-user mr-2 text-green-400"></i> Tài khoản
                </router-link>
                <router-link to="/membership" class="menu-item">
                  <i class="fas fa-crown mr-2 text-yellow-400"></i> Gói VIP
                </router-link>
                <button @click="logout" class="menu-item w-full text-left">
                  <i class="fas fa-sign-out-alt mr-2 text-gray-400"></i> Thoát
                </button>
              </div>
            </div>
          </template>
          <template v-else>
            <button class="btn-modern flex items-center space-x-2" @click="modalStore.openLoginModal()">
              <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="8" r="4" />
                <path d="M6 20v-2a4 4 0 014-4h0a4 4 0 014 4v2" />
              </svg>
              <span>Thành viên</span>
            </button>
          </template>
        </div>
      </nav>
    </header>
    <LoginModal v-if="modalStore.isLoginModalOpen" @close="modalStore.closeLoginModal()" @show-register="switchToRegister" />
    <RegisterModal v-if="modalStore.isRegisterModalOpen" @close="modalStore.closeRegisterModal()" @show-login="switchToLogin" />

    <!-- Mobile menu overlay -->
    <transition name="mobile-menu-fade-slide">
      <div v-if="mobileMenuOpen && !isAdminRoute" class="fixed top-17 left-4 z-50 w-72 bg-[#232846] rounded-xl shadow-lg p-6 flex flex-col items-start xl:hidden">
        <button class="absolute top-2 right-2 text-white" @click="closeMobileMenu">
          <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <line x1="18" y1="6" x2="6" y2="18" />
            <line x1="6" y1="6" x2="18" y2="18" />
          </svg>
        </button>
        <router-link to="/" class="flex items-center mb-6 justify-start" @click="closeMobileMenu">
          <img src="/images/logo/tmoviee2.png" alt="Logo" class="h-10 w-10 mr-2" />
          <span class="font-bold text-lg">RoPhim</span>
        </router-link>
        <router-link to="/movies" class="menu-link block py-2 text-white text-left w-full" @click="closeMobileMenu">Tìm phim</router-link>
        <router-link to="/single" class="menu-link block py-2 text-white text-left w-full" @click="closeMobileMenu">Phim Lẻ</router-link>
        <router-link to="/series" class="menu-link block py-2 text-white text-left w-full" @click="closeMobileMenu">Phim Bộ</router-link>
        <router-link to="/actors" class="menu-link block py-2 text-white text-left w-full" @click="closeMobileMenu">Diễn viên</router-link>
        <!-- Thêm các link khác nếu cần -->
      </div>
    </transition>

    <main class="flex-grow">
      <router-view></router-view>
    </main>
    <Footer v-if="!isAdminRoute" />
  </div>
</template>

<script setup>
import axios from 'axios';
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';
import CountryDropdown from './components/CountryDropdown.vue';
import Footer from './components/Footer.vue';
import GenreDropdown from './components/GenreDropdown.vue';
import LoginModal from './components/LoginModal.vue';
import RegisterModal from './components/RegisterModal.vue';
import { useModalStore } from './stores/modal';

const isLoggedIn = ref(false);
const userEmail = ref('');
const userAvatar = ref('');
const isScrolled = ref(false);
const menuOpen = ref(false);
const mobileMenuOpen = ref(false);
const modalStore = useModalStore();
const isVip = ref(false);
const searchQuery = ref('');

const route = useRoute();
const router = useRouter();
const isAdminRoute = computed(() => route.path.startsWith('/admin'));

const handleScroll = () => {
  isScrolled.value = window.scrollY > 20;
};

const switchToRegister = () => {
  modalStore.closeLoginModal();
  modalStore.openRegisterModal();
};

const switchToLogin = () => {
  modalStore.closeRegisterModal();
  modalStore.openLoginModal();
};

function closeMenu() {
  menuOpen.value = false;
}

function getAvatarUrl(avatar) {
  if (!avatar) return '/default-avatar.png';
  if (avatar.startsWith('/images/avatars')) {
    return window.location.origin + avatar;
  }
  return avatar;
}

const fetchMembership = async () => {
  try {
    const token = localStorage.getItem('token');
    if (!token) {
      isVip.value = false;
      return;
    }
    const res = await axios.get('/api/user/membership', { headers: { Authorization: `Bearer ${token}` } });
    isVip.value = !!res.data.active;
  } catch (e) {
    isVip.value = false;
  }
};

const checkLogin = () => {
  const token = localStorage.getItem('token');
  if (token) {
    isLoggedIn.value = true;
    const user = localStorage.getItem('user');
    if (user) {
      const userObj = JSON.parse(user);
      userEmail.value = userObj.name || userObj.email || 'Tài khoản';
      userAvatar.value = userObj.avatar || '';
      fetchMembership();
    } else {
      axios.get('/api/user/me', { 
        headers: { Authorization: `Bearer ${token}` } 
      }).then(res => {
        const userData = res.data;
        userEmail.value = userData.name || userData.email || 'Tài khoản';
        userAvatar.value = userData.avatar || '';
        localStorage.setItem('user', JSON.stringify(userData));
        fetchMembership();
      }).catch(() => {
        userEmail.value = 'Tài khoản';
        userAvatar.value = '';
        isVip.value = false;
      });
    }
  } else {
    isLoggedIn.value = false;
    userEmail.value = '';
    userAvatar.value = '';
    isVip.value = false;
  }
};

const logout = () => {
  localStorage.removeItem('token');
  localStorage.setItem('showSuccessToast', 'Đăng xuất thành công!');
  isLoggedIn.value = false;
  userEmail.value = '';
  userAvatar.value = '';
  window.location.reload();
};

function toggleMobileMenu() {
  mobileMenuOpen.value = !mobileMenuOpen.value;
}

function closeMobileMenu() {
  mobileMenuOpen.value = false;
}

const handleSearch = () => {
  if (searchQuery.value.trim()) {
    router.push({
      path: '/search',
      query: { q: searchQuery.value.trim() }
    });
  }
};

onMounted(() => {
  window.addEventListener('scroll', handleScroll);
  checkLogin();
  const msg = localStorage.getItem('showSuccessToast');
  if (msg) {
    toast.success(msg);
    localStorage.removeItem('showSuccessToast');
  }
  if (route.query['ban-hay-dang-nhap']) {
    toast.error('Bạn cần đăng nhập để truy cập trang này!', { 
      autoClose: 4000,
      theme: "dark"
    });
  }
});

// Watch khi query thay đổi
watch(
  () => route.query['ban-hay-dang-nhap'],
  (val) => {
    if (val) {
      toast.error('Bạn cần đăng nhập để truy cập trang này!', { 
        autoClose: 4000,
        theme: "dark"
      });
    }
  }
);

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll);
});
</script>

<style scoped>
.main-header {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  z-index: 50;
  background: rgba(26, 32, 44, 0.5);
  backdrop-filter: blur(16px);
  -webkit-backdrop-filter: blur(16px);
  border-bottom: 1px solid rgba(255,255,255,0.05);
  transition: background 0.3s cubic-bezier(0.4,0,0.2,1), box-shadow 0.3s cubic-bezier(0.4,0,0.2,1), padding 0.3s, height 0.3s;
  box-shadow: 0 2px 8px 0 rgba(0,0,0,0.04);
}
.nav-content {
  transition: padding 0.3s, min-height 0.3s;
  min-height: 50px;
  padding-top: 0.7rem;
  padding-bottom: 0.7rem;
}
.logo-link {
  transition: font-size 0.3s, padding 0.3s;
  display: flex;
  align-items: center;
}
.logo-text {
  font-size: 1.4rem;
  font-weight: bold;
  background: linear-gradient(45deg, #f56565, #ed8936);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  transition: font-size 0.3s;
}
.menu-group {
  transition: font-size 0.3s, gap 0.3s;
  font-size: 1rem;
  gap: 1.1rem;
}
.main-header.scrolled {
  background: rgba(26, 32, 44, 0.98);
  box-shadow: 0 4px 24px 0 rgba(0,0,0,0.12);
}
.main-header.scrolled .nav-content {
  min-height: 44px;
  padding-top: 0.3rem;
  padding-bottom: 0.3rem;
}
.main-header.scrolled .logo-text {
  font-size: 1.2rem;
}
.main-header.scrolled .menu-group {
  font-size: 0.95rem;
  gap: 1rem;
}
.gradient-text {
  background: linear-gradient(45deg, #f56565, #ed8936);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}
.search-bar {
  position: relative;
  display: flex;
  align-items: center;
  background: rgba(255,255,255,0.08);
  border-radius: 9999px;
  padding: 0.1rem 0.7rem 0.1rem 2rem;
  transition: box-shadow 0.2s;
  box-shadow: 0 2px 8px 0 rgba(0,0,0,0.04);
  min-height: 32px;
  max-width: 260px;
}
.search-bar:focus-within {
  box-shadow: 0 4px 16px 0 rgba(237, 137, 54, 0.15);
  background: rgba(255,255,255,0.15);
}
.search-icon {
  position: absolute;
  left: 0.5rem;
  color: #a0aec0;
  pointer-events: none;
  width: 16px;
  height: 16px;
  display: flex;
  align-items: center;
}
.search-input {
  background: transparent;
  border: none;
  outline: none;
  color: #fff;
  font-size: 0.95rem;
  width: 180px;
  padding: 0.3rem 0;
}
.search-input::placeholder {
  color: #cbd5e1;
  opacity: 1;
}
.btn-modern {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  background: linear-gradient(45deg, #f56565, #ed8936);
  border: none;
  color: white;
  padding: 0.5rem 1.25rem;
  border-radius: 9999px;
  font-weight: 600;
  font-size: 1rem;
  box-shadow: 0 2px 8px 0 rgba(237, 137, 54, 0.08);
  cursor: pointer;
  outline: none;
  display: flex;
  align-items: center;
}
.btn-modern:hover {
  transform: translateY(-2px) scale(1.04);
  box-shadow: 0 4px 16px 0 rgba(237, 137, 54, 0.18);
}
@media (max-width: 768px) {
  .search-input {
    width: 120px;
  }
}
.pt-header {
  padding-top: 130px;
  padding-bottom: 220px;
}
@media (max-width: 768px) {
  .pt-header {
    padding-top: 72px;
  }
}
.menu-item {
  display: flex;
  align-items: center;
  padding: 0.5rem 1rem;
  color: white;
  transition: background 0.2s;
  cursor: pointer;
  border: none;
  background: none;
  width: 100%;
  text-align: left;
}
.menu-item:hover {
  background-color: #374151;
}
.mobile-menu-fade-slide-enter-active,
.mobile-menu-fade-slide-leave-active {
  transition: opacity 0.25s cubic-bezier(0.4,0,0.2,1), transform 0.25s cubic-bezier(0.4,0,0.2,1);
}
.mobile-menu-fade-slide-enter-from,
.mobile-menu-fade-slide-leave-to {
  opacity: 0;
  transform: translateY(-30px) scale(0.96);
}
.mobile-menu-fade-slide-enter-to,
.mobile-menu-fade-slide-leave-from {
  opacity: 1;
  transform: translateY(0) scale(1);
}
.menu-link {
  transition: color 0.18s, transform 0.18s;
}
.menu-link:hover {
  color: #facc15;
  transform: translateX(6px) scale(1.04);
}
</style>
