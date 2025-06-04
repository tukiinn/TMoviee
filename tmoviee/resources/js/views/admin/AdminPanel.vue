<template>
  <AdminLayout>
    <div class="p-6 h-full flex flex-col">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-2xl font-bold text-white">Dashboard</h1>
        <p class="text-gray-400">Chào mừng đến với trang quản trị</p>
      </div>

      <!-- Stats Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Movies -->
        <div class="bg-[#23262f] rounded-lg p-6 shadow-lg">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-400 text-sm">Tổng số phim</p>
              <h3 class="text-2xl font-bold text-white mt-1">{{ stats.totalMovies }}</h3>
            </div>
            <div class="bg-blue-500/10 rounded-full w-12 h-12 flex items-center justify-center">
              <i class="fas fa-film text-blue-500 text-xl"></i>
            </div>
          </div>
          <div class="mt-4">
            <span class="text-green-500 text-sm">
              <i class="fas fa-arrow-up"></i> {{ stats.newMovies }} phim mới
            </span>
          </div>
        </div>

        <!-- Total Actors -->
        <div class="bg-[#23262f] rounded-lg p-6 shadow-lg">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-400 text-sm">Tổng số diễn viên</p>
              <h3 class="text-2xl font-bold text-white mt-1">{{ stats.totalActors }}</h3>
            </div>
            <div class="bg-purple-500/10 rounded-full w-12 h-12 flex items-center justify-center">
              <i class="fas fa-users text-purple-500 text-xl"></i>
            </div>
          </div>
          <div class="mt-4">
            <span class="text-green-500 text-sm">
              <i class="fas fa-arrow-up"></i> {{ stats.newActors }} diễn viên mới
            </span>
          </div>
        </div>

        <!-- Total Directors -->
        <div class="bg-[#23262f] rounded-lg p-6 shadow-lg">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-400 text-sm">Tổng số đạo diễn</p>
              <h3 class="text-2xl font-bold text-white mt-1">{{ stats.totalDirectors }}</h3>
            </div>
            <div class="bg-yellow-500/10 rounded-full w-12 h-12 flex items-center justify-center">
              <i class="fas fa-user-tie text-yellow-500 text-xl"></i>
            </div>
          </div>
          <div class="mt-4">
            <span class="text-green-500 text-sm">
              <i class="fas fa-arrow-up"></i> {{ stats.newDirectors }} đạo diễn mới
            </span>
          </div>
        </div>

        <!-- Total Views -->
        <div class="bg-[#23262f] rounded-lg p-6 shadow-lg">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-400 text-sm">Lượt xem hôm nay</p>
              <h3 class="text-2xl font-bold text-white mt-1">{{ stats.todayViews }}</h3>
            </div>
            <div class="bg-green-500/10 rounded-full w-12 h-12 flex items-center justify-center">
              <i class="fas fa-eye text-green-500 text-xl"></i>
            </div>
          </div>
          <div class="mt-4">
            <span class="text-green-500 text-sm">
              <i class="fas fa-arrow-up"></i> {{ stats.viewsIncrease }}% so với hôm qua
            </span>
          </div>
        </div>
      </div>

      <!-- Recent Activity & Quick Actions -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Activity -->
        <div class="bg-[#23262f] rounded-lg p-6 shadow-lg">
          <h2 class="text-xl font-bold text-white mb-4">Hoạt động gần đây</h2>
          <div class="space-y-4 max-h-[300px] overflow-y-auto pr-2 custom-scrollbar">
            <div v-for="activity in recentActivities.slice(0, 5)" :key="activity.id" class="flex items-start space-x-4">
              <div :class="getActivityIconClass(activity.type)" class="p-2 rounded-full">
                <i :class="getActivityIcon(activity.type)" class="text-lg"></i>
              </div>
              <div>
                <p class="text-white">{{ activity.description }}</p>
                <p class="text-gray-400 text-sm">{{ formatDate(activity.created_at) }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-[#23262f] rounded-lg p-6 shadow-lg">
          <h2 class="text-xl font-bold text-white mb-4">Thao tác nhanh</h2>
          <div class="grid grid-cols-2 gap-4">
            <button @click="navigateTo('/admin/movies')" class="bg-blue-500 hover:bg-blue-600 text-white p-4 rounded-lg flex items-center justify-center space-x-2">
              <i class="fas fa-film"></i>
              <span>Quản lý phim</span>
            </button>
            <button @click="navigateTo('/admin/actors')" class="bg-purple-500 hover:bg-purple-600 text-white p-4 rounded-lg flex items-center justify-center space-x-2">
              <i class="fas fa-users"></i>
              <span>Quản lý diễn viên</span>
            </button>
            <button @click="navigateTo('/admin/directors')" class="bg-yellow-500 hover:bg-yellow-600 text-white p-4 rounded-lg flex items-center justify-center space-x-2">
              <i class="fas fa-user-tie"></i>
              <span>Quản lý đạo diễn</span>
            </button>
            <button @click="openImportModal" class="bg-green-500 hover:bg-green-600 text-white p-4 rounded-lg flex items-center justify-center space-x-2">
              <i class="fas fa-download"></i>
              <span>Import từ OPhim</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Thêm bảng giao dịch dưới các phần hiện tại -->
      <div class="bg-[#23262f] rounded-lg p-6 shadow-lg mt-8">
        <h2 class="text-xl font-bold text-white mb-4">Giao dịch hội viên gần đây</h2>
        <div class="overflow-x-auto">
          <table class="w-full text-left rounded-lg overflow-hidden">
            <thead>
              <tr class="text-gray-400 bg-[#23262f] border-b border-[#374151]">
                <th class="py-3 px-4 font-semibold">Người dùng</th>
                <th class="py-3 px-4 font-semibold">Gói</th>
                <th class="py-3 px-4 font-semibold text-right">Số tiền</th>
                <th class="py-3 px-4 font-semibold text-center">Trạng thái</th>
                <th class="py-3 px-4 font-semibold">Ngày tạo</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="t in recentTransactions" :key="t.id"
                  class="text-white hover:bg-[#23243a] transition-colors border-b border-[#23262f] last:border-0">
                <td class="py-3 px-4 flex items-center gap-2">
                  <span v-if="t.avatar" class="inline-block w-8 h-8 rounded-full overflow-hidden bg-gray-700">
                    <img :src="t.avatar" alt="avatar" class="w-full h-full object-cover aspect-square" />
                  </span>
                  <span v-else class="inline-block w-8 h-8 rounded-full bg-gray-700 flex items-center justify-center text-lg font-bold">
                    <i class="fas fa-user"></i>
                  </span>
                  <span>{{ t.user }}</span>
                </td>
                <td class="py-3 px-4">{{ t.package }}</td>
                <td class="py-3 px-4 text-right font-semibold text-green-400">
                  {{ Number(t.amount).toLocaleString('vi-VN') }} đ
                </td>
                <td class="py-3 px-4 text-center">
                  <span
                    :class="[
                      'px-3 py-1 rounded-full text-xs font-bold',
                      t.status === 'success' || t.status === 'completed' ? 'bg-green-500/20 text-green-400' :
                      t.status === 'pending' ? 'bg-yellow-500/20 text-yellow-400' :
                      t.status === 'failed' ? 'bg-red-500/20 text-red-400' : 'bg-gray-500/20 text-gray-400'
                    ]"
                  >
                    {{
                      t.status === 'success' ? 'Thành công'
                      : t.status === 'completed' ? 'Hoàn thành'
                      : t.status === 'pending' ? 'Chờ xử lý'
                      : t.status === 'failed' ? 'Thất bại'
                      : t.status
                    }}
                  </span>
                </td>
                <td class="py-3 px-4">{{ t.created_at }}</td>
              </tr>
              <tr v-if="recentTransactions.length === 0">
                <td colspan="5" class="py-6 text-center text-gray-400">Không có giao dịch nào gần đây.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Nhóm chọn thời gian và biểu đồ line doanh thu (Chart.js) -->
      <div class="bg-[#23262f] rounded-lg p-6 shadow-lg mt-8">
        <h2 class="text-xl font-bold text-white mb-4">Biểu đồ doanh thu</h2>
        <div class="flex flex-wrap gap-6 mb-4">
          <div class="flex gap-2 items-center flex-wrap">
            <select v-model="timeType" class="rounded px-2 py-1 bg-[#181c2f] text-white border border-[#374151]">
              <option value="day">Theo ngày</option>
              <option value="month">Theo tháng</option>
              <option value="year">Theo năm</option>
              <option value="range">Khoảng thời gian</option>
            </select>
            <input v-if="timeType==='day'" type="date" v-model="timeValue" class="rounded px-2 py-1 bg-[#181c2f] text-white border border-[#374151]" />
            <input v-if="timeType==='month'" type="month" v-model="timeValue" class="rounded px-2 py-1 bg-[#181c2f] text-white border border-[#374151]" />
            <input v-if="timeType==='year'" type="number" min="2000" max="2100" v-model="timeValue" class="rounded px-2 py-1 bg-[#181c2f] text-white border border-[#374151]" />
            <input v-if="timeType==='range'" type="date" v-model="timeFrom" class="rounded px-2 py-1 bg-[#181c2f] text-white border border-[#374151]" />
            <input v-if="timeType==='range'" type="date" v-model="timeTo" class="rounded px-2 py-1 bg-[#181c2f] text-white border border-[#374151]" />
            <button @click="fetchAllRevenue" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-1 rounded">Xem</button>
          </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <div class="chart-container">
            <Line :data="revenueLineData" :options="revenueLineOptions" />
          </div>
          <div class="chart-container doughnut-container">
            <Doughnut :data="revenueDoughnutData" :options="revenueDoughnutOptions" />
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/components/admin/AdminLayout.vue'
import axios from 'axios'
import { Chart, registerables } from 'chart.js'
import { nextTick, onMounted, ref } from 'vue'
import { Doughnut, Line } from 'vue-chartjs'
import { useRouter } from 'vue-router'
Chart.register(...registerables)

const router = useRouter();

const stats = ref({
  totalMovies: 0,
  newMovies: 0,
  totalActors: 0,
  newActors: 0,
  totalDirectors: 0,
  newDirectors: 0,
  todayViews: 0,
  viewsIncrease: 0
});

const recentActivities = ref([]);
const recentTransactions = ref([]);
const timeType = ref('month')
const timeValue = ref(new Date().toISOString().slice(0,7))
const timeFrom = ref('')
const timeTo = ref('')

const revenueLineData = ref({
  labels: [],
  datasets: []
})

const revenueLineOptions = ref({
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      display: true,
      position: 'top',
      labels: {
        color: '#bdbdbd',
        font: { family: 'Roboto', size: 16, weight: 'bold' }
      }
    },
    tooltip: {
      backgroundColor: '#23262f',
      titleColor: '#f472b6',
      bodyColor: '#fff',
      borderColor: '#f472b6',
      borderWidth: 1,
      padding: 10,
      displayColors: false,
      titleFont: { family: 'Roboto', size: 15, weight: 'bold' },
      bodyFont: { family: 'Roboto', size: 14 }
    }
  },
  scales: {
    x: {
      grid: { color: 'rgba(255,255,255,0.08)' },
      ticks: { color: '#bdbdbd', font: { family: 'Roboto', size: 13 } }
    },
    y: {
      grid: { color: 'rgba(255,255,255,0.08)' },
      ticks: { color: '#bdbdbd', font: { family: 'Roboto', size: 13 } }
    }
  }
})

