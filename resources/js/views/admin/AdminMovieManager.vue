<template>
  <AdminLayout>
    <div class="flex-1 p-4 min-h-screen">
      <div class="p-6 pt-header min-h-screen">
        <div class="flex justify-between items-center mb-6">
          <el-input v-model="search" placeholder="Tìm kiếm phim..." style="max-width: 300px" @keyup.enter="onSearch" clearable @clear="onSearch" class="shadow-sm" />
          <div class="flex gap-2">
            <button @click="openImportModal" class="bg-green-500 hover:bg-green-400 text-white font-bold px-5 py-2 rounded-lg shadow flex items-center">
              <i class="fas fa-download mr-2"></i> Import từ OPhim
            </button>
            <button @click="openAddModal" class="bg-yellow-400 hover:bg-yellow-300 text-gray-900 font-bold px-5 py-2 rounded-lg shadow flex items-center">
              <i class="fas fa-plus mr-2"></i> Thêm phim mới 
            </button>
          </div>
        </div>
        <el-table :data="movies" style="width: 100%" v-loading="loading" class="shadow-md rounded-lg overflow-hidden bg-[#23262f] text-white">
          <el-table-column prop="title" label="Tên phim" min-width="180">
            <template #default="{ row }">
              <span class="text-white">{{ row.title }}</span>
            </template>
          </el-table-column>
          <el-table-column prop="release_year" label="Năm" width="80">
            <template #default="{ row }">
              <span class="text-white">{{ row.release_year }}</span>
            </template>
          </el-table-column>
          <el-table-column prop="country.name" label="Quốc gia" min-width="120">
            <template #default="{ row }">
              <span class="text-white">{{ row.country.name }}</span>
            </template>
          </el-table-column>
          <el-table-column label="Thể loại" min-width="160">
            <template #default="{ row }">
              <el-tag v-for="g in row.genres" :key="g.id" class="mr-1" size="small">{{ g.name }}</el-tag>
            </template>
          </el-table-column>
          <el-table-column prop="quality" label="Chất lượng" width="100">
            <template #default="{ row }">
              <span class="text-white">{{ row.quality }}</span>
            </template>
          </el-table-column>
          <el-table-column prop="is_series" label="Loại" width="80">
            <template #default="{ row }">
              <el-tag :type="row.is_series ? 'success' : 'info'">{{ row.is_series ? 'Phim Bộ' : 'Phim Lẻ' }}</el-tag>
            </template>
          </el-table-column>
          <el-table-column label="Hành động" width="160">
            <template #default="{ row }">
              <button @click="openEditModal(row)" class="px-3 py-1 rounded bg-blue-500 text-white hover:bg-blue-600 mr-2"><i class="fas fa-edit"></i></button>
              <button @click="confirmDelete(row)" class="px-3 py-1 rounded bg-red-500 text-white hover:bg-red-600"><i class="fas fa-trash"></i></button>
            </template>
          </el-table-column>
        </el-table>
        <el-pagination
          class="mt-4 rounded-lg bg-[#23262f] px-4 py-2"
          background
          layout="prev, pager, next"
          :total="total"
          :page-size="pageSize"
          :current-page="page"
          @update:current-page="onPageChange"
        />

        <!-- Modal Thêm/Sửa -->
        <el-dialog :title="editMovie ? 'Sửa phim' : 'Thêm phim mới'" v-model="modalVisible" width="600px" :close-on-click-modal="false" class="rounded-lg shadow-lg">
          <el-form :model="form" :rules="rules" ref="formRef" label-width="120px" label-position="left">
            <el-form-item label="Tên phim" prop="title">
              <el-input v-model="form.title" class="shadow-sm" />
            </el-form-item>
            <el-form-item label="Slug" prop="slug">
              <el-input v-model="form.slug" class="shadow-sm" />
            </el-form-item>
            <el-form-item label="Sub title" prop="sub_title">
              <el-input v-model="form.sub_title" class="shadow-sm" />
            </el-form-item>
            <el-form-item label="Mô tả" prop="description">
              <el-input type="textarea" v-model="form.description" class="shadow-sm" />
            </el-form-item>
            <el-form-item label="Poster" prop="poster">
              <el-input v-model="form.poster" class="shadow-sm" />
            </el-form-item>
            <el-form-item label="Banner" prop="banner">
              <el-input v-model="form.banner" class="shadow-sm" />
            </el-form-item>
            <el-form-item label="Trailer URL" prop="trailer_url">
              <el-input v-model="form.trailer_url" class="shadow-sm" />
            </el-form-item>
            <el-form-item label="Video URL" prop="video_url">
              <el-input v-model="form.video_url" class="shadow-sm" />
            </el-form-item>
            <el-form-item label="Thời lượng" prop="duration">
              <el-input v-model="form.duration" class="shadow-sm" />
            </el-form-item>
            <el-form-item label="Năm phát hành" prop="release_year">
              <el-input-number v-model="form.release_year" :min="1900" :max="2100" class="shadow-sm" />
            </el-form-item>
            <el-form-item label="Chất lượng" prop="quality">
              <el-input v-model="form.quality" class="shadow-sm" />
            </el-form-item>
            <el-form-item label="Độ tuổi" prop="age_rating">
              <el-input v-model="form.age_rating" class="shadow-sm" />
            </el-form-item>
            <el-form-item label="IMDb" prop="imdb_rating">
              <el-input-number v-model="form.imdb_rating" :min="0" :max="10" :step="0.1" class="shadow-sm" />
            </el-form-item>
            <el-form-item label="Loại phim" prop="is_series">
              <el-switch v-model="form.is_series" active-text="Phim Bộ" inactive-text="Phim Lẻ" />
            </el-form-item>
            <template v-if="form.is_series">
              <el-form-item label="Tổng tập" prop="total_episodes">
                <el-input-number v-model="form.total_episodes" :min="1" class="shadow-sm" />
              </el-form-item>
              <el-form-item label="Tập hiện tại" prop="current_episode">
                <el-input-number v-model="form.current_episode" :min="1" class="shadow-sm" />
              </el-form-item>
            </template>
            <el-form-item label="Danh sách tập phim">
              <div class="space-y-4">
                <div v-for="(episode, index) in form.episodes" :key="index" class="bg-gray-100 p-4 rounded-lg">
                  <div class="flex justify-between items-center mb-2">
                    <h4 class="font-medium">Tập {{ index + 1 }}</h4>
                    <el-button type="danger" size="small" @click="removeEpisode(index)">
                      <i class="fas fa-trash"></i>
                    </el-button>
                  </div>
                  <div class="grid grid-cols-2 gap-4">
                    <el-form-item label="Tên tập" :prop="'episodes.' + index + '.name'">
                      <el-input v-model="episode.name" placeholder="Tên tập phim" />
                    </el-form-item>
                    <el-form-item label="Slug" :prop="'episodes.' + index + '.slug'">
                      <el-input v-model="episode.slug" placeholder="slug-tap-phim" />
                    </el-form-item>
                    <el-form-item label="Link Embed" :prop="'episodes.' + index + '.link_embed'">
                      <el-input v-model="episode.link_embed" placeholder="Link embed video" />
                    </el-form-item>
                    <el-form-item label="Link M3U8" :prop="'episodes.' + index + '.link_m3u8'">
                      <el-input v-model="episode.link_m3u8" placeholder="Link m3u8 video" />
                    </el-form-item>
                  </div>
                </div>
                <el-button type="primary" @click="addEpisode" class="w-full">
                  <i class="fas fa-plus mr-2"></i> Thêm tập phim
                </el-button>
              </div>
            </el-form-item>
            <el-form-item label="Nổi bật" prop="is_featured">
              <el-switch v-model="form.is_featured" />
            </el-form-item>
            <el-form-item label="Mới nhất" prop="is_latest">
              <el-switch v-model="form.is_latest" />
            </el-form-item>
            <el-form-item label="Trạng thái" prop="status">
              <el-switch v-model="form.status" active-text="Hiện" inactive-text="Ẩn" />
            </el-form-item>
            <el-form-item label="Quốc gia" prop="country_id">
              <el-select v-model="form.country_id" filterable placeholder="Chọn quốc gia" class="shadow-sm">
                <el-option v-for="c in countries" :key="c.id" :label="c.name" :value="c.id" />
              </el-select>
            </el-form-item>
            <el-form-item label="Đạo diễn" prop="director_id">
              <el-select v-model="form.director_id" filterable placeholder="Chọn đạo diễn" class="shadow-sm">
                <el-option v-for="d in directors" :key="d.id" :label="d.name" :value="d.id" />
              </el-select>
            </el-form-item>
            <el-form-item label="Thể loại" prop="genres">
              <el-select v-model="form.genres" multiple filterable placeholder="Chọn thể loại" class="shadow-sm">
                <el-option v-for="g in genres" :key="g.id" :label="g.name" :value="g.id" />
              </el-select>
            </el-form-item>
            <el-form-item label="Diễn viên" prop="actors">
              <el-select 
                v-model="selectedActorIds" 
                multiple 
                filterable 
                placeholder="Chọn diễn viên" 
                class="shadow-sm"
                @change="handleActorSelect"
              >
                <el-option 
                  v-for="actor in actors" 
                  :key="actor.id" 
                  :label="actor.name" 
                  :value="actor.id"
                />
              </el-select>
              <div class="mt-2">
                <el-tag 
                  v-for="actor in form.actors" 
                  :key="actor.id" 
                  class="mr-2 mb-2"
                  closable
                  @close="() => { form.actors = form.actors.filter(a => a.id !== actor.id); selectedActorIds = selectedActorIds.filter(id => id !== actor.id); }"
                >
                  <strong>{{ getActorName(actor.id) }}</strong>
                  <span v-if="actor.character_name"> ({{ actor.character_name }})</span>
                </el-tag>
              </div>
            </el-form-item>
          </el-form>
          <template #footer>
            <el-button @click="modalVisible = false" class="shadow-sm">Hủy</el-button>
            <el-button type="primary" @click="submitForm" class="shadow-sm">{{ editMovie ? 'Cập nhật' : 'Tạo mới' }}</el-button>
          </template>
        </el-dialog>

        <!-- Thêm dialog nhập tên nhân vật -->
        <el-dialog
          v-model="characterDialogVisible"
          title="Nhập tên nhân vật"
          width="30%"
          :close-on-click-modal="false"
        >
          <el-form>
            <el-form-item label="Diễn viên">
              <span>{{ selectedActor?.name }}</span>
            </el-form-item>
            <el-form-item label="Tên nhân vật">
              <el-input v-model="characterName" placeholder="Nhập tên nhân vật" />
            </el-form-item>
          </el-form>
          <template #footer>
            <el-button @click="characterDialogVisible = false">Hủy</el-button>
            <el-button type="primary" @click="confirmCharacterName">Xác nhận</el-button>
          </template>
        </el-dialog>

        <!-- Modal Import từ OPhim -->
        <el-dialog
          title="Import phim từ OPhim"
          v-model="importModalVisible"
          width="90%"
          :close-on-click-modal="false"
          class="rounded-lg shadow-lg"
        >
          <div class="mb-4 flex gap-4">
            <el-input
              v-model="importPage"
              type="number"
              placeholder="Nhập số trang"
              class="w-32"
              :min="1"
            />
            <el-button type="primary" @click="fetchOPhimMovies" :loading="importing">
              Lấy danh sách phim
            </el-button>
            <el-button 
              type="success" 
              @click="importSelectedMovies" 
              :disabled="!selectedMovies.length"
              :loading="importingSelected"
            >
              Import đã chọn ({{ selectedMovies.length }})
            </el-button>
          </div>

          <el-table 
            :data="ophimMovies" 
            style="width: 100%" 
            v-loading="importing" 
            class="shadow-md rounded-lg overflow-hidden bg-[#23262f] text-white"
            @selection-change="handleSelectionChange"
          >
            <el-table-column type="selection" width="55" />
            <el-table-column label="Ảnh" width="70">
              <template #default="{ row }">
                <img
                  :src="getMovieThumb(row.poster_url || row.thumb_url)"
                  alt="poster"
                  class="w-12 h-16 object-cover rounded"
                  @error="event => event.target.src = '/default-poster.png'"
                />
              </template>
            </el-table-column>
            <el-table-column prop="name" label="Tên phim" min-width="200">
              <template #default="{ row }">
                <div class="flex items-center gap-2">
                  <div>
                    <div class="font-medium">{{ row.name }}</div>
                    <div class="text-sm text-gray-400">{{ row.origin_name }}</div>
                    <div class="flex items-center gap-2 mt-1">
                      <span v-if="row.tmdb?.vote_average" class="text-xs bg-blue-500 px-2 py-0.5 rounded flex items-center gap-1">
                        <i class="fa-solid fa-star text-yellow-400"></i> {{ row.tmdb.vote_average }}
                      </span>
                      <span v-if="row.imdb?.vote_average" class="text-xs bg-yellow-500 px-2 py-0.5 rounded flex items-center gap-1">
                        <i class="fa-brands fa-imdb text-white"></i> {{ row.imdb.vote_average }}
                      </span>
                    </div>
                  </div>
                </div>
              </template>
            </el-table-column>
            <el-table-column label="Loại" width="90">
              <template #default="{ row }">
                <el-tag :type="row.tmdb?.type === 'tv' ? 'success' : 'info'">
                  {{ row.tmdb?.type === 'tv' ? 'Phim Bộ' : 'Phim Lẻ' }}
                </el-tag>
              </template>
            </el-table-column>
            <el-table-column prop="year" label="Năm" width="80">
              <template #default="{ row }">
                <span class="text-white">{{ row.year }}</span>
              </template>
            </el-table-column>
            <el-table-column label="Cập nhật" width="160">
              <template #default="{ row }">
                <span class="text-white">{{ formatDate(row.modified.time) }}</span>
              </template>
            </el-table-column>
            <el-table-column label="Hành động" width="120">
              <template #default="{ row }">
                <button @click="showMovieDetail(row)" class="px-3 py-1 rounded bg-blue-500 text-white hover:bg-blue-600 mr-2">
                  <i class="fas fa-info-circle"></i>
                </button>
                <button @click="importMovie(row)" class="px-3 py-1 rounded bg-green-500 text-white hover:bg-green-600">
                  <i class="fas fa-download"></i>
                </button>
              </template>
            </el-table-column>
          </el-table>
        </el-dialog>

        <!-- Modal Chi tiết phim -->
        <el-dialog
          title="Chi tiết phim"
          v-model="detailModalVisible"
          width="800px"
          :close-on-click-modal="false"
          class="rounded-lg shadow-lg"
        >
          <div v-if="selectedMovieDetail" class="space-y-6">
            <!-- Header -->
            <div class="flex gap-6">
              <img :src="selectedMovieDetail.poster_url" class="w-48 h-72 object-cover rounded-lg shadow-lg" />
              <div class="flex-1">
                <h3 class="text-2xl font-bold mb-2">{{ selectedMovieDetail.name }}</h3>
                <p class="text-gray-400 mb-4">{{ selectedMovieDetail.origin_name }}</p>
                
                <!-- Ratings -->
                <div class="flex gap-4 mb-4">
                  <div v-if="selectedMovieDetail.tmdb?.vote_average" class="flex items-center gap-2">
                    <div class="bg-blue-500 text-white px-3 py-1 rounded-lg">
                      <i class="fas fa-star text-yellow-400"></i>
                      <span class="ml-1">{{ selectedMovieDetail.tmdb.vote_average }}</span>
                    </div>
                    <span class="text-sm text-gray-400">TMDB</span>
                  </div>
                  <div v-if="selectedMovieDetail.imdb?.vote_average" class="flex items-center gap-2">
                    <div class="bg-yellow-500 text-white px-3 py-1 rounded-lg">
                      <i class="fas fa-imdb"></i>
                      <span class="ml-1">{{ selectedMovieDetail.imdb.vote_average }}</span>
                    </div>
                    <span class="text-sm text-gray-400">IMDb</span>
                  </div>
                </div>

                <!-- Info Grid -->
                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <p class="text-gray-400">Năm: <span style="color: #7f7f5b">{{ selectedMovieDetail.year }}</span></p>
                    <p class="text-gray-400">Chất lượng: <span style="color: #7f7f5b">{{ selectedMovieDetail.quality }}</span></p>
                    <p class="text-gray-400">Thời lượng: <span style="color: #7f7f5b">{{ selectedMovieDetail.time }}</span></p>
                    <p class="text-gray-400">Ngôn ngữ: <span style="color: #7f7f5b">{{ selectedMovieDetail.lang }}</span></p>
                  </div>
                  <div>
                    <p class="text-gray-400">Quốc gia: <span style="color: #7f7f5b">{{ selectedMovieDetail.country?.[0]?.name }}</span></p>
                    <p class="text-gray-400">Tổng tập: <span style="color: #7f7f5b">{{ selectedMovieDetail.episode_total }}</span></p>
                    <p class="text-gray-400">Tập hiện tại: <span style="color: #7f7f5b">{{ selectedMovieDetail.episode_current }}</span></p>
                    <p class="text-gray-400">Ngày phát hành: <span style="color: #7f7f5b">{{ formatDate(selectedMovieDetail.modified.time) }}</span></p>
                  </div>
                </div>

                <!-- Categories -->
                <div class="mt-4">
                  <h4 class="text-gray-400 mb-2">Thể loại:</h4>
                  <div class="flex flex-wrap gap-2">
                    <el-tag v-for="cat in selectedMovieDetail.category" :key="cat.id">
                      {{ cat.name }}
                    </el-tag>
                  </div>
                </div>

                <!-- Director -->
                <div class="mt-4">
                  <h4 class="text-lg font-semibold mb-2">Đạo diễn</h4>
                  <div class="flex flex-wrap gap-2">
                    <el-tag v-for="director in selectedMovieDetail.director" :key="director">
                      {{ director }}
                    </el-tag>
                  </div>
                </div>

                <!-- Actors -->
                <div class="mt-4">
                  <h4 class="text-lg font-semibold mb-2">Diễn viên</h4>
                  <div class="flex flex-wrap gap-2">
                    <el-tag v-for="actor in selectedMovieDetail.actor" :key="actor">
                      {{ actor }}
                    </el-tag>
                  </div>
                </div>

                <!-- Content -->
                <div class="mt-4">
                  <h4 class="text-lg font-semibold mb-2">Nội dung</h4>
                  <div class="text-gray-600" v-html="selectedMovieDetail.content"></div>
                </div>

                <!-- Episodes -->
                <div v-if="selectedMovieDetail.type === 'series'" class="mt-4">
                  <h4 class="text-lg font-semibold mb-2">Danh sách tập phim</h4>
                  <div class="space-y-4">
                    <div v-for="server in selectedMovieDetail.episodes" :key="server.server_name" class="bg-gray-800 rounded-lg p-4">
                      <h5 class="font-medium mb-2">{{ server.server_name }}</h5>
                      <div class="grid grid-cols-4 gap-2">
                        <div v-for="ep in server.server_data" :key="ep.slug" 
                          class="bg-gray-700 rounded p-2 text-center hover:bg-gray-600 cursor-pointer">
                          Tập {{ ep.name }}
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Trailer -->
                <div v-if="selectedMovieDetail.trailer_url" class="mt-4">
                  <h4 class="text-lg font-semibold mb-2">Trailer</h4>
                  <div class="relative" style="padding-top: 56.25%">
                    <iframe 
                      :src="getEmbedUrl(selectedMovieDetail.trailer_url)"
                      class="absolute top-0 left-0 w-full h-full rounded-lg"
                      frameborder="0"
                      allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                      allowfullscreen
                    ></iframe>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </el-dialog>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/components/admin/AdminLayout.vue';
