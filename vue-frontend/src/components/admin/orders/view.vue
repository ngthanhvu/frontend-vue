<template>
  <div id="layoutSidenav_content">
    <main>
      <div class="container-fluid px-4" style="margin-top: 80px;">
        <h2 class="mt-4">Quản lý đơn hàng</h2>
        <table class="table table-bordered mt-3">
          <thead class="table-dark text-center">
            <tr>
              <th>#</th>
              <th>Họ tên</th>
              <th>Email</th>
              <th>Trạng thái</th>
              <th>Hành động</th>
            </tr>
          </thead>
          <tbody class="text-center">
            <tr v-for="(order, index) in pagedOrders" :key="order.id">
              <td>{{ index + 1 }}</td>
              <td>{{ order.full_name }}</td>
              <td>{{ order.email }}</td>
              <td>
                <span v-if="order.status === 'paid'" class="badge text-bg-success">Đã thanh toán</span>
                <span v-else-if="order.status === 'pending'" class="badge text-bg-warning text-white">Đang chờ thanh
                  toán</span>
                <span v-else-if="order.status === 'shipping'" class="badge text-bg-info text-white">Đang vận
                  chuyển</span>
                <span v-else-if="order.status === 'completed'" class="badge text-bg-success">Hoàn thành</span>
                <span v-else-if="order.status === 'canceled'" class="badge text-bg-danger">Hủy</span>
              </td>
              <td>
                <!-- Nút chỉnh sửa trạng thái -->
                <button class="btn btn-primary me-2" @click="openModal(order)">
                  <font-awesome-icon :icon="['fas', 'circle-info']" />
                </button>
                <!-- Nút xóa đơn hàng (nếu cần) -->
                <button class="btn btn-danger me-2" @click="deleteOrder(order.id)">
                  <font-awesome-icon :icon="['fas', 'trash']" />
                </button>
                <!-- Nút mở modal sửa trạng thái -->
                <button class="btn btn-success me-2" @click="openStatusModal(order)">
                  <font-awesome-icon :icon="['far', 'pen-to-square']" />
                </button>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- phân trang -->
        <div class="d-flex justify-content-center mt-3">
          <nav aria-label="Page navigation example">
            <ul class="pagination">
              <li class="page-item" :class="{ disabled: currentPage === 1 }">
                <a class="page-link" href="#" @click.prevent="goToPage(currentPage - 1)">Previous</a>
              </li>
              <li class="page-item" v-for="page in totalPages" :key="page" :class="{ active: page === currentPage }">
                <a class="page-link" href="#" @click.prevent="goToPage(page)">{{
                  page
                }}</a>
              </li>
              <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                <a class="page-link" href="#" @click.prevent="goToPage(currentPage + 1)">Next</a>
              </li>
            </ul>
          </nav>
        </div>

        <!-- Modal Chi tiết đơn hàng -->
        <div v-if="isModalOpen" class="overlay" @click="closeModal"></div>
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

        <!-- Modal Chỉnh sửa trạng thái đơn hàng -->
        <div v-if="isStatusModalOpen" class="modal fade show" tabindex="-1" style="display: block;"
          aria-labelledby="statusModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="statusModalLabel">Chỉnh sửa trạng thái đơn hàng</h5>
                <button type="button" class="btn-close" @click="closeStatusModal"></button>
              </div>
              <div class="modal-body">
                <div v-if="selectedOrder">
                  <h6>Trạng thái hiện tại:</h6>
                  <p><strong>{{ selectedOrder.status === 'paid' ? 'Đã thanh toán' : selectedOrder.status === 'pending' ?
                    'Đang chờ thanh toán' : selectedOrder.status }}</strong></p>

                  <h6>Chọn trạng thái mới:</h6>
                  <!-- Dropdown để chọn trạng thái mới -->
                  <select v-model="newStatus" class="form-select">
                    <option value="pending">Đang chờ thanh toán</option>
                    <option value="paid">Đã thanh toán</option>
                  </select>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" @click="closeStatusModal">Đóng</button>
                <button type="button" class="btn btn-primary" @click="updateStatus(selectedOrder.id, newStatus)">Cập
                  nhật trạng thái</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { onMounted, ref, computed } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';

const API_URL = import.meta.env.VITE_API_URL;
const orders = ref([]);
const user_id = localStorage.getItem('user_id');

const isModalOpen = ref(false);
const isStatusModalOpen = ref(false);
const selectedOrder = ref(null);
const newStatus = ref('');

const itemsPerPage = 10;
const currentPage = ref(1);

const totalPages = computed(() => {
  return Math.ceil(orders.value.length / itemsPerPage);
});

const pagedOrders = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage;
  const end = start + itemsPerPage;
  return orders.value.slice(start, end);
});

const goToPage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page;
  }
};

const fetchOrders = async () => {
  try {
    const response = await axios.get(`${API_URL}/api/orders`);
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

const openStatusModal = async (order) => {
  const result = await Swal.fire({
    title: 'Bạn có chắc chắn?',
    text: 'Bạn muốn sửa trạng thái đơn hàng này?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Sửa',
    cancelButtonText: 'Hủy',
  });

  if (result.isConfirmed) {
    selectedOrder.value = order;
    newStatus.value = order.status;
    isStatusModalOpen.value = true;
  }
};


const closeStatusModal = () => {
  isStatusModalOpen.value = false;
  selectedOrder.value = null;
};

const updateStatus = async (orderId, newStatus) => {
  try {
    const response = await axios.put(`${API_URL}/api/orders/${orderId}`, { status: newStatus });
    if (response.status === 200) {
      fetchOrders();
      closeStatusModal();
      Swal.fire('Thành công', 'Trạng thái đơn hàng được cập nhật', 'success');
    }
  } catch (error) {
    console.error('Không thể cập nhật trạng thái đơn hàng:', error);
  }
};

const deleteOrder = async (orderId) => {
  try {
    const result = await Swal.fire({
      title: 'Bạn có chắc chắn?',
      text: 'Hành động này không thể hoàn tác!',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Xóa',
      cancelButtonText: 'Hủy',
    });

    if (result.isConfirmed) {
      await axios.delete(`${API_URL}/api/orders/${orderId}`);
      fetchOrders();
      Swal.fire('Thành công', 'Đơn hàng được xóa', 'success');
    }
  } catch (error) {
    console.error('Không thể xóa đơn hàng:', error);
  }
}

onMounted(() => {
  fetchOrders();
});
</script>

<style scoped>
.modal-body select {
  width: 100%;
  padding: 8px;
  border-radius: 4px;
  border: 1px solid #ccc;
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
</style>
