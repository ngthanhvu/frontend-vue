<template>
  <div class="container mt-5" style="height: 100vh; padding-top: 150px;">
    <h2 class="text-center mb-4">Thanh toán</h2>

    <div class="row">
      <!-- Thông tin người nhận và phương thức thanh toán -->
      <div class="col-md-6">
        <form>
          <!-- Thông tin người nhận -->
          <div class="mb-3">
            <label for="fullName" class="form-label">Họ và tên</label>
            <input type="text" class="form-control" id="fullName" placeholder="Nhập họ tên" required>
          </div>
          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" placeholder="Nhập email" required>
          </div>

          <!-- Địa chỉ -->
          <div class="mb-3">
            <label for="address" class="form-label">Địa chỉ giao hàng</label>
            <input type="text" class="form-control" id="address" placeholder="Nhập địa chỉ" required>
          </div>

          <!-- Phương thức thanh toán -->
          <fieldset class="mb-3">
            <legend class="form-label">Phương thức thanh toán</legend>
            <!-- <div class="form-check">
              <input type="radio" class="form-check-input" id="creditCard" value="creditCard" v-model="paymentMethod"
                required />
              <label class="form-check-label" for="creditCard">Thẻ tín dụng</label>
            </div> -->
            <div class="form-check">
              <input type="radio" class="form-check-input" id="cod" value="cod" v-model="paymentMethod" required />
              <label class="form-check-label" for="cod">Thanh toán khi nhận hàng</label>
            </div>
            <div class="form-check">
              <input type="radio" class="form-check-input" id="vnpay" value="vnpay" v-model="paymentMethod" required />
              <label class="form-check-label" for="paypal">VnPay</label>
            </div>
          </fieldset>

          <!-- Xác nhận thanh toán -->
          <div class="mb-4">
            <button type="submit" class="btn btn-primary w-100" @click.prevent="placeOrder">Hoàn tất thanh toán</button>
          </div>
        </form>
      </div>

      <!-- Giỏ hàng (Cart) -->
      <div class="col-md-6">
        <div class="card">
          <div class="card-header">
            <h5 class="card-title">Giỏ hàng</h5>
          </div>
          <div class="card-body">
            <ul class="list-group" v-for="item in carts" :key="item.id">
              <li class="list-group-item d-flex justify-content-between align-items-center">
                {{ item.product.name }}
                <span>₫{{ item.product.price }}</span>
              </li>
            </ul>
            <ul class="list-group" v-if="carts.length === 0">
              <li class="list-group-item text-center">Giỏ hàng trống</li>
            </ul>
          </div>
        </div>

        <!-- Input mã coupon -->
        <div class="coupon mt-3">
          <div class="input-group mb-3">
            <input type="text" class="form-control" v-model="couponCode" placeholder="Nhập mã coupon" />
            <button class="btn btn-primary" @click="applyCoupon">Xác nhận</button>
          </div>
        </div>

        <!-- Hiển thị giá trị giảm giá và tổng tiền -->
        <div class="card-footer text-end mt-3">
          <h4>Giảm giá: <span>₫{{ discount }}</span></h4>
          <h4>Tổng cộng: <span>₫{{ totalPrice }}</span></h4>
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
const product_id = ref([]);
const paymentMethod = ref('creditCard');

console.log(JSON.stringify(product_id.value));

const fetchCart = async () => {
  try {
    const response = await axios.get(`${API_URL}/api/cart/${user_id}`);
    carts.value = response.data;
    product_id.value = carts.value.map(item => item.product.id);
    calculateTotalPrice();
  } catch (error) {
    console.error('Error fetching cart:', error);
  }
};

const calculateTotalPrice = () => {
  totalPrice.value = carts.value.reduce((sum, cart) => sum + cart.product.price * cart.quantity, 0);
  totalPrice.value -= discount.value;
};