import axios from 'axios';
import { ElMessage, ElMessageBox } from 'element-plus';
import { onMounted, reactive, ref } from 'vue';

const movies = ref([]);
const total = ref(0);
const page = ref(1);
const pageSize = 20;
const loading = ref(false);
const search = ref('');

const modalVisible = ref(false);
const editMovie = ref(null);
const form = reactive({
  title: '', slug: '', sub_title: '', description: '', poster: '', banner: '', trailer_url: '', video_url: '', duration: '',
  release_year: new Date().getFullYear(), quality: '', age_rating: '', imdb_rating: null, is_series: false, total_episodes: null, current_episode: null,
  is_featured: false, is_latest: false, status: true, country_id: '', director_id: '', genres: [], actors: [], episodes: []
});
const formRef = ref();

const countries = ref([]);
const genres = ref([]);
const directors = ref([]);
const actors = ref([]);

const characterDialogVisible = ref(false);
const selectedActor = ref(null);
const characterName = ref('');

const selectedActorIds = ref([]);

// Import từ OPhim
const importModalVisible = ref(false);
const importPage = ref(1);
const ophimMovies = ref([]);
const importing = ref(false);
const selectedMovies = ref([]);
const importingSelected = ref(false);
const detailModalVisible = ref(false);
const selectedMovieDetail = ref(null);

