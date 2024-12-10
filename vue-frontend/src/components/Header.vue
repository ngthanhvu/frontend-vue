<template>
  <!-- Navigation -->
  <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container px-4 px-lg-5">
      <router-link class="navbar-brand" to="/">Frontend Vue</router-link>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
          <li class="nav-item">
            <router-link class="nav-link" to="/">Trang chủ</router-link>
          </li>
          <li class="nav-item">
            <router-link class="nav-link" to="/products">Sản phẩm</router-link>
          </li>
          <li class="nav-item">
            <router-link class="nav-link" to="/history">Lịch sử</router-link>
          </li>
          <li class="nav-item">
            <router-link class="nav-link" to="/profile">Thông tin cá nhân</router-link>
          </li>
          <li class="nav-item" v-if="isAdmin">
            <router-link class="nav-link" to="/admin">Admin</router-link>
          </li>
        </ul>
        <div class="d-flex me-3 cart-mb">
          <router-link to="/cart" class="btn btn-outline-dark ms2"><font-awesome-icon
              :icon="['fas', 'bag-shopping']" /><span class="ps-2">{{ carts.length }}</span></router-link>
        </div>
        <div class="d-flex" v-if="!isLogin">
          <router-link class="btn btn-outline-success" to="/register">Đăng ký</router-link>
          <router-link class="btn btn-outline-primary ms-2" to="/login">Đăng nhập</router-link>
        </div>

        <div class="d-flex logout-mb" v-else>
          <button class="btn btn-outline-danger" @click="logout">Đăng xuất</button>
          <span class="ms-2 mt-2">Xin chào, <span class="fw-bold">{{ username }}</span>!!</span>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { ref, watchEffect, onMounted, watch } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const API_URL = import.meta.env.VITE_API_URL;
const user_id = localStorage.getItem('user_id');
const carts = ref([]);
const totalPrice = ref(0);

const router = useRouter();
const isLogin = ref(localStorage.getItem('isLogin') === 'true');
const isAdmin = ref(localStorage.getItem('role') === 'admin');
const username = ref(localStorage.getItem('username'));
const logout = () => {
  localStorage.removeItem('token');
  localStorage.removeItem('role');
  localStorage.removeItem('isLogin');
  localStorage.removeItem('user_id');
  localStorage.removeItem('username');
  isLogin.value = false;
  // router.push('/login');
  window.location.href = '/login';
};

const fetchCart = async () => {
  try {
    const response = await axios.get(`${API_URL}/api/cart/${user_id}`);
    carts.value = response.data;
    totalPrice.value = carts.value.reduce((sum, cart) => sum + cart.product.price * cart.quantity, 0);
  } catch (error) {
    console.error('Error fetching cart:', error);
  }
};

watchEffect(() => {
  isLogin.value = localStorage.getItem('isLogin') === 'true';
  fetchCart();
});

onMounted(() => {
  fetchCart();
});
</script>
<style>
@media screen and (max-width: 600px) {
  .logout-mb {
    flex-direction: column;
    margin-top: 10px;
  }

  .cart-mb {
    flex-direction: column;
    margin-left: 10px;
  }
}
</style>