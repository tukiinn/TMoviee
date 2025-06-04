<template>
  <AdminLayout>
    <div class="flex-1 p-4 min-h-screen">
      <div class="p-6 pt-header min-h-screen">
        <div class="flex justify-between items-center mb-6">
          <el-input v-model="search" placeholder="Tìm kiếm đạo diễn..." style="max-width: 300px" @keyup.enter="onSearch" clearable @clear="onSearch" class="shadow-sm" />
          <button @click="openAddModal" class="bg-yellow-400 hover:bg-yellow-300 text-gray-900 font-bold px-5 py-2 rounded-lg shadow flex items-center">
            <i class="fas fa-plus mr-2"></i> Thêm đạo diễn mới
          </button>
        </div>
        <el-table :data="directors" style="width: 100%" v-loading="loading" class="shadow-md rounded-lg overflow-hidden bg-[#23262f] text-white">
          <el-table-column prop="name" label="Tên" min-width="180" />
          <el-table-column prop="birth_date" label="Ngày sinh" width="120">
            <template #default="{ row }">
              {{ formatDate(row.birth_date) }}
            </template>
          </el-table-column>
          <el-table-column prop="birth_place" label="Nơi sinh" min-width="160" />
          <el-table-column prop="status" label="Trạng thái" width="100">
            <template #default="{ row }">
              <el-tag :type="row.status ? 'success' : 'info'">{{ row.status ? 'Hoạt động' : 'Ẩn' }}</el-tag>
            </template>
          </el-table-column>
          <el-table-column label="Hành động" width="160">
            <template #default="{ row }">
              <button @click="openEditModal(row)" class="px-3 py-1 rounded bg-blue-500 text-white hover:bg-blue-600 mr-2">
                <i class="fas fa-edit"></i>
              </button>
              <button @click="confirmDelete(row)" class="px-3 py-1 rounded bg-red-500 text-white hover:bg-red-600">
                <i class="fas fa-trash"></i>
              </button>
            </template>
          </el-table-column>
        </el-table>
        <el-pagination
          class="mt-4 rounded-lg bg-[#23262f] px-4 py-2"
          layout="prev, pager, next"
          :total="total"
          :page-size="pageSize"
          :current-page="page"
          @update:current-page="onPageChange"
        />
        <el-dialog :title="editDirector ? 'Sửa đạo diễn' : 'Thêm đạo diễn'" v-model="modalVisible" width="500px" :close-on-click-modal="false" class="!bg-[#23262f] rounded-xl shadow-xl p-8 overflow-y-auto max-h-[90vh] hide-scrollbar text-white">
          <el-form :model="form" :rules="rules" ref="formRef" label-width="120px" label-position="left">
            <el-form-item label="Tên" prop="name">
              <el-input v-model="form.name" class="shadow-sm text-white" />
            </el-form-item>
            <el-form-item label="Slug" prop="slug">
              <el-input v-model="form.slug" class="shadow-sm text-white" />
            </el-form-item>
            <el-form-item label="Ngày sinh" prop="birth_date">
              <el-date-picker v-model="form.birth_date" type="date" placeholder="Chọn ngày sinh" style="width: 100%" />
            </el-form-item>
            <el-form-item label="Nơi sinh" prop="birth_place">
              <el-input v-model="form.birth_place" class="shadow-sm text-white" />
            </el-form-item>
            <el-form-item label="Tiểu sử" prop="biography">
              <el-input type="textarea" v-model="form.biography" class="shadow-sm text-white" />
            </el-form-item>
            <el-form-item label="Trạng thái" prop="status">
              <el-switch v-model="form.status" active-text="Hoạt động" inactive-text="Ẩn" />
            </el-form-item>
          </el-form>
          <template #footer>
            <el-button @click="modalVisible = false" class="shadow-sm">Hủy</el-button>
            <el-button type="primary" @click="submitForm" class="shadow-sm">{{ editDirector ? 'Cập nhật' : 'Tạo mới' }}</el-button>
          </template>
        </el-dialog>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/components/admin/AdminLayout.vue';
import axios from 'axios';
import { ElMessage, ElMessageBox } from 'element-plus';
import { onMounted, ref } from 'vue';

const directors = ref([]);
const total = ref(0);
const page = ref(1);
const pageSize = 10;
const loading = ref(false);
const search = ref('');
const modalVisible = ref(false);
const editDirector = ref(null);
const form = ref({});
const formRef = ref();

const rules = {
  name: [{ required: true, message: 'Vui lòng nhập tên', trigger: 'blur' }],
  slug: [{ required: true, message: 'Vui lòng nhập slug', trigger: 'blur' }],
};

function fetchDirectors() {
  loading.value = true;
  axios.get('/api/admin/directors', {
    params: { search: search.value, page: page.value }
  })
    .then(res => {
      directors.value = res.data.data;
      total.value = res.data.total;
    })
    .finally(() => loading.value = false);
}

function openAddModal() {
  editDirector.value = null;
  form.value = { name: '', slug: '', birth_date: '', birth_place: '', biography: '', status: true };
  modalVisible.value = true;
}

function openEditModal(row) {
  editDirector.value = row;
  form.value = { ...row };
  modalVisible.value = true;
}

function submitForm() {
  formRef.value.validate(async valid => {
    if (!valid) return;
    try {
      if (editDirector.value) {
        await axios.put(`/api/admin/directors/${editDirector.value.id}`, form.value);
        ElMessage.success('Cập nhật thành công');
      } else {
        await axios.post('/api/admin/directors', form.value);
        ElMessage.success('Tạo mới thành công');
      }
      modalVisible.value = false;
      fetchDirectors();
    } catch (e) {
      ElMessage.error(e.response?.data?.message || 'Có lỗi xảy ra');
    }
  });
}

function confirmDelete(row) {
  ElMessageBox.confirm('Bạn chắc chắn muốn xóa đạo diễn này?', 'Xác nhận', {
    type: 'warning',
    confirmButtonText: 'Xóa',
    cancelButtonText: 'Hủy',
  }).then(async () => {
    await axios.delete(`/api/admin/directors/${row.id}`);
    ElMessage.success('Đã xóa đạo diễn');
    fetchDirectors();
  });
}

function formatDate(dateStr) {
  if (!dateStr) return '';
  const d = new Date(dateStr);
  if (isNaN(d)) return '';
  return d.toLocaleDateString('vi-VN');
}

function onSearch() {
  page.value = 1;
  fetchDirectors();
}

function onPageChange(newPage) {
  page.value = newPage;
  fetchDirectors();
}

onMounted(fetchDirectors);
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