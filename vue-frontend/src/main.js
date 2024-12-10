
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import './assets/admin.css'

import { createApp } from 'vue'
import App from './App.vue'
import router from './router'

// Font Awesome
import { library } from '@fortawesome/fontawesome-svg-core';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

// Import tất cả các icon
import { fas } from '@fortawesome/free-solid-svg-icons';
import { far } from '@fortawesome/free-regular-svg-icons';
import { fab } from '@fortawesome/free-brands-svg-icons';

// Thêm các icon vào thư viện
library.add(fas, far, fab);

const app = createApp(App)

app.component('font-awesome-icon', FontAwesomeIcon);
app.use(router)
app.mount('#app')
