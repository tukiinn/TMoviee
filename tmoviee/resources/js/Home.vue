<script setup>
import { onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import { toast } from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'

const route = useRoute()

function showAuthErrorToastIfNeeded() {
  console.log('Checking showAuthError flag...')
  const showAuthError = localStorage.getItem('showAuthError')
  console.log('showAuthError value:', showAuthError)
  
  if (showAuthError) {
    console.log('Showing auth error toast...')
    toast.error('Bạn cần đăng nhập để truy cập trang này!', { 
      autoClose: 4000,
      position: "top-right",
      theme: "dark"
    })
    localStorage.removeItem('showAuthError')
  }
}

onMounted(() => {
  console.log('Home component mounted')
  showAuthErrorToastIfNeeded()
})

watch(
  () => route.path,
  () => {
    console.log('Route path changed:', route.path)
    showAuthErrorToastIfNeeded()
  }
)
</script> 