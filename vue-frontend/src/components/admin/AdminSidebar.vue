<template>
  <div id="layoutSidenav_nav" style="min-height: 100vh;">
    <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
      <div class="sb-sidenav-menu">
        <div class="nav">
          <div class="sb-sidenav-menu-heading">Core</div>
          <router-link class="nav-link" to="/admin">
            <div class="sb-nav-link-icon"><font-awesome-icon :icon="['fas', 'gauge-high']" /></div>
            Dashboard
          </router-link>
          <div class="sb-sidenav-menu-heading">Quản lý</div>
          <router-link class="nav-link" to="/admin/products">
            <div class="sb-nav-link-icon"><font-awesome-icon :icon="['fas', 'cart-shopping']" /></div>
            Products
          </router-link>
          <router-link class="nav-link" to="/admin/category">
            <div class="sb-nav-link-icon"><font-awesome-icon :icon="['fas', 'table-list']" /></div>
            Category
          </router-link>
          <router-link class="nav-link" to="/admin/orders">
            <div class="sb-nav-link-icon"><font-awesome-icon :icon="['fas', 'cart-shopping']" /></div>
            Orders
          </router-link>
        </div>
      </div>
    </nav>
  </div>
</template>