const rules = {
  title: [{ required: true, message: 'Vui lòng nhập tên phim', trigger: 'blur' }],
  slug: [{ required: true, message: 'Vui lòng nhập slug', trigger: 'blur' }],
  release_year: [{ required: true, message: 'Vui lòng nhập năm', trigger: 'blur' }],
  country_id: [{ required: true, message: 'Chọn quốc gia', trigger: 'change' }],
  is_series: [{ required: true, message: 'Chọn loại phim', trigger: 'change' }],
  actors: [{ required: true, message: 'Vui lòng chọn diễn viên', trigger: 'change' }],
};

function fetchMovies() {
  loading.value = true;
  axios.get('/api/admin/movies', {
    params: { page: page.value, search: search.value }
  }).then(res => {
    movies.value = res.data.data.map(movie => ({
      ...movie,
      genres: movie.genres || [],
      actors: movie.actors || [],
      country: movie.country || { name: '' }
    }));
    total.value = res.data.total;
  }).finally(() => loading.value = false);
}

function fetchCountries() {
  axios.get('/api/countries').then(res => countries.value = res.data);
}
function fetchGenres() {
  axios.get('/api/genres').then(res => genres.value = res.data);
}
function fetchDirectors() {
  axios.get('/api/directors').then(res => directors.value = res.data);
}
function fetchActors() {
  axios.get('/api/actors').then(res => {
    actors.value = res.data.filter(actor => actor && actor.id && actor.name);
  });
}

