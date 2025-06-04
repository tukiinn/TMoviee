import { defineStore } from 'pinia'

export const useModalStore = defineStore('modal', {
  state: () => ({
    isLoginModalOpen: false,
    isRegisterModalOpen: false
  }),
  
  actions: {
    openLoginModal() {
      this.isLoginModalOpen = true
    },
    closeLoginModal() {
      this.isLoginModalOpen = false
    },
    openRegisterModal() {
      this.isRegisterModalOpen = true
    },
    closeRegisterModal() {
      this.isRegisterModalOpen = false
    }
  }
}) 