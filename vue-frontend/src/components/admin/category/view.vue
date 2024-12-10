<template>
  <div id="layoutSidenav_content">
    <main>
      <div class="container-fluid" style="margin-top: 80px;">
        <h2 class="mt-4">Quản lý danh mục</h2>
        <div class="add-button d-flex justify-content-end mb-3">
          <router-link to="/admin/category/add" class="btn btn-primary">
            Thêm danh mục <font-awesome-icon :icon="['far', 'square-plus']" />
          </router-link>
        </div>
        <table class="table table-bordered">
          <thead class="table-dark text-center">
            <tr>
              <th>#</th>
              <th>Tên danh mục</th>
              <th>Mô tả</th>
              <th>Hành động</th>
            </tr>
          </thead>
          <tbody class="text-center mx-auto">
            <tr v-for="(category, index) in pagedCategories" :key="category.id">
              <td>{{ index + 1 }}</td>
              <td>{{ category.name }}</td>
              <td>{{ category.description }}</td>
              <td>
                <button class="btn btn-primary me-2" @click="openEditModal(category)">
                  <font-awesome-icon :icon="['far', 'pen-to-square']" />
                </button>
                <button class="btn btn-danger" @click="deleteCategory(category.id)">
                  <font-awesome-icon :icon="['far', 'trash-can']" />
                </button>
              </td>
            </tr>
            <tr v-if="categories.length === 0">
              <td colspan="4" class="text-center">Không có danh mục</td>
            </tr>
          </tbody>
        </table>

        <!-- phân trang -->
        <div class="d-flex justify-content-center mt-3">
          <nav aria-label="Page navigation example">
            <ul class="pagination">
              <li class="page-item" :class="{ disabled: currentPage === 1 }">
                <a class="page-link" href="#" @click.prevent="goToPage(currentPage - 1)">Previous</a>
              </li>
              <li class="page-item" v-for="page in totalPages" :key="page" :class="{ active: page === currentPage }">
                <a class="page-link" href="#" @click.prevent="goToPage(page)">{{
                  page
                }}</a>
              </li>
              <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                <a class="page-link" href="#" @click.prevent="goToPage(currentPage + 1)">Next</a>
              </li>
            </ul>
          </nav>
        </div>

        <!-- Modal Update Danh Mục -->
        <div v-if="showModal" class="modal fade show d-block" tabindex="-1" aria-labelledby="exampleModalLabel"
          aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Cập nhật danh mục</h5>
                <button type="button" class="btn-close" @click="closeModal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <form @submit.prevent="updateCategory">
                  <div class="mb-3">
                    <label for="categoryName" class="form-label">Tên danh mục</label>
                    <input type="text" class="form-control" id="categoryName" v-model="selectedCategory.name"
                      required />
                  </div>
                  <div class="mb-3">
                    <label for="categoryDescription" class="form-label">Mô tả</label>
                    <textarea id="categoryDescription" class="form-control" v-model="selectedCategory.description"
                      required></textarea>
                  </div>
                  <button type="submit" class="btn btn-primary">Cập nhật</button>
                </form>
              </div>
            </div>
          </div>
        </div>

      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';

const API_URL = import.meta.env.VITE_API_URL;

const categories = ref([]);
const showModal = ref(false);
const selectedCategory = ref({});

const itemsPerPage = 10;
const currentPage = ref(1);

const totalPages = computed(() => {
  return Math.ceil(categories.value.length / itemsPerPage);
});

const pagedCategories = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage;
  const end = start + itemsPerPage;
  return categories.value.slice(start, end);
});

const goToPage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page;
  }
};

const fetchCategories = async () => {
  try {
    const response = await axios.get(`${API_URL}/api/categories`);
    if (response.status === 200) {
      categories.value = response.data;
    }
  } catch (error) {
    console.error('Error fetching categories:', error);
  }
};

const deleteCategory = async (categoryId) => {
  try {
    const result = await Swal.fire({
      title: 'Bạn có chắc chắn?',
      text: 'Hành động này không thể hoàn tác!',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Xóa',
      cancelButtonText: 'Hủy',
    });

    if (result.isConfirmed) {
      await axios.delete(`${API_URL}/api/categories/${categoryId}`);
      fetchCategories();
      Swal.fire('Thành công', 'Danh mục đã được xóa', 'success');
    }
  } catch (error) {
    console.error('Error deleting category:', error);
  }
};

const openEditModal = async (category) => {
  const result = await Swal.fire({
    title: 'Bạn có chắc chắn?',
    text: 'Bạn muốn sửa thông tin danh mục này?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Sửa',
    cancelButtonText: 'Hủy',
  });

  if (result.isConfirmed) {
    selectedCategory.value = { ...category };
    showModal.value = true;
  }
};

const closeModal = () => {
  showModal.value = false;
};

const updateCategory = async () => {
  try {
    const response = await axios.put(`${API_URL}/api/categories/${selectedCategory.value.id}`, {
      name: selectedCategory.value.name,
      description: selectedCategory.value.description,
    });

    if (response.status === 200) {
      fetchCategories();
      closeModal();
      Swal.fire('Thành công', 'Danh mục đã được cập nhật', 'success');
    }
  } catch (error) {
    console.error('Error updating category:', error);
    Swal.fire('Thất bại', 'Có lỗi xảy ra khi cập nhật danh mục', 'error');
  }
};

onMounted(() => {
  fetchCategories();
});
</script>