const revenueDoughnutData = ref({
  labels: [],
  datasets: []
})

const revenueDoughnutOptions = ref({
  responsive: true,
  plugins: {
    legend: {
      position: 'bottom',
      labels: {
        color: '#fff',
        font: {
          family: 'Roboto'
        }
      }
    },
    tooltip: {
      callbacks: {
        label: function(context) {
          let label = context.label || '';
          if (label) label += ': ';
          if (context.parsed !== null) {
            label += new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(context.parsed);
          }
          return label;
        }
      }
    }
  }
})

function getActivityIcon(type) {
  const icons = {
    movie: 'fas fa-film',
    actor: 'fas fa-users',
    director: 'fas fa-user-tie',
    import: 'fas fa-download'
  };
  return icons[type] || 'fas fa-info-circle';
}

function getActivityIconClass(type) {
  const classes = {
    movie: 'bg-blue-500/10 text-blue-500',
    actor: 'bg-purple-500/10 text-purple-500',
    director: 'bg-yellow-500/10 text-yellow-500',
    import: 'bg-green-500/10 text-green-500'
  };
  return classes[type] || 'bg-gray-500/10 text-gray-500';
}

function formatDate(date) {
  return new Date(date).toLocaleDateString('vi-VN', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
    hour: '2-digit',
    minute: '2-digit'
  });
}

