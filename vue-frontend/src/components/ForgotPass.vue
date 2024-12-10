<template>
  <div class="container mt-5" style="min-height: 100vh; padding-top: 100px;">
    <div class="row justify-content-center">
      <div class="col-md-4">
        <h3 class="text-center mb-4">Quên Mật Khẩu</h3>
        <form @submit.prevent="submitEmail">
          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" v-model="email" placeholder="Nhập email" required>
          </div>
          <button type="submit" class="btn btn-primary w-100">Gửi Liên Kết Đặt Lại Mật Khẩu</button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';

const API_URL = import.meta.env.VITE_API_URL;
const email = ref('');

const submitEmail = async () => {
  try {
    const response = await axios.post(`${API_URL}/password/forgot`, {
      email: email.value,
    });
    Swal.fire({
      icon: 'success',
      title: 'Success',
      text: 'Liên kết đặt lại mật khấu đã được gửi!',
    })

  } catch (error) {
    console.error(error);
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'Có lỗi xảy ra. Vui lòng thử lại!',
    })
  }
};
</script>
