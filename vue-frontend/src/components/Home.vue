<template>

  <!-- </header> -->
  <header class="py-5" style="background-color: #1F4529;">
    <div class="container px-4 px-lg-5 my-5">
      <div class="text-center text-white">
        <h1 class="display-4 fw-bolder">Mua sắm theo phong cách</h1>
        <p class="lead fw-normal text-white-50 mb-0">Với trang web mua sắm theo phong cách hiện đại</p>
      </div>
    </div>
  </header>
  <!-- Section-->
  <section class="py-5">
    <div class="container px-4 px-lg-5 mt-5">
      <h3 class="mb-3">Các sản phẩm mới</h3>
      <div class="row gx-4 gx-lg-5 row-cols-2 row-cols-md-3 row-cols-xl-4 justify-content-center">
        <div class="col mb-5" v-for="product in sortedProducts.slice(0, 4)" :key="product.id">
          <div class="card h-100">
            <img class="card-img-top"
              :src="`${API_URL}/storage/` + product.images.find((image) => image.is_main === 1)?.path"
              style="width: 100%; height: 200px; object-fit: cover" alt="{{ product.name }}" />
            <div class="card-body p-4">
              <span class="badge text-light-emphasis bg-light-subtle border border-light-subtle rounded-pill">{{
                product.category.name }}</span>
              <div class="text-left mt-1 ps-1">
                <h5 class="fw-bolder">{{ product.name }}</h5>
                {{ formatVND(product.price) }}
              </div>
            </div>
            <div class="card-footer p-4 pt-0 border-top-0 bg-transparent">
              <div class="text-left"><router-link class="btn btn-outline-dark mt-auto" :to="`/detail/${product.id}`">Chi
                  tiết <font-awesome-icon :icon="['fas', 'circle-info']" /></router-link></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- product related -->
  <section class="py-5">
    <div class="container px-4 px-lg-5 mt-3" v-for="category in categories" :key="category.id">
      <h3 class="mb-3">{{ category.name }}</h3>
      <div class="row gx-4 gx-lg-5 row-cols-2 row-cols-md-3 row-cols-xl-4 justify-content-center">
        <div class="col mb-5" v-for="product in productByCategory[category.id]" :key="product.id">
          <div class="card h-100">
            <img class="card-img-top"
              :src="`${API_URL}/storage/` + product.images.find((image) => image.is_main === 1)?.path"
              style="width: 100%; height: 200px; object-fit: cover" alt="{{ product.name }}" />
            <div class="card-body p-4">
              <span class="badge text-light-emphasis bg-light-subtle border border-light-subtle rounded-pill">{{
                product.category.name }}</span>
              <div class="text-left mt-1 ps-1">
                <h5 class="fw-bolder">{{ product.name }}</h5>
                {{ formatVND(product.price) }}
              </div>
            </div>
            <div class="card-footer p-4 pt-0 border-top-0 bg-transparent">
              <div class="text-left"><router-link class="btn btn-outline-dark mt-auto" :to="`/detail/${product.id}`">Chi
                  tiết <font-awesome-icon :icon="['fas', 'circle-info']" /></router-link></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';

const API_URL = import.meta.env.VITE_API_URL;

const categories = ref([]);
const products = ref([]);
const productByCategory = ref({});

const fetchProducts = async () => {
  try {
    const response = await axios.get(`${API_URL}/api/products`);
    if (response.status === 200) {
      products.value = response.data.data;
    }
  } catch (error) {
    console.error(error);
  }
};

const fetchCategories = async () => {
  try {
    const response = await axios.get(`${API_URL}/api/categories`);
    categories.value = response.data;
    categories.value.forEach(category => {
      getProductByCategory(category.id);
    })
  } catch (error) {
    console.error(error);
  }
};

const getProductByCategory = async (categoryId) => {
  try {
    const response = await axios.get(`${API_URL}/api/products/category/${categoryId}`);
    productByCategory.value[categoryId] = response.data.map(product => ({
      ...product,
      image: `http://127.0.0.1:8000/${product.images[0]?.image_path}`
    }));
    console.log(productByCategory.value);
  } catch (error) {
    console.error('Error fetching products by category:', error);
  }
};

const sortedProducts = computed(() => {
  return products.value.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
});

const formatVND = (price) => {
  return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);
}

onMounted(async () => {
  await fetchProducts();
  await fetchCategories();
});
</script>
<style>
#banner {
  background-image: url("https://bizweb.dktcdn.net/100/483/998/themes/904984/assets/slider_1.jpg?1722078914172");
}
</style>
