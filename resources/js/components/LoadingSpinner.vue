<template>
  <div class="flex items-center justify-center min-h-[400px]">
    <div class="relative">
      <!-- Outer ring: gradient vàng-xanh dương -->
      <div class="w-14 h-14 border-4 border-t-transparent border-b-transparent rounded-full animate-spin"
           style="border-left: 4px solid #facc15; border-right: 4px solid #3b82f6;"></div>
      <!-- Inner ring: gradient xanh dương-vàng, xoay ngược chiều -->
      <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-8 h-8 border-4 border-t-transparent border-b-transparent rounded-full animate-spin-reverse"
           style="border-left: 4px solid #3b82f6; border-right: 4px solid #facc15; animation-duration: 1.2s"></div>
    </div>
  </div>
</template>

<style scoped>
@keyframes spin-reverse {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(-360deg); }
}
.animate-spin-reverse {
  animation: spin-reverse 1s linear infinite;
}
</style> 