function openAddModal() {
  editMovie.value = null;
  Object.assign(form, {
    title: '', slug: '', sub_title: '', description: '', poster: '', banner: '', trailer_url: '', video_url: '', duration: '',
    release_year: new Date().getFullYear(), quality: '', age_rating: '', imdb_rating: null, is_series: false, total_episodes: null, current_episode: null,
    is_featured: false, is_latest: false, status: true, country_id: '', director_id: '', genres: [], actors: [], episodes: []
  });
  selectedActorIds.value = [];
  modalVisible.value = true;
}

function openEditModal(row) {
  editMovie.value = row;
  Object.assign(form, {
    ...row,
    genres: row.genres ? row.genres.map(g => g.id) : [],
    actors: row.actors ? row.actors.map(a => ({
      id: a.id,
      character_name: a.pivot.character_name
    })) : [],
    imdb_rating: Number(row.imdb_rating),
    episodes: row.episodes || []
  });
  selectedActorIds.value = form.actors.map(a => a.id);
  modalVisible.value = true;
}

function submitForm() {
  formRef.value.validate(async valid => {
    if (!valid) return;
    try {
      const formData = {
        ...form,
        genres: form.genres.map(id => ({ id })),
        actors: form.actors // Không cần map lại vì đã đúng format
      };

      if (editMovie.value) {
        await axios.put(`/api/admin/movies/${editMovie.value.id}`, formData);
        ElMessage.success('Cập nhật thành công');
      } else {
        await axios.post('/api/admin/movies', formData);
        ElMessage.success('Tạo mới thành công');
      }
      modalVisible.value = false;
      fetchMovies();
    } catch (e) {
      ElMessage.error(e.response?.data?.message || 'Có lỗi xảy ra');
    }
  });
}

