import { createApp } from 'vue'
import { createPinia } from 'pinia'
import { createRouter, createWebHistory } from 'vue-router'

import App from './App.vue'
import router from './router'
import { useAuthStore } from './stores/auth'
import { useCartStore } from './stores/cart'
import { useProductsStore } from './stores/products'

import './assets/main.css'

const app = createApp(App)

app.use(createPinia())
app.use(router)

// Initialize auth on app startup
const authStoreInstance = useAuthStore()
authStoreInstance.initializeAuth()

app.mount('#app')
