<template>
  <footer class="bg-gray-900 text-white py-12 border-t border-gray-800">
    <div class="container mx-auto px-4">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
        <!-- About -->
        <div class="hover-lift">
          <h3 class="text-xl font-bold mb-4">TMoviee</h3>
          <p class="text-gray-400">
            Website xem phim trực tuyến với chất lượng cao, cập nhật nhanh chóng và đa dạng thể loại.
          </p>
        </div>

        <!-- Quick Links -->
        <div>
          <h3 class="text-xl font-bold mb-4">Liên kết nhanh</h3>
          <ul class="space-y-2">
            <li><router-link to="/" class="text-gray-400 hover:text-white transition-colors duration-200">Trang chủ</router-link></li>
            <li><router-link to="/movies" class="text-gray-400 hover:text-white transition-colors duration-200">Phim mới</router-link></li>
            <li><router-link to="/series" class="text-gray-400 hover:text-white transition-colors duration-200">Phim bộ</router-link></li>
            <li><router-link to="/single" class="text-gray-400 hover:text-white transition-colors duration-200">Phim lẻ</router-link></li>
          </ul>
        </div>

        <!-- Categories -->
        <div>
          <h3 class="text-xl font-bold mb-4">Thể loại phim</h3>
          <ul class="space-y-2">
            <li><router-link to="/genre/hanh-dong" class="text-gray-400 hover:text-white transition-colors duration-200">Hành động</router-link></li>
            <li><router-link to="/genre/hai" class="text-gray-400 hover:text-white transition-colors duration-200">Hài hước</router-link></li>
            <li><router-link to="/genre/phieu-luu" class="text-gray-400 hover:text-white transition-colors duration-200">Phiêu lưu</router-link></li>
            <li><router-link to="/genre/kinh-di" class="text-gray-400 hover:text-white transition-colors duration-200">Kinh dị</router-link></li>
          </ul>
        </div>

        <!-- Contact -->
        <div>
          <h3 class="text-xl font-bold mb-4">Liên hệ</h3>
          <ul class="space-y-2">
            <li class="text-gray-400 hover:text-white transition-colors duration-200">Email: donhotu03@dev.gmail.com</li>
            <li class="text-gray-400 hover:text-white transition-colors duration-200">Facebook: Tú</li>
            <li class="text-gray-400 hover:text-white transition-colors duration-200">Github: <a href="https://github.com/tukiinn" class="hover:text-white">Tukiin</a></li>
          </ul>
        </div>
      </div>

      <!-- Copyright -->
      <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
        <p>&copy; {{ new Date().getFullYear() }} TMovie. All rights reserved.</p>
      </div>
    </div>
  </footer>
</template>

<script setup>
// Component logic here if needed
</script> 