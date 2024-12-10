<template>
  <div id="layoutSidenav_content">
    <main>
      <div class="container-fluid px-4" style="margin-top: 80px;">
        <h1 class="mt-4">Thêm sản phẩm</h1>
        <form @submit.prevent="submitForm">
          <div class="mb-3">
            <label for="productName" class="form-label">Tên sản phẩm</label>
            <input v-model="product.name" type="text" class="form-control" id="productName"
              placeholder="Nhập tên sản phẩm">
          </div>
          <div class="mb-3">
            <label for="price" class="form-label">Giá</label>
            <input v-model="product.price" type="number" class="form-control" id="price"
              placeholder="Nhập giá sản phẩm">
          </div>
          <div class="mb-3">
            <label for="quantity" class="form-label">Số lượng</label>
            <input v-model="product.quantity" type="number" class="form-control" id="quantity"
              placeholder="Nhập số lượng sản phẩm">
          </div>
          <div class="mb-3">
            <label for="images" class="form-label">Hình ảnh</label>
            <input @change="handleFileUpload" type="file" class="form-control" id="images" multiple>
            <div v-if="product.images.length">
              <label for="mainImage" class="form-label mt-2">Chọn ảnh chính</label>
              <div v-for="(image, index) in product.images" :key="index" class="form-check">
                <input class="form-check-input" type="radio" :id="'mainImage' + index" name="mainImage" :value="index"
                  v-model="product.main_image_index" />
                <label class="form-check-label" :for="'mainImage' + index">
                  Ảnh {{ index + 1 }}
                </label>
              </div>
            </div>
          </div>
          <div class="mb-3">
            <label for="category" class="form-label">Danh mục</label>
            <select v-model="product.category_id" class="form-control" id="category">
              <option value="">-- Chọn danh mục --</option>
              <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.name }}
              </option>
            </select>
          </div>
          <button type="submit" class="btn btn-primary">Submit</button>
        </form>
        <div style="height: 100vh"></div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'

const API_URL = import.meta.env.VITE_API_URL;

const resetForm = ref({
  name: '',
  price: '',
  quantity: '',
  category_id: '',
  images: [],
  main_image_index: 0
})

const product = ref({
  name: '',
  price: '',
  quantity: '',
  category_id: '',
  images: [],
  main_image_index: 0
})
const categories = ref([])

const fetchCategories = async () => {
  try {
    const response = await axios.get(`${API_URL}/api/categories`)
    if (response.status === 200) {
      categories.value = response.data
    }
  } catch (error) {
    console.error('Error fetching categories:', error)
  }
}

const handleFileUpload = (event) => {
  const files = event.target.files
  if (files.length) {
    product.value.images = Array.from(files)
  }
}

const submitForm = async () => {
  const formData = new FormData()
  formData.append('name', product.value.name)
  formData.append('price', product.value.price)
  formData.append('quantity', product.value.quantity)
  formData.append('category_id', product.value.category_id)

  //validate
  if (!product.value.name) {
    Swal.fire({
      icon: 'error',
      title: 'Lỗi',
      text: 'Vui lòng nhập tên sản phẩm'
    })
    return
  }
  if (!product.value.price) {
    Swal.fire({
      icon: 'error',
      title: 'Lỗi',
      text: 'Vui lòng nhập giá'
    })
    return
  }
  if (!product.value.quantity) {
    Swal.fire({
      icon: 'error',
      title: 'Lỗi',
      text: 'Vui lòng nhập số lượng'
    })
    return
  }
  if (!product.value.category_id) {
    Swal.fire({
      icon: 'error',
      title: 'Lỗi',
      text: 'Vui lòng chọn danh mục'
    })
    return
  }

  if (!product.value.images.length) {
    Swal.fire({
      icon: 'error',
      title: 'Lỗi',
      text: 'Vui lòng chọn hình ảnh'
    })
    return
  }

  product.value.images.forEach((image, index) => {
    formData.append('images[]', image)
    if (index === product.value.main_image_index) {
      formData.append('main_image_index', index)
    }
  })

  try {
    await axios.post(`${API_URL}/api/products`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    })
    Swal.fire({
      icon: 'success',
      title: 'Thành công',
      text: 'Sản phẩm đã được tạo'
    })
    product.value = resetForm.value
  } catch (error) {
    console.error('Error creating product:', error.response.data)
  }
}

onMounted(() => {
  fetchCategories()
})
</script>

<style scoped>
h1 {
  color: #343a40;
}
</style>
