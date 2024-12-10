<template>
  <div id="layoutSidenav_content">
    <main>
      <div class="container-fluid" style="margin-top: 80px;">
        <h2 class="mt-4">Quản lý sản phẩm</h2>
        <div class="add-button d-flex justify-content-end mb-3">
          <router-link to="/admin/products/add" class="btn btn-primary">
            Thêm sản phẩm <font-awesome-icon :icon="['far', 'square-plus']" />
          </router-link>
        </div>
        <table class="table table-bordered">
          <thead class="table-dark text-center">
            <tr>
              <th>#</th>
              <th>Tên sản phẩm</th>
              <th>Giá</th>
              <th>Số lượng</th>
              <th>Hình ảnh chính</th>
              <th>Hình ảnh phụ</th>
              <th>Danh mục</th>
              <th>Hành động</th>
            </tr>
          </thead>
          <tbody class="text-center mx-auto">
            <tr v-for="(product, index) in pagedProducts" :key="product.id">
              <td>{{ index + 1 }}</td>
              <td>{{ product.name }}</td>
              <td>{{ formatVND(product.price) }}</td>
              <td>{{ product.quantity }}</td>
              <td>
                <img :src="`${API_URL}/storage/` + product.images.find((image) => image.is_main === 1)?.path"
                  alt="Main Image" style="max-width: 100px; max-height: 100px;" />
              </td>
              <td>
                <img v-for="(image, index) in product.images" :key="index" :src="`${API_URL}/storage/` + image.path"
                  alt="Image" style="max-width: 100px; max-height: 100px;" class="me-2" />
              </td>
              <td>{{ product.category.name }}</td>
              <td>
                <button class="btn btn-primary me-2" @click="openEditModal(product)">
                  <font-awesome-icon :icon="['far', 'pen-to-square']" />
                </button>
                <button class="btn btn-danger" @click="deleteProduct(product.id)">
                  <font-awesome-icon :icon="['far', 'trash-can']" />
                </button>
              </td>
            </tr>
            <tr v-if="products.length === 0">
              <td colspan="9" class="text-center">Không có sản phẩm</td>
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

        <!-- Modal Update Sản Phẩm -->
        <div v-if="showModal" class="modal fade show d-block" tabindex="-1" aria-labelledby="exampleModalLabel"
          aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Cập nhật sản phẩm</h5>
                <button type="button" class="btn-close" @click="closeModal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <form @submit.prevent="updateProduct">
                  <div class="mb-3">
                    <label for="productName" class="form-label">Tên sản phẩm</label>
                    <input type="text" class="form-control" id="productName" v-model="selectedProduct.name" required />
                  </div>
                  <div class="mb-3">
                    <label for="productPrice" class="form-label">Giá</label>
                    <input type="number" class="form-control" id="productPrice" v-model="selectedProduct.price"
                      required />
                  </div>
                  <div class="mb-3">
                    <label for="productQuantity" class="form-label">Số lượng</label>
                    <input type="number" class="form-control" id="productQuantity" v-model="selectedProduct.quantity"
                      required />
                  </div>
                  <div class="mb-3">
                    <label for="productCategory" class="form-label">Danh mục</label>
                    <select id="productCategory" class="form-control" v-model="selectedProduct.category_id" required>
                      <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}
                      </option>
                    </select>
                  </div>

                  <!-- Chọn hình ảnh chính -->
                  <div class="mb-3">
                    <label for="mainImage" class="form-label">Chọn hình ảnh chính</label>
                    <div v-for="(image, index) in selectedProduct.images" :key="index"
                      class="d-inline-block position-relative">
                      <img :src="`${API_URL}/storage/` + image.path" alt="Image"
                        style="max-width: 100px; max-height: 100px;" @click="selectMainImage(index)"
                        :class="{ 'border-primary': selectedProduct.main_image_index === index }" />
                      <input type="radio" :id="'mainImage' + index" :name="'mainImage'" :value="index"
                        v-model="selectedProduct.main_image_index">
                    </div>
                  </div>

                  <!-- Hiển thị hình ảnh phụ -->
                  <div class="mb-3">
                    <label for="productImages" class="form-label">Hình ảnh phụ</label>
                    <input type="file" class="form-control" id="productImages" multiple @change="handleImageChange" />
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

const products = ref([]);
const categories = ref([]);
const showModal = ref(false);
const selectedProduct = ref({});
const imageFiles = ref([]);

const itemsPerPage = 10;
const currentPage = ref(1);

const totalPages = computed(() => {
  return Math.ceil(products.value.length / itemsPerPage);
});

const pagedProducts = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage;
  const end = start + itemsPerPage;
  return products.value.slice(start, end);
});

const goToPage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page;
  }
};

const fetchProducts = async () => {
  try {
    const response = await axios.get(`${API_URL}/api/products`);
    if (response.status === 200) {
      products.value = response.data.data;
    }
  } catch (error) {
    console.error('Error fetching products:', error);
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

const deleteProduct = async (productId) => {
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
      await axios.delete(`${API_URL}/api/products/${productId}`);
      fetchProducts();
      Swal.fire('Thành công', 'Sản phẩm đã được xóa', 'success');
    }
  } catch (error) {
    console.error('Error deleting product:', error);
  }
};

const openEditModal = async (product) => {
  const result = await Swal.fire({
    title: 'Bạn có chắc chắn?',
    text: 'Bạn muốn sửa thông tin sản phẩm này?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Sửa',
    cancelButtonText: 'Hủy',
  });

  if (result.isConfirmed) {
    selectedProduct.value = { ...product };
    selectedProduct.value.main_image_index = product.images.findIndex(image => image.is_main === 1);
    showModal.value = true;
  }
};


const closeModal = () => {
  showModal.value = false;
};

const updateProduct = async () => {
  const formData = new FormData();

  formData.append('name', selectedProduct.value.name);
  formData.append('price', selectedProduct.value.price);
  formData.append('quantity', selectedProduct.value.quantity);
  formData.append('category_id', selectedProduct.value.category_id);

  selectedProduct.value.images.forEach((image, index) => {
    formData.append(`existing_images[${index}][path]`, image.path);
    formData.append(`existing_images[${index}][is_main]`,
      index === selectedProduct.value.main_image_index ? 1 : 0
    );
  });

  imageFiles.value.forEach((file, index) => {
    formData.append(`new_images[${index}]`, file);
  });

  try {
    const response = await axios.post(`${API_URL}/api/products/${selectedProduct.value.id}`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      },
      params: {
        _method: 'PUT'
      }
    });

    if (response.status === 200) {
      fetchProducts();
      closeModal();
      Swal.fire('Thành công', 'Sản phẩm đã được cập nhật', 'success');
    }
  } catch (error) {
    console.error('Error updating product:', error);
    Swal.fire('Thất bại', 'Có lỗi xảy ra khi cập nhật sản phẩm', 'error');
  }
};

const handleImageChange = (event) => {
  imageFiles.value = Array.from(event.target.files);
};

const removeImage = (index) => {
  selectedProduct.value.images.splice(index, 1);
};

const selectMainImage = (index) => {
  selectedProduct.value.main_image_index = index;
};

const formatVND = (price) => {
  return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);
};

onMounted(() => {
  fetchProducts();
  fetchCategories();
});
</script>
