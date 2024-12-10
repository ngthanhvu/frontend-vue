import { createWebHistory, createRouter } from "vue-router";

import Home from "../components/Home.vue";
import Detail from "@/components/Detail.vue";
import Product from "@/components/ProductList.vue";
import Result from "@/components/Result.vue";
import Profile from "@/components/Profile.vue";
//auth
import Login from "@/components/Login.vue";
import Register from "@/components/Register.vue";
import ForgotPassword from "@/components/ForgotPass.vue";
import ConfimPass from "@/components/ConfimPass.vue";
import Cart from "@/components/Cart.vue";
import History from "@/components/History.vue";
import Checkout from "@/components/Checkout.vue";
//admin
import AdminLayout from "@/components/admin/AdminLayout.vue";
import Dashboard from "@/components/admin/Dashboard.vue";

// products
import ProductView from "@/components/admin/products/view.vue";
import ProductAdd from '@/components/admin/products/add.vue';

// category
import CategoryView from '@/components/admin/category/view.vue';
import CategoryAdd from '@/components/admin/category/add.vue';

// orders
import OrderView from '@/components/admin/orders/view.vue';

const routes = [
  {
    path: "/",
    name: "home",
    component: Home,
  },
  {
    path: "/detail/:id",
    name: "detail",
    component: Detail
  },
  {
    path: "/products",
    name: "product",
    component: Product
  },
  {
    path: "/cart",
    name: "cart",
    component: Cart
  },
  {
    path: "/history",
    name: "history",
    component: History
  },
  {
    path: "/checkout",
    name: "checkout",
    component: Checkout
  },
  {
    path: "/payment/result",
    name: "result",
    component: Result
  },
  {
    path: "/profile",
    name: "profile",
    component: Profile
  },
  {
    path: "/admin",
    name: "admin",
    component: AdminLayout,
    children: [
      {
        path: "",
        name: "dashboard",
        component: Dashboard
      },
      {
        path: "/admin/products",
        name: "products",
        component: ProductView
      },
      {
        path: "/admin/products/add",
        name: "product-add",
        component: ProductAdd
      },
      {
        path: "/admin/category",
        name: "category",
        component: CategoryView
      },
      {
        path: "/admin/category/add",
        name: "category-add",
        component: CategoryAdd
      },
      {
        path: "/admin/orders",
        name: "orders",
        component: OrderView
      }
    ]
  },
  {
    path: "/login",
    name: "login",
    component: Login
  },
  {
    path: "/register",
    name: "register",
    component: Register
  },
  {
    path: "/forgot-password",
    name: "forgot-password",
    component: ForgotPassword
  },
  {
    path: "/confirm-password",
    name: "confirm-password",
    component: ConfimPass
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