function confirmDelete(row) {
  ElMessageBox.confirm('Bạn chắc chắn muốn xóa phim này?', 'Xác nhận', {
    type: 'warning',
    confirmButtonText: 'Xóa',
    cancelButtonText: 'Hủy',
  }).then(async () => {
    await axios.delete(`/api/admin/movies/${row.id}`);
    ElMessage.success('Đã xóa phim');
    fetchMovies();
  });
}

function handleActorSelect(newIds) {
  const existingIds = form.actors.map(a => a.id);
  const newActorId = newIds.find(id => !existingIds.includes(id));
  if (newActorId) {
    selectedActor.value = actors.value.find(a => a.id === newActorId);
    characterName.value = '';
    characterDialogVisible.value = true;
  }
  form.actors = form.actors.filter(a => newIds.includes(a.id));
}

function getActorName(id) {
  const found = actors.value.find(a => a.id === id);
  return found ? found.name : '';
}

function confirmCharacterName() {
  if (selectedActor.value && characterName.value) {
    const actorIndex = form.actors.findIndex(a => a.id === selectedActor.value.id);
    if (actorIndex !== -1) {
      form.actors[actorIndex] = {
        id: selectedActor.value.id,
        character_name: characterName.value
      };
    }
  }
  characterDialogVisible.value = false;
}

