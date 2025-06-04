<template>
  <router-link
    :to="to"
    :class="[
      'flex items-center gap-3 px-4 py-2 rounded-lg transition font-semibold hover:bg-gray-700',
      collapsed ? 'justify-center' : 'justify-start',
      isActive ? 'bg-gray-900 text-yellow-400' : 'text-gray-300'
    ]"
    style="min-height: 40px;"
  >
    <el-icon :class="color" class="text-2xl">
      <component :is="icon" />
    </el-icon>
    <span v-show="!collapsed" class="ml-3">{{ label }}</span>
  </router-link>
</template>
<script setup>
import { ElIcon } from 'element-plus'
import { computed } from 'vue'
import { useRoute } from 'vue-router'
const props = defineProps(['icon', 'color', 'label', 'collapsed', 'to'])
const route = useRoute()
const isActive = computed(() => route.path === props.to)
</script> 