<template>
  <div class="container mt-5">
    <div class="row justify-content-center" style="height: 100vh; padding-top: 100px;">
      <div class="col-md-4">
        <h3 class="text-center mb-4">Đặt Lại Mật Khẩu</h3>
        <form @submit.prevent="resetPassword">
          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" v-model="email" placeholder="Nhập email" required>
          </div>
          <div class="mb-3">
            <label for="token" class="form-label">Token</label>
            <input type="text" class="form-control" id="token" v-model="token" placeholder="Nhập token" required>
          </div>
          <div class="mb-3">
            <label for="password" class="form-label">Mật khẩu mới</label>
            <input type="password" class="form-control" id="password" v-model="password" placeholder="Nhập mật khẩu mới"
              required>
          </div>
          <div class="mb-3">
            <label for="password_confirmation" class="form-label">Xác nhận mật khẩu mới</label>
            <input type="password" class="form-control" id="password_confirmation" v-model="password_confirmation"
              placeholder="Nhập lại mật khẩu mới" required>
          </div>
          <button type="submit" class="btn btn-primary w-100">Đặt Lại Mật Khẩu</button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useRoute, useRouter } from 'vue-router';
import Swal from 'sweetalert2';

const route = useRoute();
const router = useRouter();

const email = ref('');
const password = ref('');
const password_confirmation = ref('');
const token = route.query.token;
const API_URL = import.meta.env.VITE_API_URL;
onMounted(() => {
  email.value = route.query.email;
});

const resetPassword = async () => {
  try {
    const response = await axios.post(`${API_URL}/password/reset`, {
      email: email.value,
      password: password.value,
      password_confirmation: password_confirmation.value,
      token: token,
    });
    if (response.status === 200) {
      Swal.fire('Success', 'Mật khẩu đã được đặt lại thành công!', 'success');
      router.push('/login');
    }
  } catch (error) {
    console.error(error);
    Swal.fire('Error', 'Có lỗi xảy ra. Vui lòng thử lại!', 'error');
  }
};
</script>
