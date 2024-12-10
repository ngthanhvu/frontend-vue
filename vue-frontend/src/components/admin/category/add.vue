<template>
  <div id="layoutSidenav_content">
    <main>
      <div class="container-fluid px-4" style="margin-top: 80px;">
        <h1 class="mt-4">Thêm danh mục</h1>
        <form @submit.prevent="submitForm">
          <div class="mb-3">
            <label for="name" class="form-label">Tên danh mục</label>
            <input v-model="category.name" type="text" class="form-control" id="name"
              placeholder="Nhập tên danh mục">
          </div>
          <div class="mb-3">
            <label for="description" class="form-label">Mô tả</label>
            <textarea v-model="category.description" class="form-control" id="description"
              placeholder="Nhập mô tả danh mục"></textarea>
          </div>
          <button type="submit" class="btn btn-primary">Submit</button>
        </form>
        <div style="height: 100vh"></div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';
const API_URL = import.meta.env.VITE_API_URL;

const category = ref({
  name: '',
  description: ''
});

const submitForm = async () => {
  //validate
  if (!category.value.name) {
    Swal.fire({
      icon: 'error',
      title: 'Lỗi',
      text: 'Vui lòng nhập tên danh mục'
    })
    return
  }
  if (!category.value.description) {
    Swal.fire({
      icon: 'error',
      title: 'Lỗi',
      text: 'Vui lòng nhập mô tả danh mục'
    })
    return
  }
  try {
    await axios.post(`${API_URL}/api/categories`, category.value);
    Swal.fire({
      icon: 'success',
      title: 'Thành công',
      text: 'Danh mục đã được tạo',
    })
    category.value = {
      name: '',
      description: ''
    };
  } catch (error) {
    console.error('Error creating category:', error.response.data);
  }
};

</script>

<style scoped>
h1 {
  color: #343a40;
}
</style>
