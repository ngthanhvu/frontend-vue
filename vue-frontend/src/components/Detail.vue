<template>
  <!-- Product section-->
  <section class="py-5">
    <div class="container px-4 px-lg-5 my-5">
      <div class="row gx-4 gx-lg-5 align-items-center">
        <div class="col-md-6">
          <img class="card-img-top mb-5 mb-md-0"
            :src="`${API_URL}/storage/` + product.images.find((image) => image.is_main === 1)?.path" alt="..." />
          <div class="d-flex mt-3">
            <img class="img-thumbnail me-2" v-for="image in product.images" :key="image.id"
              :src="`${API_URL}/storage/` + image.path" style="max-width: 100px; max-height: 100px;" alt="..." />
          </div>
        </div>
        <div class="col-md-6">
          <div class="small mb-1">
            <p class="text-muted fw-bold fs-6">Danh mục: {{ product.category.name }}</p>
          </div>
          <h1 class="display-5 fw-bolder">{{ product.name }}</h1>
          <div class="fs-5 mb-5">
            <!-- <span class="text-decoration-line-through">$45.00</span> -->
            <span>Giá: {{ formatVND(product.price) }}</span>
          </div>
          <p class="lead">Mô tả: {{ product.description }}</p>
          <div class="d-flex">
            <input class="form-control text-center me-3" id="inputQuantity" type="num" value="1"
              style="max-width: 3rem" />
            <button class="btn btn-outline-dark flex-shrink-0" type="button" @click="addToCart">
              <i class="bi-cart-fill me-1"></i>
              Thêm vào giỏ hàng <font-awesome-icon :icon="['fas', 'cart-plus']" />
            </button>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="py-5 bg-light">
    <div class="container px-4 px-lg-5 mt-5">
      <h2 class="fw-bolder mb-4">Sản phẩm liên quan</h2>
      <div class="row gx-4 gx-lg-5 row-cols-2 row-cols-md-3 row-cols-xl-4 justify-content-center">

        <div class="col mb-5" v-for="product in relatedProducts" :key="product.id">
          <div class="card h-100">
            <!-- Product image-->
            <img class="card-img-top"
              :src="`${API_URL}/storage/` + product.images.find((image) => image.is_main === 1)?.path" alt="..." />
            <!-- Product details-->
            <div class="card-body p-4">
              <div class="text-center">
                <!-- Product name-->
                <h5 class="fw-bolder">{{ product.name }}</h5>
                <!-- Product price-->
                {{ formatVND(product.price) }}
              </div>
            </div>
            <!-- Product actions-->
            <div class="card-footer p-4 pt-0 border-top-0 bg-transparent">
              <div class="text-center"><a class="btn btn-outline-dark mt-auto" :href="`/detail/${product.id}`">Chi
                  tiết <font-awesome-icon :icon="['fas', 'circle-info']" /></a></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- Footer-->
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useRoute, useRouter } from 'vue-router';
const route = useRoute();
const router = useRouter();
import Swal from 'sweetalert2';

const API_URL = import.meta.env.VITE_API_URL;

const user_id = localStorage.getItem('user_id');
const product = ref({
  images: [],
  category: {}
});
const categories = ref([]);
const relatedProducts = ref([]);

const fetchCategories = async () => {
  try {
    const response = await axios.get(`${API_URL}/api/categories`);
    if (response.status === 200) {
      categories.value = response.data;
    }
  } catch (error) {
    console.error(error);
  }
};

const fetchProduct = async () => {
  try {
    const response = await axios.get(`${API_URL}/api/products/${route.params.id}`);
    if (response.status === 200) {
      product.value = response.data;
    }
  } catch (error) {
    console.error(error);
  }
};

const addToCart = async () => {
  const quantity = document.getElementById('inputQuantity').value;

  try {
    const response = await axios.post(`${API_URL}/api/cart`, {
      user_id: user_id,
      product_id: product.value.id,
      quantity: quantity,
      total_price: product.value.price * quantity,
      discount: 0
    });

    if (response.status === 201) {
      Swal.fire({
        icon: 'success',
        title: 'Thành công',
        text: 'Đã thêm vào giỏ hàng',
      });
      router.push('/cart');
    }
  } catch (error) {
    console.error('Error adding to cart:', error);
  }
};

const fetchRelatedProducts = async () => {
  try {
    const response = await axios.get(`${API_URL}/api/products/category/${product.value.category.id}`);
    if (response.status === 200) {
      relatedProducts.value = response.data;
    }
  } catch (error) {
    console.error(error);
  }
};

const formatVND = (price) => {
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND',
  }).format(price);
};

onMounted(async () => {
  await fetchCategories();
  await fetchProduct();
  fetchRelatedProducts();
});
</script>
