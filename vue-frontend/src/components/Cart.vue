<!-- <template>
  <div class="container" style="min-height: 100vh; padding-top: 100px;">
    <h2 class="text-center mt-5 mb-3">Giỏ Hàng</h2>
    <table class="table table-cart text-center">
      <thead>
        <tr>
          <th>Tên Sản phẩm</th>
          <th>Giá</th>
          <th>Số lượng</th>
          <th>Tạm tính</th>
          <th>Hành động</th>
        </tr>
      </thead>
      <tbody class="text-center">
        <tr v-for="cart in carts" :key="cart.id">
          <td class="text-start">{{ cart.product.name }}</td>
          <td>{{ cart.product.price }}</td>
          <td>
            <button class="btn btn-outline-secondary" @click="adjustQuantity(cart.id, cart.quantity - 1)"
              :disabled="cart.quantity <= 1">-</button>

            <span class="mx-3">{{ cart.quantity }}</span>

            <button class="btn btn-outline-secondary" @click="adjustQuantity(cart.id, cart.quantity + 1)">+</button>
          </td>
          <td>{{ formatVND(cart.product.price * cart.quantity) }}</td>
          <td><button class="btn btn-danger" @click="removeFromCart(cart.id)">Xóa</button></td>
        </tr>

        <tr v-if="carts.length === 0">
          <td colspan="5" class="text-center">Không có sản phẩm trong giỏ hàng</td>
        </tr>
      </tbody>
    </table>
    <div class="row">
      <div class="col-md-4">
      </div>
      <div class="col-md-8 mt-5">
        <div style="margin-left: 500px;">
          <h4 class="text-start">Tổng tiền: {{ formatVND(totalPrice - discount) }}</h4>
          <router-link to="/checkout" class="btn btn-danger" style="width: 100%;">Tiến hành thanh toán</router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';
import { useRouter } from 'vue-router';

const router = useRouter();

const API_URL = import.meta.env.VITE_API_URL;
const carts = ref([]);
const user_id = localStorage.getItem('user_id');
const totalPrice = ref(0);
const couponCode = ref('');
const discount = ref(0);

const fetchCart = async () => {
  try {
    const response = await axios.get(`${API_URL}/api/cart/${user_id}`);
    carts.value = response.data;
    totalPrice.value = carts.value.reduce((sum, cart) => sum + cart.product.price * cart.quantity, 0);
  } catch (error) {
    console.error('Error fetching cart:', error);
  }
};

const adjustQuantity = async (cartId, newQuantity) => {
  if (newQuantity < 1) return;

  try {
    const response = await axios.put(`${API_URL}/api/cart/update/${cartId}`, { quantity: newQuantity });
    if (response.status === 200) {
      fetchCart();
    }
  } catch (error) {
    console.error('Error updating quantity:', error);
  }
};

const removeFromCart = async (cartId) => {
  const result = await Swal.fire({
    title: 'Bạn chắc chắn muốn xóa sản phẩm này khỏi giỏ hàng?',
    text: "Hành động này không thể hoàn tác!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Xóa',
    cancelButtonText: 'Hủy',
    reverseButtons: true
  });

  if (result.isConfirmed) {
    try {
      const response = await axios.delete(`${API_URL}/api/cart/${cartId}`);
      if (response.status === 200) {
        fetchCart();
        Swal.fire('Đã xóa!', 'Sản phẩm đã được xóa khỏi giỏ hàng.', 'success');
      }
    } catch (error) {
      console.error('Error removing product from cart:', error);
      Swal.fire('Lỗi!', 'Không thể xóa sản phẩm, vui lòng thử lại.', 'error');
    }
  } else {
    Swal.fire('Đã hủy', 'Sản phẩm không bị xóa.', 'info');
  }
};

const formatVND = (price) => {
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND',
  }).format(price);
};


onMounted(() => {
  fetchCart();
});
</script>

<style scoped>
.product-image {
  width: 60px;
  height: 60px;
  object-fit: cover;
}
</style> -->

