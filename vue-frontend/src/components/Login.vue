<template>
  <div class="container mt-5">
    <div class="row justify-content-center" style="height: 64.2vh; margin-top: 150px;">
      <div class="col-md-4">
        <h3 class="text-center mb-4">Đăng Nhập</h3>
        <form @submit.prevent="loginUser">
          <div class="mb-3">
            <label for="loginUsername" class="form-label">Username</label>
            <input type="text" class="form-control" id="loginUsername" v-model="username" placeholder="Nhập username">
          </div>
          <div class="mb-3">
            <label for="loginPassword" class="form-label">Mật khẩu</label>
            <input type="password" class="form-control" id="loginPassword" v-model="password"
              placeholder="Nhập mật khẩu">
          </div>
          <div class="mb-3 text-end">
            <span>
              <RouterLink to="/forgot-password" class="text-primary">Forgot Password?</RouterLink>
            </span>
          </div>
          <button type="submit" class="btn btn-primary w-100">Đăng Nhập</button>
        </form>
        <div class="mt-3 text-center">
          <span>Chưa có tài khoản, <RouterLink to="/register" class="text-primary">Đăng Ký</RouterLink></span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watchEffect } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';
import Swal from 'sweetalert2';

const router = useRouter();
const API_URL = import.meta.env.VITE_API_URL;

const username = ref('');
const password = ref('');
const isLogin = ref(localStorage.getItem('isLogin') === 'true');

const loginUser = async () => {
  // Validate form
  if (!username.value.trim()) {
    Swal.fire({
      icon: 'error',
      title: 'Lỗi',
      text: 'Vui lòng nhập tên đăng nhập!',
    });
    return;
  }

  if (!password.value.trim()) {
    Swal.fire({
      icon: 'error',
      title: 'Lỗi',
      text: 'Vui lòng nhập mật khẩu!',
    });
    return;
  }

  try {
    const response = await axios.post(`${API_URL}/api/login`, {
      username: username.value,
      password: password.value,
    });

    if (response.status === 200) {
      const token = response.data.token;
      const userResponse = await axios.get(`${API_URL}/api/user`, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      });

      const userRole = userResponse.data.role;

      localStorage.setItem('token', token);
      localStorage.setItem('role', userRole);
      localStorage.setItem('isLogin', 'true');
      localStorage.setItem('user_id', userResponse.data.id);
      localStorage.setItem('username', userResponse.data.username);
      isLogin.value = true;

      Swal.fire({
        icon: 'success',
        title: 'Success',
        text: 'Đăng nhập thành công!',
      })
      window.location.href = '/';
    }
  } catch (error) {
    if (error.response && error.response.status === 401) {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'Đăng nhập thất bại. Vui lòng kiểm tra lại thông tin!',
      });
    } else {
      console.error(error);
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'Đăng nhập thất bại. Vui lòng thử lại!',
      });
    }
  }
};

// Sử dụng watchEffect để theo dõi sự thay đổi của localStorage
watchEffect(() => {
  isLogin.value = localStorage.getItem('isLogin') === 'true';
});
</script>
