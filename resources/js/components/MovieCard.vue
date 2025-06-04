<template>
  <div class="bg-gray-800 rounded-lg overflow-hidden shadow-lg hover:shadow-xl transition-shadow duration-300">
    <div class="relative">
      <img :src="movie.poster || '/poster-fallback.jpg'" :alt="movie.title" class="w-full h-[300px] object-cover">
      <div class="absolute top-2 right-2 bg-red-600 px-2 py-1 rounded text-sm">
        {{ movie.quality }}
      </div>
      <div class="absolute bottom-2 left-2 bg-gray-900 bg-opacity-75 px-2 py-1 rounded text-sm">
        {{ movie.duration }} phút
      </div>
    </div>
    <div class="p-4">
      <h3 class="font-bold text-lg mb-2 line-clamp-1">{{ movie.title }}</h3>
      <div class="flex items-center justify-between text-sm text-gray-400">
        <span>{{ movie.release_year }}</span>
        <div class="flex items-center">
          <span class="text-yellow-500 mr-1">★</span>
          <span>{{ movie.imdb_rating }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  movie: {
    type: Object,
    required: true
  }
})
</script>

<style scoped>
.movie-card {
  background-color: #1a1a1a;
  border-radius: 8px;
  overflow: hidden;
  transition: transform 0.3s ease;
}

.movie-card:hover {
  transform: translateY(-5px);
}

.movie-poster {
  position: relative;
  padding-top: 150%;
  overflow: hidden;
}

.movie-poster img {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.movie-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(to bottom, rgba(0,0,0,0) 0%, rgba(0,0,0,0.8) 100%);
  opacity: 0;
  transition: opacity 0.3s ease;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  padding: 1rem;
}

.movie-card:hover .movie-overlay {
  opacity: 1;
}

.movie-info {
  display: flex;
  justify-content: space-between;
  color: #fff;
}

.movie-year, .movie-rating {
  font-size: 0.9rem;
}

.movie-rating i {
  color: #ffd700;
  margin-right: 0.25rem;
}

.movie-actions {
  display: flex;
  justify-content: center;
  gap: 1rem;
}

.play-btn, .add-btn {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  border: none;
  background-color: rgba(255, 255, 255, 0.2);
  color: #fff;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.play-btn:hover, .add-btn:hover {
  background-color: #e50914;
}

.movie-details {
  padding: 1rem;
}

.movie-title {
  font-size: 1rem;
  margin-bottom: 0.5rem;
  color: #fff;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.movie-genres {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.genre-tag {
  font-size: 0.8rem;
  padding: 0.25rem 0.5rem;
  background-color: #333;
  color: #fff;
  border-radius: 4px;
}
</style> 