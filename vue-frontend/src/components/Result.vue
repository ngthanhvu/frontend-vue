<template>
  <div v-if="order" class="container mt-5 text-center" style="height: 100vh; padding-top: 150px;">
    <font-awesome-icon :icon="['far', 'circle-check']" class="text-success" v-if="status === '00'"
      style="font-size: 100px;" />
    <font-awesome-icon :icon="['fas', 'triangle-exclamation']" class="text-danger" v-if="status === '01'"
      style="font-size: 100px;" />
    <h2>Thanh toán {{ status === '00' ? 'thành công' : 'thất bại' }}</h2>
    <div v-if="status === '00'">
      <p>Đơn hàng của bạn đã được thanh toán thành công!</p>
    </div>
    <div v-if="status === '01'">
      <p>Thanh toán thất bại. Vui lòng thử lại.</p>
    </div>
    <router-link to="/" class="btn btn-danger me-2"><font-awesome-icon :icon="['fas', 'house-chimney']" /></router-link>
    <router-link to="/history" class="btn btn-primary"><font-awesome-icon
        :icon="['fas', 'clock-rotate-left']" /></router-link>
  </div>
  <div v-else>
    <p>Đang tải...</p>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useRoute } from 'vue-router';

const order = ref(null);
const route = useRoute();
const API_URL = import.meta.env.VITE_API_URL;

const user_id = localStorage.getItem('user_id');
const status = ref('');
onMounted(async () => {
  status.value = route.query.status;
  const orderId = route.query.order_id;

  console.log('Status:', status.value);
  console.log('Order ID:', orderId);

  if (orderId) {
    try {
      const response = await axios.get(`${API_URL}/api/orders/${orderId}`);
      order.value = response.data;

      console.log('Order status từ API:', order.value.status);

      if (status.value === '00') {
        await clearCart();
      }

    } catch (error) {
      console.error('Không thể lấy thông tin đơn hàng:', error);
    }
  }
});

const clearCart = async () => {
  try {
    const response = await axios.delete(`${API_URL}/api/cart/clear/${user_id}`);
    if (response.status === 200) {
      console.log('Cart cleared successfully');
    }
  } catch (error) {
    console.error('Error clearing cart:', error);
  }
};
</script>