function onPageChange(newPage) {
  page.value = newPage;
  fetchMovies();
}

function onSearch() {
  page.value = 1;
  fetchMovies();
}

function openImportModal() {
  importModalVisible.value = true;
  importPage.value = 1;
  ophimMovies.value = [];
}

function formatDate(dateString) {
  const date = new Date(dateString);
  return date.toLocaleDateString('vi-VN', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
    hour: '2-digit',
    minute: '2-digit'
  });
}

async function fetchOPhimMovies() {
  importing.value = true;
  try {
    const response = await axios.get(`https://ophim1.com/danh-sach/phim-moi-cap-nhat?page=${importPage.value}`);
    if (response.data.status) {
      ophimMovies.value = response.data.items;
    } else {
      ElMessage.error('Không thể lấy danh sách phim từ OPhim');
    }
  } catch (error) {
    ElMessage.error('Không thể lấy danh sách phim từ OPhim');
    console.error(error);
  } finally {
    importing.value = false;
  }
}

async function showMovieDetail(movie) {
  try {
    const response = await axios.get(`https://ophim1.com/phim/${movie.slug}`);
    if (response.data.status) {
      selectedMovieDetail.value = response.data.movie;
      detailModalVisible.value = true;
    } else {
      ElMessage.error('Không thể lấy thông tin chi tiết phim');
    }
  } catch (error) {
    ElMessage.error('Không thể lấy thông tin chi tiết phim');
    console.error(error);
  }
}

