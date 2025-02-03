import { createApp } from 'vue'
import App from './App.vue'
import router from './router/index.js' // ✅ Import router
import './index.css'

const app = createApp(App) // ✅ Create app instance
app.use(router) // ✅ Register Vue Router
app.mount('#app') // ✅ Mount the app
