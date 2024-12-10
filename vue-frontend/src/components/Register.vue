<template>
  <div class="container mt-5">
    <div class="row justify-content-center" style="height: 70vh; margin-top: 100px;">
      <div class="col-md-4">
        <h3 class="text-center mb-4">Đăng Ký</h3>
        <form @submit.prevent="registerUser">
          <div class="mb-3">
            <label for="registerName" class="form-label">Username</label>
            <input type="text" class="form-control" id="registerName" v-model="username" placeholder="Nhập username">
          </div>
          <div class="mb-3">
            <label for="registerEmail" class="form-label">Email</label>
            <input type="email" class="form-control" id="registerEmail" v-model="email" placeholder="Nhập email">
          </div>
          <div class="mb-3">
            <label for="registerPassword" class="form-label">Mật khẩu</label>
            <input type="password" class="form-control" id="registerPassword" v-model="password"
              placeholder="Nhập mật khẩu">
          </div>
          <div class="mb-3">
            <label for="registerConfirmPassword" class="form-label">Xác Nhận Mật Khẩu</label>
            <input type="password" class="form-control" id="registerConfirmPassword" v-model="confirmPassword"
              placeholder="Nhập lại mật khẩu">
          </div>
          <button type="submit" class="btn btn-success w-100">Đăng Ký</button>
        </form>
        <div class="mt-3 text-center">
          <span>Đã có tài khoản, <RouterLink to="/login" class="text-primary">Đăng Nhập</RouterLink></span>
        </div>
      </div>
    </div>
  </div>
</template>


<script setup>
import { ref } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';
import Swal from 'sweetalert2';


const router = useRouter();
const API_URL = import.meta.env.VITE_API_URL;

const username = ref('');
const email = ref('');
const password = ref('');
const confirmPassword = ref('');

const registerUser = async () => {
  // validation
  if (!username.value.trim()) {
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'Vui lòng nhập username',
    });
    return;
  }

  if (!email.value.trim()) {
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'Vui lòng nhập email',
    });
    return;
  }

  if (!password.value.trim()) {
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'Vui lí nhập mật khẩu',
    });
    return;
  }

  if (password.value !== confirmPassword.value) {
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'Mật khẩu và xác nhận mật khẩu không khớp',
    });
    return;
  }

  try {
    const response = await axios.post(`${API_URL}/api/register`, {
      username: username.value,
      email: email.value,
      password: password.value,
      password_confirmation: confirmPassword.value,
      role: 'user' // Gán mặc định role là 'user'
    });

    if (response.status === 201) {
      Swal.fire({
        icon: 'success',
        title: 'Success',
        text: 'Đăng ký thành công!',
      });
      router.push('/login');
    }
  } catch (error) {
    if (error.response && error.response.status === 422) {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'Đăng ký thất bại. Vui lòng kiểm tra lại thông tin!',
      });
    } else {
      console.error(error);
      alert('Đăng ký thất bại. Vui lòng thử lại!');
    }
  }
};
</script>
