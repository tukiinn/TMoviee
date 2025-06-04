<template>
  <div :style="{ marginLeft: depth * 24 + 'px' }" class="mb-2">
    <div class="flex gap-3 py-2">
      <img :src="comment.user.avatar || '/images/default-avatar.png'" class="w-8 h-8 rounded-full object-cover" />
      <div class="flex-1">
        <div class="flex items-center gap-2">
          <span class="font-semibold text-white">{{ comment.user.name }}</span>
          <span v-if="comment.is_spoiler" class="ml-2 px-2 py-0.5 bg-red-600 text-white text-xs rounded">Spoiler</span>
          <span class="text-xs text-gray-400 ml-2">{{ formatDate(comment.created_at) }}</span>
        </div>
        <div class="text-gray-200 mt-1">{{ comment.content }}</div>
        <div class="flex items-center gap-4 mt-1 text-gray-400 text-xs">
          <button @click="$emit('like', comment)" :class="[comment.liked ? 'text-yellow-400' : '', 'flex items-center gap-1 hover:text-yellow-400 transition']">
            <i class="fas fa-thumbs-up"></i> {{ comment.likes_count || 0 }}
          </button>
          <button class="hover:text-blue-400 flex items-center gap-1" @click="$emit('reply', comment.id)"><i class="fas fa-reply"></i> Trả lời</button>
          <button v-if="canDeleteComment && canDeleteComment(comment)" @click="$emit('delete-comment', comment.id)" class="text-red-600 hover:text-red-800 ml-2">
            <i class="fas fa-trash"></i>
          </button>
        </div>
        <!-- Form trả lời -->
        <div v-if="replyBox[comment.id]" class="mt-2 ml-2">
          <textarea
            v-model="replyContent[comment.id]"
            rows="2"
            class="w-full bg-gray-800 text-white p-2 rounded border border-gray-700"
            placeholder="Nhập trả lời..."
            @keydown.enter.exact.prevent="$emit('submit-reply', comment.id)"
            @keydown.shift.enter.stop
          ></textarea>
          <div class="flex gap-2 mt-1">
            <button
              :disabled="isSubmittingReply && (isSubmittingReply[comment.id] || !replyContent[comment.id]?.trim())"
              @click="$emit('submit-reply', comment.id)"
              class="bg-yellow-400 hover:bg-yellow-300 text-gray-900 font-bold px-6 py-2 rounded-lg flex items-center gap-1 transition disabled:opacity-60"
            >
              <span v-if="isSubmittingReply && isSubmittingReply[comment.id]"><i class="fas fa-spinner fa-spin"></i></span>
              <span v-else>Gửi <i class="fas fa-paper-plane ml-1"></i></span>
            </button>
            <button @click="$emit('hide-reply', comment.id)" class="text-gray-400">Huỷ</button>
          </div>
        </div>
        <!-- Đệ quy replies -->
        <div v-if="comment.replies && comment.replies.length">
          <CommentItem
            v-for="reply in comment.replies"
            :key="reply.id"
            :comment="reply"
            :depth="depth + 1"
            :reply-box="replyBox"
            :reply-content="replyContent"
            :is-submitting-reply="isSubmittingReply"
            :can-delete-comment="canDeleteComment"
            @like="$emit('like', $event)"
            @reply="$emit('reply', $event)"
            @submit-reply="$emit('submit-reply', $event)"
            @hide-reply="$emit('hide-reply', $event)"
            @delete-comment="$emit('delete-comment', $event)"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import CommentItem from './CommentItem.vue'
const props = defineProps({
  comment: Object,
  depth: { type: Number, default: 0 },
  replyBox: Object,
  replyContent: Object,
  isSubmittingReply: Object,
  canDeleteComment: Function,
})
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
</script> 