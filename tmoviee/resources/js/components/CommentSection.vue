<template>
  <div class="comment-section mt-8 bg-gray-900 rounded-xl p-6">
    <!-- Header & Tabs -->
    <div class="flex items-center gap-4 mb-4">
      <h3 class="text-2xl font-bold">Bình luận <span class="font-normal text-gray-400">({{ totalComments }})</span></h3>
      <button class="px-4 py-1 rounded border border-gray-700 bg-gray-800 text-white font-semibold mr-2 focus:outline-none">Bình luận</button>
      <button class="px-4 py-1 rounded border border-gray-700 bg-gray-800 text-gray-400 font-semibold focus:outline-none">Đánh giá</button>
    </div>

    <!-- Form nhập bình luận mới -->
    <div v-if="isAuthenticated" class="mb-6">
      <div class="flex items-center mb-2">
        <img :src="auth.user?.avatar || '/images/default-avatar.png'" class="w-12 h-12 rounded-full object-cover mr-3" />
        <div>
          <div class="text-sm text-gray-400">Bình luận với tên</div>
          <div class="font-bold text-white">{{ auth.user?.name }}</div>
        </div>
      </div>
      <div class="rounded-lg bg-[#23242b] p-3 border border-[#35363e] text-sm">
        <div class="flex justify-between items-center mb-1">
          <span></span>
          <span class="text-xs text-gray-500">{{ newComment.length }} / 1000</span>
        </div>
        <textarea
          id="comment-input"
          v-model="newComment"
          class="w-full bg-transparent text-white p-0 border-0 focus:ring-0 resize-none outline-none"
          rows="4"
          maxlength="1000"
          placeholder="Viết bình luận"
          @keydown.enter.exact.prevent="submitComment"
          @keydown.shift.enter.stop
        ></textarea>
        <div class="flex items-center justify-between mt-4">
          <button type="button" @click="isSpoiler = !isSpoiler" class="flex items-center gap-2 focus:outline-none">
            <span class="relative inline-block w-10 align-middle select-none transition duration-200 ease-in">
              <span :class="[isSpoiler ? 'bg-yellow-400' : 'bg-gray-600', 'block w-10 h-6 rounded-full transition-colors duration-200']"></span>
              <span :class="[isSpoiler ? 'translate-x-4 bg-white' : 'translate-x-0 bg-gray-300', 'absolute left-0 top-0 w-6 h-6 rounded-full shadow transform transition-transform duration-200']"></span>
            </span>
            <span class="text-gray-400 text-sm">Tiết lộ?</span>
          </button>
          <button
            :disabled="isSubmitting || !newComment.trim()"
            @click="submitComment"
            class="bg-yellow-400 hover:bg-yellow-300 text-gray-900 font-bold px-6 py-2 rounded-lg flex items-center gap-1 transition disabled:opacity-60"
          >
            <span v-if="isSubmitting"><i class="fas fa-spinner fa-spin"></i></span>
            <span v-else>Gửi <i class="fas fa-paper-plane ml-1"></i></span>
          </button>
        </div>
      </div>
    </div>
    <div v-else class="mb-6">
      <p class="text-gray-400">
        Vui lòng <button @click="openLoginModal" class="text-yellow-400 hover:underline font-semibold">đăng nhập</button> để bình luận
      </p>
    </div>

    <!-- Danh sách bình luận -->
    <div class="divide-y divide-gray-800">
      <div v-if="loading" class="text-center py-4">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-yellow-400 mx-auto"></div>
      </div>
      <div v-else-if="comments.length === 0" class="text-center py-4 text-gray-500">
        Chưa có bình luận nào. Hãy là người đầu tiên bình luận!
      </div>
      <div v-else>
        <CommentItem
          v-for="comment in comments"
          :key="comment.id"
          :comment="comment"
          :depth="0"
          :reply-box="replyBox"
          :reply-content="replyContent"
          :is-submitting-reply="isSubmittingReply"
          :can-delete-comment="canDeleteComment"
          @like="likeComment"
          @reply="showReplyBox"
          @submit-reply="submitReply"
          @hide-reply="hideReplyBox"
          @delete-comment="deleteComment"
        />
      </div>
    </div>

    <!-- Phân trang -->
    <div v-if="totalPages > 1" class="flex justify-center mt-6">
      <button
        v-for="page in totalPages"
        :key="page"
        @click="loadComments(page)"
        :class="[
          'mx-1 px-3 py-1 rounded',
          currentPage === page
            ? 'bg-yellow-400 text-gray-900 font-bold'
            : 'bg-gray-800 text-gray-400 hover:bg-gray-700'
        ]"
      >
        {{ page }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { useAuthStore } from '@/stores/auth'
import { useModalStore } from '@/stores/modal'
import axios from 'axios'
import { computed, onMounted, ref, watch } from 'vue'
import CommentItem from './CommentItem.vue'

const props = defineProps({
  movieSlug: {
    type: String,
    required: true
  }
})

const auth = useAuthStore()
const modalStore = useModalStore()
const comments = ref([])
const newComment = ref('')
const loading = ref(false)
const isSubmitting = ref(false)
const currentPage = ref(1)
const totalPages = ref(1)
const totalComments = ref(0)
const isSpoiler = ref(false)
const replyBox = ref({})
const replyContent = ref({})
const isSubmittingReply = ref({})

const isAuthenticated = computed(() => auth.isAuthenticated)

const emit = defineEmits(['loading'])
watch(loading, (val) => emit('loading', val))

const loadComments = async (page = 1) => {
  loading.value = true
  try {
    const response = await axios.get(`/api/movies/${props.movieSlug}/comments?page=${page}`, {
      headers: {
        Authorization: `Bearer ${auth.token}`
      }
    })
    comments.value = response.data.data.map(c => ({ ...c, showSpoil: false }))
    currentPage.value = response.data.current_page
    totalPages.value = response.data.last_page
    totalComments.value = response.data.total
  } catch (error) {
    console.error('Lỗi khi tải bình luận:', error)
    if (error.response?.status === 401) {
      auth.clearAuth()
    }
  } finally {
    loading.value = false
  }
}

const submitComment = async () => {
  if (!newComment.value.trim()) return
  
  isSubmitting.value = true
  try {
    const response = await axios.post(`/api/movies/${props.movieSlug}/comments`, {
      content: newComment.value,
      is_spoiler: isSpoiler.value
    }, {
      headers: {
        Authorization: `Bearer ${auth.token}`
      }
    })
    newComment.value = ''
    await loadComments(currentPage.value)
  } catch (error) {
    console.error('Lỗi khi gửi bình luận:', error)
    if (error.response?.status === 401) {
      auth.clearAuth()
    }
  } finally {
    isSubmitting.value = false
  }
}

const deleteComment = async (commentId) => {
  if (!confirm('Bạn có chắc chắn muốn xóa bình luận này?')) return
  
  try {
    await axios.delete(`/api/comments/${commentId}`, {
      headers: {
        Authorization: `Bearer ${auth.token}`
      }
    })
    await loadComments(currentPage.value)
  } catch (error) {
    console.error('Lỗi khi xóa bình luận:', error)
    if (error.response?.status === 401) {
      auth.clearAuth()
    }
  }
}

const canDeleteComment = (comment) => {
  return auth.user?.id === comment.user_id || auth.user?.role === 'admin'
}

const formatDate = (date) => {
  const d = new Date(date)
  const now = new Date()
  const diff = (now - d) / 1000
  if (diff < 60) return 'Vừa xong'
  if (diff < 3600) return Math.floor(diff / 60) + ' phút trước'
  if (diff < 86400) return Math.floor(diff / 3600) + ' giờ trước'
  if (diff < 2592000) return Math.floor(diff / 86400) + ' ngày trước'
  return d.toLocaleDateString('vi-VN')
}

const openLoginModal = () => {
  modalStore.openLoginModal()
}

const likeComment = async (comment) => {
  if (!auth.isAuthenticated) {
    modalStore.openLoginModal()
    return
  }
  try {
    if (comment.liked) {
      await axios.delete(`/api/comments/${comment.id}/like`, {
        headers: { Authorization: `Bearer ${auth.token}` }
      })
      comment.liked = false
      comment.likes_count--
    } else {
      await axios.post(`/api/comments/${comment.id}/like`, {}, {
        headers: { Authorization: `Bearer ${auth.token}` }
      })
      comment.liked = true
      comment.likes_count++
    }
  } catch (e) {
    // Xử lý lỗi nếu cần
  }
}

const showReplyBox = (commentId) => {
  if (!auth.isAuthenticated) {
    modalStore.openLoginModal()
    return
  }
  replyBox.value[commentId] = true
}
const hideReplyBox = (commentId) => {
  replyBox.value[commentId] = false
  replyContent.value[commentId] = ''
}
const submitReply = async (parentId) => {
  const content = replyContent.value[parentId]?.trim()
  if (!content) return
  isSubmittingReply.value[parentId] = true
  try {
    await axios.post(`/api/movies/${props.movieSlug}/comments`, {
      content,
      parent_id: parentId
    }, {
      headers: { Authorization: `Bearer ${auth.token}` }
    })
    replyContent.value[parentId] = ''
    replyBox.value[parentId] = false
    await loadComments(currentPage.value)
  } catch (e) {}
  finally {
    isSubmittingReply.value[parentId] = false
  }
}

onMounted(() => {
  loadComments()
})
</script>

<style scoped>
.blur-sm {
  filter: blur(6px);
  transition: filter 0.3s;
}
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style> 