<template>
  <div class="container" style="min-height: 100vh; padding-top: 100px;">
    <h2 class="text-center mt-5 mb-3">Giỏ Hàng</h2>

    <!-- Thêm class "table-responsive" để tạo khả năng cuộn trên các thiết bị di động -->
    <div class="table-responsive">
      <table class="table table-hover text-center">
        <thead>
          <tr>
            <th>#</th>
            <th>Tên Sản phẩm</th>
            <th>Giá</th>
            <th>Số lượng</th>
            <th>Tạm tính</th>
            <th>Hành động</th>
          </tr>
        </thead>
        <tbody class="text-center">
          <tr v-for="(cart, index) in carts" :key="index">
            <td>{{ index + 1 }}</td>
            <td>{{ cart.product.name }}</td>
            <td>{{ formatVND(cart.product.price) }}</td>
            <td>
              <button class="btn btn-outline-secondary" @click="adjustQuantity(cart.id, cart.quantity - 1)"
                :disabled="cart.quantity <= 1">-</button>
              <span class="mx-3">{{ cart.quantity }}</span>
              <button class="btn btn-outline-secondary" @click="adjustQuantity(cart.id, cart.quantity + 1)">+</button>
            </td>
            <td>{{ formatVND(cart.product.price * cart.quantity) }}</td>
            <td><button class="btn btn-danger" @click="removeFromCart(cart.id)"><font-awesome-icon
                  :icon="['far', 'trash-can']" /></button></td>
          </tr>

          <tr v-if="carts.length === 0">
            <td colspan="5" class="text-center">Không có sản phẩm trong giỏ hàng</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Phần tổng tiền và thanh toán -->
    <div class="row">
      <div class="col-12 col-md-8 offset-md-4 mt-5">
        <div class="text-end">
          <h4>Tổng tiền: {{ formatVND(totalPrice - discount) }}</h4>
          <router-link to="/checkout" class="btn btn-danger w-50">Tiến hành thanh toán <font-awesome-icon
              :icon="['fas', 'credit-card']" /></router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';
import { useRouter } from 'vue-router';

const router = useRouter();

const API_URL = import.meta.env.VITE_API_URL;
const carts = ref([]);
const user_id = localStorage.getItem('user_id');
const totalPrice = ref(0);
const couponCode = ref('');
const discount = ref(0);

const fetchCart = async () => {
  try {
    const response = await axios.get(`${API_URL}/api/cart/${user_id}`);
    carts.value = response.data;
    totalPrice.value = carts.value.reduce((sum, cart) => sum + cart.product.price * cart.quantity, 0);
  } catch (error) {
    console.error('Error fetching cart:', error);
  }
};

const adjustQuantity = async (cartId, newQuantity) => {
  if (newQuantity < 1) return;

  try {
    const response = await axios.put(`${API_URL}/api/cart/update/${cartId}`, { quantity: newQuantity });
    if (response.status === 200) {
      fetchCart();
    }
  } catch (error) {
    console.error('Error updating quantity:', error);
  }
};

const removeFromCart = async (cartId) => {
  const result = await Swal.fire({
    title: 'Bạn chắc chắn muốn xóa sản phẩm này khỏi giỏ hàng?',
    text: "Hành động này không thể hoàn tác!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Xóa',
    cancelButtonText: 'Hủy',
    reverseButtons: true
  });

  if (result.isConfirmed) {
    try {
      const response = await axios.delete(`${API_URL}/api/cart/${cartId}`);
      if (response.status === 200) {
        fetchCart();
        Swal.fire('Đã xóa!', 'Sản phẩm đã được xóa khỏi giỏ hàng.', 'success');
      }
    } catch (error) {
      console.error('Error removing product from cart:', error);
      Swal.fire('Lỗi!', 'Không thể xóa sản phẩm, vui lòng thử lại.', 'error');
    }
  } else {
    Swal.fire('Đã hủy', 'Sản phẩm không bị xóa.', 'info');
  }
};

const formatVND = (price) => {
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND',
  }).format(price);
};

onMounted(() => {
  fetchCart();
});
</script>

<style scoped>
.product-image {
  width: 60px;
  height: 60px;
  object-fit: cover;
}
</style>
