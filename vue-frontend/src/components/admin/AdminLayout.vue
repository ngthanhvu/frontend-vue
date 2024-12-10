<template>
  <div id="layoutSidenav">
    <Sidebar />
    <router-view />
  </div>
</template>

<script setup>
import Sidebar from './AdminSidebar.vue';
</script>
