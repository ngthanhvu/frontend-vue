<template>
    <div class="container mt-4" style="min-height: 100vh; padding-top: 100px; padding-bottom: 100px;">
        <h2>Thông tin cá nhân</h2>
        <form @submit.prevent="updateInfo" class="mb-4">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" class="form-control" v-model="info.email" required />
            </div>
            <div class="mb-3">
                <label for="username" class="form-label">Tên đăng nhập</label>
                <input type="text" id="username" class="form-control" v-model="info.username" required />
            </div>
            <button type="submit" class="btn btn-primary">Cập nhật thông tin</button>
        </form>

        <h2>Đổi mật khẩu</h2>
        <form @submit.prevent="updatePassword">
            <div class="mb-3">
                <label for="currentPassword" class="form-label">Mật khẩu hiện tại</label>
                <input type="password" id="currentPassword" class="form-control" v-model="passwordForm.currentPassword"
                    placeholder="Nhập mật khẩu hiện tại" />
            </div>
            <div class="mb-3">
                <label for="newPassword" class="form-label">Mật khẩu mới</label>
                <input type="password" id="newPassword" class="form-control" v-model="passwordForm.newPassword"
                    placeholder="Nhập mật khẩu mới" />
            </div>
            <div class="mb-3">
                <label for="confirmPassword" class="form-label">Nhập lại mật khẩu mới</label>
                <input type="password" id="confirmPassword" class="form-control" v-model="passwordForm.confirmPassword"
                    placeholder="Nhập lại mật khẩu mới" />
            </div>
            <button type="submit" class="btn btn-primary">Đổi mật khẩu</button>
        </form>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";

const API_URL = import.meta.env.VITE_API_URL;
// const user_id = localStorage.getItem("user_id");
const token = localStorage.getItem("token");
const passwordForm = ref({
    currentPassword: "",
    newPassword: "",
    confirmPassword: "",
});

const info = ref({
    email: "",
    username: "",
});

// Hàm lấy thông tin người dùng
const fetchUserInfo = async () => {
    try {
        const response = await axios.get(`${API_URL}/api/user`, {
            headers: {
                Authorization: `Bearer ${token}`,
            },
        });
        info.value = response.data;
    } catch (error) {
        console.error("Lỗi khi lấy thông tin:", error.response?.data || error.message);
    }
};

// Hàm cập nhật thông tin
const updateInfo = async () => {
    try {
        const response = await axios.put(
            `${API_URL}/api/user/update-info`,  // URL
            {  // Dữ liệu cần gửi đi
                email: info.value.email,
                username: info.value.username,
            },
            {  // Cấu hình headers
                headers: {
                    Authorization: `Bearer ${token}`,
                },
            }
        );
        alert("Cập nhật thông tin thành công!");
        console.log(response.data);
    } catch (error) {
        console.error("Lỗi khi cập nhật thông tin:", error.response?.data || error.message);
        alert("Cập nhật thất bại!");
    }
};

const updatePassword = async () => {
    if (passwordForm.value.newPassword !== passwordForm.value.confirmPassword) {
        alert("Mật khẩu mới không khớp!");
        return;
    }

    try {
        const response = await axios.put(
            `${API_URL}/api/user/update-password`,
            {
                current_password: passwordForm.value.currentPassword,
                new_password: passwordForm.value.newPassword,
                new_password_confirmation: passwordForm.value.confirmPassword,
            },
            {
                headers: {
                    Authorization: `Bearer ${token}`,
                },
            }
        );
        alert("Đổi mật khẩu thành công!");
        console.log(response.data);
    } catch (error) {
        console.error("Lỗi khi đổi mật khẩu:", error.response?.data || error.message);
        alert("Đổi mật khẩu thất bại!");
    }
};


onMounted(() => {
    fetchUserInfo();
});
</script>

<style scoped>
.container {
    max-width: 600px;
}
</style>