async function importMovie(movie) {
  try {
    // Lấy chi tiết phim từ OPhim
    const detailResponse = await axios.get(`https://ophim1.com/phim/${movie.slug}`);
    if (!detailResponse.data.status) {
      throw new Error('Không thể lấy thông tin chi tiết phim');
    }
    const movieDetail = detailResponse.data.movie;
    const episodes = detailResponse.data.episodes || [];
    const formData = mapOphimMovieToBackendFormat(movieDetail, genres.value, countries.value, episodes);

    // Gửi request tạo phim qua endpoint mới
    await axios.post('/api/admin/movies/import-ophim', formData);
    ElMessage.success('Import phim thành công');
    fetchMovies();
  } catch (error) {
    // Log chi tiết lỗi từ Axios
    if (error.response) {
      // Lỗi trả về từ backend
      console.error('Backend error:', error.response.data);
      ElMessage.error(error.response.data?.message || 'Không thể import phim');
    } else if (error.request) {
      // Không nhận được response từ backend
      console.error('No response from backend:', error.request);
      ElMessage.error('Không nhận được phản hồi từ server');
    } else {
      // Lỗi khác
      console.error('Error:', error.message);
      ElMessage.error(error.message);
    }
  }
}

function handleSelectionChange(selection) {
  selectedMovies.value = selection;
}

