<template>
  <section class="py-5" style="min-height: 100vh;">
    <h2 class="mt-4 text-center">Danh sách sản phẩm</h2>
    <div class="d-flex justify-content-between mb-3 w-50 mx-auto">
      <input type="text" class="form-control me-2" v-model="search" @input="fetchProducts" placeholder="Tìm kiếm">
      <select v-model="selectedCategory" @change="fetchProducts" class="form-select me-2">
        <option value="">Tất cả danh mục</option>
        <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
      </select>
      <select v-model="perPage" @change="fetchProducts" class="form-select">
        <option :value="10">10</option>
        <option :value="20">20</option>
        <option :value="50">50</option>
      </select>
    </div>
    <div class="container px-4 px-lg-5 mt-5">
      <div class="row gx-4 gx-lg-5 row-cols-2 row-cols-md-3 row-cols-xl-4 justify-content-center">
        <!-- Kiểm tra nếu không có sản phẩm nào, hiển thị thông báo -->
        <div v-if="products.data && products.data.length === 0" class="col-12 text-center">
          <p>Không có sản phẩm nào phù hợp với tìm kiếm của bạn.</p>
        </div>
        <!-- Hiển thị sản phẩm nếu có -->
        <div class="col mb-5" v-for="product in products.data" :key="product.id">
          <div class="card h-100">
            <img class="card-img-top"
              :src="`${API_URL}/storage/` + product.images.find((image) => image.is_main === 1)?.path" alt="..."
              style="width: 100%; height: 200px; object-fit: cover" />
            <div class="card-body p-4">
              <div class="text-center">
                <span class="badge text-light-emphasis bg-light-subtle border border-light-subtle rounded-pill">{{
                  product.category.name }}</span>
                <h5 class="fw-bolder">{{ product.name }}</h5>
                {{ formatVND(product.price) }}
              </div>
            </div>
            <div class="card-footer p-4 pt-0 border-top-0 bg-transparent">
              <div class="text-center"><router-link class="btn btn-outline-danger mt-auto"
                  :to="`/detail/${product.id}`">Xem chi tiết <font-awesome-icon
                    :icon="['fas', 'circle-info']" /></router-link></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="d-flex justify-content-center mt-3">
      <button class="btn btn-primary me-2" @click="changePage('prev')" :disabled="page === 1">Trước</button>
      <span>Trang {{ page }} / {{ totalPages }}</span>
      <button class="btn btn-primary ms-2" @click="changePage('next')" :disabled="page === totalPages">Sau</button>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';

const API_URL = import.meta.env.VITE_API_URL;

const products = ref({});
const categories = ref([]);
const search = ref('');
const selectedCategory = ref('');
const perPage = ref(10);
const page = ref(1);
const totalPages = ref(1);

const fetchProducts = async () => {
  try {
    const response = await axios.get(`${API_URL}/api/products`, {
      params: {
        search: search.value,
        category_id: selectedCategory.value,
        perPage: perPage.value,
        page: page.value,
      },
    });
    products.value = response.data;
    totalPages.value = response.data.last_page;
  } catch (error) {
    console.error(error);
  }
};

const fetchCategories = async () => {
  try {
    const response = await axios.get(`${API_URL}/api/categories`);
    categories.value = response.data;
  } catch (error) {
    console.error(error);
  }
};

const changePage = (direction) => {
  if (direction === 'prev' && page.value > 1) {
    page.value--;
  } else if (direction === 'next' && page.value < totalPages.value) {
    page.value++;
  }
  fetchProducts();
};

const formatVND = (price) => {
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND',
  }).format(price);
};

onMounted(() => {
  fetchProducts();
  fetchCategories();
});

watch([search, selectedCategory, perPage], () => {
  page.value = 1; // Reset to first page on filter change
  fetchProducts();
});
</script>

<style scoped>
/* Add custom styles here */
</style>