function navigateTo(path) {
  router.push(path);
}

function openImportModal() {
  router.push('/admin/movies?import=true');
}

async function fetchStats() {
  try {
    const response = await axios.get('/api/admin/stats');
    stats.value = response.data;
  } catch (error) {
    console.error('Error fetching stats:', error);
  }
}

async function fetchRecentActivities() {
  try {
    const response = await axios.get('/api/admin/activities');
    recentActivities.value = response.data;
  } catch (error) {
    console.error('Error fetching activities:', error);
  }
}

async function fetchRecentTransactions() {
  try {
    const response = await axios.get('/api/admin/recent-transactions');
    recentTransactions.value = response.data;
  } catch (error) {
    console.error('Error fetching transactions:', error);
  }
}

async function fetchRevenueCharts() {
  let params = { type: timeType.value }
  if (timeType.value === 'day' || timeType.value === 'month' || timeType.value === 'year') {
    params.value = timeValue.value
  } else if (timeType.value === 'range') {
    params.from = timeFrom.value
    params.to = timeTo.value
  }
  try {
    const res = await axios.get('/api/admin/revenue-chart', { params })
    // Line chart: tổng doanh thu theo thời gian
    revenueLineData.value = {
      labels: res.data.line.labels,
      datasets: [
        {
          label: 'Tổng doanh thu',
          data: res.data.line.data,
          borderColor: '#f472b6',
          backgroundColor: 'transparent',
          tension: 0.4,
          fill: false,
          pointRadius: 0,
          borderWidth: 2
        }
      ]
    }
    // Doughnut chart: doanh thu theo phương thức thanh toán
    revenueDoughnutData.value = {
      labels: res.data.doughnut.labels,
      datasets: [
        {
          label: 'Doanh thu theo phương thức',
          data: res.data.doughnut.data,
          backgroundColor: [
            '#10b981', '#f59e42', '#3b82f6', '#f43f5e', '#a78bfa', '#fbbf24', '#6366f1', '#ef4444'
          ],
          borderColor: '#23262f',
          borderWidth: 2
        }
      ]
    }
  } catch (e) {
    console.error('Error fetching revenue data:', e)
    revenueLineData.value = { labels: [], datasets: [] }
    revenueDoughnutData.value = { labels: [], datasets: [] }
  }
}

function getLineGradient() {
  const chart = document.querySelector('.chart-container canvas');
  if (!chart) return '#10b98122';
  const ctx = chart.getContext('2d');
  const gradient = ctx.createLinearGradient(0, 0, 0, 320);
  gradient.addColorStop(0, 'rgba(16,185,129,0.35)');
  gradient.addColorStop(1, 'rgba(16,185,129,0.05)');
  return gradient;
}

async function fetchAllRevenue() {
  await fetchRevenueCharts()
}

onMounted(async () => {
  await nextTick();
  fetchStats();
  fetchRecentActivities();
  fetchRecentTransactions();
  fetchAllRevenue();
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
.aspect-square {
  aspect-ratio: 1 / 1;
}
.chart-container {
  width: 100%;
  max-width: 100%;
  height: 320px;
  min-height: 320px;
  overflow-x: auto;
}
.doughnut-container {
  max-width: 320px;
  max-height: 320px;
  margin: 0 auto;
  display: flex;
  align-items: center;
  justify-content: center;
}
.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: #1a1c23;
  border-radius: 3px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #4a4d57;
  border-radius: 3px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #5a5d67;
}
</style> 