import './bootstrap';
import { createApp } from 'vue';
import app from './components/app.vue';
import router from './router/index.js';
import './components/axios.js'
import axios from 'axios';

createApp(app).use(router).mount("#app")