function mapOphimMovieToBackendFormat(ophimMovie, genresList, countriesList, episodes = []) {
  let video_url = '';
  // Nếu là phim lẻ (single), lấy link embed đầu tiên
  if (ophimMovie.tmdb?.type !== 'tv' && episodes.length > 0 && episodes[0].server_data.length > 0) {
    video_url = episodes[0].server_data[0].link_embed;
  }
  // Nếu là phim bộ, video_url để trống
  return {
    title: ophimMovie.name,
    slug: ophimMovie.slug,
    sub_title: ophimMovie.origin_name,
    description: ophimMovie.content.replace(/<[^>]*>/g, ''),
    poster: ophimMovie.poster_url,
    banner: ophimMovie.thumb_url,
    trailer_url: ophimMovie.trailer_url || '',
    duration: (() => {
      if (typeof ophimMovie.time === 'string') {
        const match = ophimMovie.time.match(/\d+/);
        return match ? match[0] : '';
      }
      return ophimMovie.time || '';
    })(),
    release_year: parseInt(ophimMovie.year) || 0,
    quality: ophimMovie.quality,
    is_series: ophimMovie.tmdb?.type === 'tv',
    total_episodes: parseInt(ophimMovie.episode_total) || null,
    current_episode: ophimMovie.episode_current && ophimMovie.episode_current.includes('Hoàn Tất')
      ? parseInt(ophimMovie.episode_total) || null
      : parseInt((ophimMovie.episode_current || '').split('/')[0]) || null,
    status: true,
    genres: ophimMovie.category || [],
    country: ophimMovie.country || [],
    director: ophimMovie.director || [],
    actors: (ophimMovie.actor || []).map(name => ({ name, character_name: '' })),
    imdb_rating: ophimMovie.imdb?.vote_average || null,
    tmdb_rating: ophimMovie.tmdb?.vote_average || null,
    video_url,
    episodes: ophimMovie.tmdb?.type === 'tv' ? episodes : [],
  };
}

async function importSelectedMovies() {
  if (!selectedMovies.value.length) return;

  importingSelected.value = true;
  try {
    // Lấy danh sách thể loại và quốc gia từ DB nếu chưa có
    const genresList = genres.value;
    const countriesList = countries.value;

    // Lấy chi tiết từng phim từ OPhim và mapping
    const moviesData = [];
    for (const movie of selectedMovies.value) {
      const detailResponse = await axios.get(`https://ophim1.com/phim/${movie.slug}`);
      if (detailResponse.data.status) {
        const movieDetail = detailResponse.data.movie;
        const episodes = detailResponse.data.episodes || [];
        moviesData.push(mapOphimMovieToBackendFormat(movieDetail, genresList, countriesList, episodes));
      }
    }

    // Gửi bulk import
    await axios.post('/api/admin/movies/import-ophim-bulk', { movies: moviesData });
    ElMessage.success('Import nhiều phim thành công');
    fetchMovies();
  } catch (error) {
    if (error.response) {
      console.error('Bulk import backend error:', error.response.data);
      ElMessage.error(error.response.data?.message || 'Không thể import nhiều phim');
    } else if (error.request) {
      console.error('No response from backend:', error.request);
      ElMessage.error('Không nhận được phản hồi từ server');
    } else {
      console.error('Error:', error.message);
      ElMessage.error(error.message);
    }
  } finally {
    importingSelected.value = false;
  }
}

function getEmbedUrl(url) {
  if (!url) return '';
  if (url.includes('youtube.com/embed')) return url;
  
  const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/;
  const match = url.match(regExp);
  
  return match && match[2].length === 11
    ? `https://www.youtube.com/embed/${match[2]}`
    : url;
}

function getMovieThumb(url) {
  if (!url) return '/default-poster.png';
  if (url.startsWith('http')) return url;
  return `https://img.ophim.live/uploads/movies/${url}`;
}

function addEpisode() {
  form.episodes.push({
    name: '',
    slug: '',
    link_embed: '',
    link_m3u8: ''
  });
}

function removeEpisode(index) {
  form.episodes.splice(index, 1);
}

onMounted(() => {
  fetchMovies();
  fetchCountries();
  fetchGenres();
  fetchDirectors();
  fetchActors();
});
</script>

<style scoped>
:deep(.el-table) {
  background-color: #23262f !important;
}
:deep(.el-table__header) {
  background-color: #23262f !important;
}
:deep(.el-table__header th) {
  background-color: #23262f !important;
  color: #9ca3af !important;
}
:deep(.el-table__row) {
  background-color: #23262f !important;
}
:deep(.el-table__cell) {
  background-color: #23262f !important;
  color: white !important;
}
</style> 