const applyCoupon = async () => {
  if (!couponCode.value) {
    Swal.fire('Lỗi', 'Vui lòng nhập mã coupon!', 'error');
    return;
  }

  try {
    const response = await axios.post(`${API_URL}/api/coupons/apply`, { code: couponCode.value });
    if (response.data.message === 'Coupon applied successfully') {
      discount.value = response.data.coupon.discount;
      calculateTotalPrice();
    } else {
      Swal.fire('Lỗi', response.data.message, 'error');
    }
  } catch (error) {
    console.error('Error applying coupon:', error);
    Swal.fire('Lỗi', 'Có lỗi xảy ra khi áp dụng coupon. Vui lòng thử lại.', 'error');
  }
};

const placeOrder = async () => {
  // Lấy dữ liệu từ form
  const fullName = document.getElementById('fullName').value.trim();
  const email = document.getElementById('email').value.trim();
  const address = document.getElementById('address').value.trim();
  const payment = paymentMethod.value;

  // Validate dữ liệu
  if (!fullName) {
    Swal.fire('Lỗi', 'Vui lòng nhập họ và tên.', 'error');
    return;
  }
  if (!email) {
    Swal.fire('Lỗi', 'Vui lòng nhập email.', 'error');
    return;
  }
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(email)) {
    Swal.fire('Lỗi', 'Vui lòng nhập email hợp lệ.', 'error');
    return;
  }
  if (!address) {
    Swal.fire('Lỗi', 'Vui lòng nhập địa chỉ giao hàng.', 'error');
    return;
  }
  if (!payment) {
    Swal.fire('Lỗi', 'Vui lòng chọn phương thức thanh toán.', 'error');
    return;
  }

  const products = carts.value.map((item) => ({
    id: item.product.id,
    quantity: item.quantity,
  }));

  const orderData = {
    user_id: user_id,
    full_name: document.getElementById('fullName').value,
    email: document.getElementById('email').value,
    address: document.getElementById('address').value,
    payment_method: paymentMethod.value,
    total_price: totalPrice.value,
    discount: discount.value,
    products: products,
  };

  console.log(orderData);

  try {
    const orderResponse = await axios.post(`${API_URL}/api/orders`, orderData);
    if (orderResponse.status === 201) {
      const createdOrderId = orderResponse.data.order.id;
      console.log('Created order ID:', createdOrderId);

      if (paymentMethod.value === 'vnpay') {
        const dataVnpay = {
          order_id: createdOrderId,
          amount: orderData.total_price,
        };

        try {
          const vnpayResponse = await axios.post(`${API_URL}/api/vnpay`, dataVnpay);

          if (vnpayResponse.status === 200) {
            const vnpayUrl = vnpayResponse.data.url;

            if (vnpayUrl && vnpayUrl.startsWith('http')) {
              console.log('VnPay URL:', vnpayUrl);
              window.location.href = vnpayUrl;
            } else {
              console.error('Invalid VnPay URL:', vnpayUrl);
              Swal.fire('Lỗi', 'URL thanh toán không hợp lệ', 'error');
            }
          }
        } catch (error) {
          console.error('Error creating VnPay payment URL:', error);
          Swal.fire('Lỗi', 'Không thể tạo URL thanh toán', 'error');
        }
      } else {
        try {
          const response = await axios.post(`${API_URL}/api/sendMail`, {
            email: orderData.email,
            subject: "Đặt hàng thành công!",
            messageContent: "Cảm ơn bạn đã đặt hàng, đơn hàng sẽ sớm được giao trong 24h tới !",
          });
          console.log('Email sent successfully:', response.data);
          Swal.fire('Thành công', 'Đơn hàng của bạn đã được tạo!', 'success');
          router.push(`/payment/result?status=00&order_id=${createdOrderId}`);
        } catch (error) {
          console.error('Error sending email:', error);
        }
      }
    }
  } catch (error) {
    console.error('Error creating order:', error);
    Swal.fire('Lỗi', 'Có lỗi xảy ra khi tạo đơn hàng. Vui lòng thử lại.', 'error');
  }
};

onMounted(() => {
  fetchCart();
});
</script>
