import axios from 'axios';
import ElementPlus from 'element-plus';
import 'element-plus/dist/index.css';
import { createPinia } from 'pinia';
import { createApp } from 'vue';
import Vue3Toastify, { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';
import App from './App.vue';
import './bootstrap';
import clickOutside from './directives/clickOutside';
import router from './router';
import { useAuthStore } from './stores/auth';

// Set CSRF token for all axios requests
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// Configure axios interceptors for error handling
axios.interceptors.response.use(
  response => response,
  error => {
    const message = error.response?.data?.message || 'Đã xảy ra lỗi!';
    toast.error(message);
    return Promise.reject(error);
  }
);

const app = createApp(App);
app.directive('click-outside', clickOutside);

// Configure Toastify
app.use(Vue3Toastify, {
  autoClose: 3000,
  position: toast.POSITION.BOTTOM_RIGHT,
  theme: 'dark',
  style: {
    background: '#1a202c',
    color: '#fff',
    borderRadius: '8px',
    padding: '12px 24px',
    fontSize: '14px',
    fontWeight: '500',
    boxShadow: '0 4px 12px rgba(0, 0, 0, 0.15)',
  },
  transition: 'bounce',
  containerId: 'toast-container',
  closeOnClick: false,
  pauseOnHover: true,
  pauseOnFocusLoss: true,
  draggable: true,
  draggablePercent: 0.6,
  showCloseButton: true,
  newestOnTop: true,
  limit: 3,
});

app.use(router);
app.use(ElementPlus);

app.component('movie-card', () => import('./components/movies/MovieCard.vue'));

const pinia = createPinia()
app.use(pinia)

// Khởi tạo auth store
const authStore = useAuthStore()
authStore.initializeAuth()

app.mount('#app');
