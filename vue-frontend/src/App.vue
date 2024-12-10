<script>
import Header from './components/Header.vue'
import Footer from './components/Footer.vue'
import AdminHeader from './components/admin/AdminHeader.vue';
import AdminFooter from './components/admin/AdminFooter.vue';
import AdminSidebar from './components/admin/AdminSidebar.vue';


export default {
  name: 'App',
  computed: {
    headerComponent() {
      return this.$route.path.includes('/admin') ? AdminHeader : Header
    },
    footerComponent() {
      return this.$route.path.includes('/admin') ? AdminFooter : Footer

    },
    sidebarComponent() {
      return this.$route.path.includes('/admin') ? AdminSidebar : Header
    }
  }
}
</script>

<template>
  <header>
    <div class="wrapper">
      <!-- <Navbar/> -->
      <component :is="headerComponent" />
    </div>
  </header>

  <main class="main">
    <RouterView />
  </main>
  <!-- <Footer /> -->
  <component :is="footerComponent" />
</template>

<style scoped>
</style>
