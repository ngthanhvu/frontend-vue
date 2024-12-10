<template>
  <div class="mt-5 container" style="height: 100vh;">
    <h2 class="text-center">Lịch sử mua hàng</h2>
    <div class="table-responsive">
      <table class="table table-bordered mt-3">
        <thead class="table-dark text-center">
          <tr>
            <th>#</th>
            <th>Mã đơn hàng</th>
            <th>Họ tên</th>
            <th>Email</th>
            <th>Trạng thái</th>
            <th>Hành động</th>
          </tr>
        </thead>
        <tbody class="text-center">
          <tr v-for="(order, index) in orders" :key="order.id">
            <td>{{ index + 1 }}</td>
            <td>#000{{ order.id }}</td>
            <td>{{ order.full_name }}</td>
            <td>{{ order.email }}</td>
            <!-- <td>{{ order.status }}</td> -->
            <td v-if="order.status == 'paid'"><span class="badge text-bg-success">Đã thanh toán</span></td>
            <td v-else-if="order.status == 'pending'"><span class="badge text-bg-warning text-white">Đang chờ thanh
                toán</span></td>
            <td>
              <button class="btn btn-primary" @click="openModal(order)"><font-awesome-icon
                  :icon="['fas', 'circle-info']" /></button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Overlay khi modal mở -->
    <div v-if="isModalOpen" class="overlay" @click="closeModal"></div>

    <!-- Modal Chi tiết -->
    <div v-if="isModalOpen" class="modal fade show" tabindex="-1" style="display: block;"
      aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Chi tiết đơn hàng</h5>
            <button type="button" class="btn-close" @click="closeModal"></button>
          </div>
          <div class="modal-body">
            <div v-if="selectedOrder">
              <h6>Thông tin đơn hàng:</h6>
              <p><strong>Họ tên:</strong> {{ selectedOrder.full_name }}</p>
              <p><strong>Email:</strong> {{ selectedOrder.email }}</p>
              <p><strong>Địa chỉ:</strong> {{ selectedOrder.address }}</p>
              <p><strong>Phương thức thanh toán:</strong> {{ selectedOrder.payment_method }}</p>
              <p><strong>Giảm giá:</strong> {{ selectedOrder.discount }} VNĐ</p>
              <p><strong>Tổng giá:</strong> {{ selectedOrder.total_price }} VNĐ</p>

              <h6>Sản phẩm trong đơn hàng:</h6>
              <ul>
                <li v-for="(detail, index) in selectedOrder.order_details" :key="detail.id">
                  <strong>{{ detail.product.name }}</strong> - Giá: {{ detail.price }} VNĐ - Số lượng: {{
                    detail.quantity }}
                </li>
              </ul>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="closeModal">Đóng</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import axios from 'axios';

const API_URL = import.meta.env.VITE_API_URL;
const orders = ref([]);
const user_id = localStorage.getItem('user_id');

const isModalOpen = ref(false);
const selectedOrder = ref(null);

const fetchOrders = async () => {
  try {
    const response = await axios.get(`${API_URL}/api/orders/user/${user_id}`);
    if (response.status === 200) {
      orders.value = response.data;
    }
  } catch (error) {
    console.error('Không thể lấy thông tin đơn hàng:', error);
  }
};

const openModal = (order) => {
  selectedOrder.value = order;
  isModalOpen.value = true;
};

const closeModal = () => {
  isModalOpen.value = false;
  selectedOrder.value = null;
};

onMounted(() => {
  fetchOrders();
});
</script>

<style scoped>
.modal.fade.show {
  display: block;
  animation: fadeIn 0.3s ease-out;
}

.modal-dialog {
  max-width: 800px;
}

.overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  z-index: 1040;
  opacity: 0;
  animation: fadeInOverlay 0.3s forwards;
}

@keyframes fadeIn {
  0% {
    opacity: 0;
    transform: scale(0.95);
  }

  100% {
    opacity: 1;
    transform: scale(1);
  }
}

@keyframes fadeInOverlay {
  0% {
    opacity: 0;
  }

  100% {
    opacity: 1;
  }
}

.modal-content {
  z-index: 1050;
}

.btn-close {
  font-size: 1.2rem;
}

.table-responsive {
  overflow-x: auto;
}

.table {
  min-width: 100%;
}
</style>
