import axios from 'axios'
import { defineStore } from 'pinia'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('token') || null,
    redirectAfterLogin: null
  }),

  getters: {
    isAuthenticated: (state) => !!state.token,
    userRole: (state) => state.user?.role || null
  },

  actions: {
    async login(credentials) {
      try {
        const response = await axios.post('/api/login', credentials)
        this.token = response.data.token
        this.user = response.data.user
        localStorage.setItem('token', this.token)
        axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
        
        // Chỉ chuyển hướng khi đăng nhập thành công và user là admin
        if (response.data.user && response.data.user.role === 'admin') {
          window.location.href = '/admin'
          return
        }
        
        return response
      } catch (error) {
        console.error('Login error:', error)
        throw error
      }
    },

    async register(userData) {
      try {
        const response = await axios.post('/api/register', userData)
        this.token = response.data.token
        this.user = response.data.user
        localStorage.setItem('token', this.token)
        axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
        return response
      } catch (error) {
        throw error
      }
    },

    async logout() {
      try {
        await axios.post('/api/logout')
        this.clearAuth()
      } catch (error) {
        console.error('Logout error:', error)
        this.clearAuth()
      }
    },

    async fetchUser() {
      try {
        const response = await axios.get('/api/user/me')
        this.user = response.data
        return response
      } catch (error) {
        this.clearAuth()
        throw error
      }
    },

    clearAuth() {
      this.user = null
      this.token = null
      localStorage.removeItem('token')
      delete axios.defaults.headers.common['Authorization']
    },

    initializeAuth() {
      const token = localStorage.getItem('token')
      if (token) {
        this.token = token
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
        this.fetchUser()
      }
    }
  }
}) 