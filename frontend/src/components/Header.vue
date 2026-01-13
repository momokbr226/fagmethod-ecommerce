<template>
  <header class="bg-white shadow-md sticky top-0 z-50">
    <!-- Top Bar -->
   

    <!-- Main Navigation -->
    <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-20">
        <!-- Logo -->
        <div class="flex-shrink-0">
          <router-link to="/" class="flex items-center">
            <div class="bg-green-700 px-3 py-2 rounded">
              <span class="text-yellow-400 text-2xl font-bold" style="font-family: Arial, sans-serif; letter-spacing: 2px;">FAGMETHOD</span>
            </div>
          </router-link>
        </div>

        <!-- Search Bar (Desktop) -->
        <div class="hidden md:flex flex-1 max-w-2xl mx-8">
          <div class="w-full">
            <div class="relative">
              <input
                type="text"
                placeholder="Rechercher des produits..."
                class="w-full px-4 py-2 pl-10 pr-4 text-gray-900 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-600 focus:border-transparent"
              >
              <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>
          </div>
        </div>

        <!-- Cart & Auth -->
        <div class="flex items-center space-x-2">
          <!-- Cart -->
          <router-link 
            to="/cart" 
            class="relative p-2 text-gray-700 hover:text-green-600 transition-colors"
          >
            <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <span 
              v-if="cartStore.cartItemCount > 0" 
              class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-semibold"
            >
              {{ cartStore.cartItemCount }}
            </span>
          </router-link>

          <!-- User Menu -->
          <div class="relative" v-if="!authStore.isLoggedIn">
            <router-link 
              to="/login" 
              class="text-gray-700 hover:text-green-600 px-3 py-2 rounded-md text-sm font-medium transition-colors"
            >
              Connexion
            </router-link>
            <router-link 
              to="/register" 
              class="bg-green-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-green-700 transition-colors"
            >
              Inscription
            </router-link>
          </div>

          <div class="relative" v-else>
            <button 
              @click="toggleUserMenu" 
              class="flex items-center space-x-2 text-gray-700 hover:text-green-600 px-3 py-2 rounded-md text-sm font-medium transition-colors"
            >
              <div class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center">
                <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
              </div>
              <span class="hidden lg:block">{{ authStore.currentUser?.nom_complet || authStore.currentUser?.name }}</span>
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>
            
            <!-- Dropdown Menu -->
            <div 
              v-if="userMenuOpen" 
              class="absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-xl py-2 ring-1 ring-black ring-opacity-5 z-50"
            >
              <!-- Dashboard selon le rôle -->
              <div v-if="authStore.isAdmin" class="px-4 py-2 bg-red-50 border-b border-red-100">
                <p class="text-xs font-semibold text-red-600 uppercase">Administrateur</p>
              </div>
              <router-link 
                v-if="authStore.isAdmin"
                to="/admin/dashboard" 
                class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-green-50 transition-colors"
                @click="userMenuOpen = false"
              >
                <svg class="h-5 w-5 mr-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                Dashboard Admin
              </router-link>

              <div v-if="authStore.isFournisseur" class="px-4 py-2 bg-purple-50 border-b border-purple-100">
                <p class="text-xs font-semibold text-purple-600 uppercase">Fournisseur</p>
              </div>
              <router-link 
                v-if="authStore.isFournisseur"
                to="/fournisseur/dashboard" 
                class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-green-50 transition-colors"
                @click="userMenuOpen = false"
              >
                <svg class="h-5 w-5 mr-3 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                Espace Fournisseur
              </router-link>

              <div v-if="authStore.isClient" class="px-4 py-2 bg-blue-50 border-b border-blue-100">
                <p class="text-xs font-semibold text-blue-600 uppercase">Client</p>
              </div>
              <router-link 
                v-if="authStore.isClient"
                to="/client/dashboard" 
                class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-green-50 transition-colors"
                @click="userMenuOpen = false"
              >
                <svg class="h-5 w-5 mr-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Mon Espace Client
              </router-link>

              <hr class="my-2">
              
              <router-link 
                to="/profile" 
                class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-green-50 transition-colors"
                @click="userMenuOpen = false"
              >
                <svg class="h-5 w-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Mon Profil
              </router-link>
              <router-link 
                to="/profile?tab=orders" 
                class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-green-50 transition-colors"
                @click="userMenuOpen = false"
              >
                <svg class="h-5 w-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                Mes Commandes
              </router-link>
              <hr class="my-2">
              <button 
                @click="handleLogout" 
                class="flex items-center w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors"
              >
                <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                Déconnexion
              </button>
            </div>
          </div>

          <!-- Mobile menu button -->
          <button 
            @click="toggleMobileMenu" 
            class="md:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-700 hover:text-green-600 hover:bg-gray-100 transition-colors"
          >
            <span class="sr-only">Menu</span>
            <svg v-if="!mobileMenuOpen" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
            <svg v-else class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Desktop Navigation Links -->
      <div class="hidden md:flex items-center justify-center space-x-8 pb-4 border-t border-gray-100 pt-4">
        <router-link 
          to="/" 
          class="text-gray-700 hover:text-indigo-600 px-3 py-2 text-sm font-medium transition-colors"
        >
          Accueil
        </router-link>
        <router-link 
          to="/products" 
          class="text-gray-700 hover:text-indigo-600 px-3 py-2 text-sm font-medium transition-colors"
        >
          Produits
        </router-link>
        <router-link 
          to="/categories" 
          class="text-gray-700 hover:text-indigo-600 px-3 py-2 text-sm font-medium transition-colors"
        >
          Catégories
        </router-link>
        <a href="#" class="text-gray-700 hover:text-indigo-600 px-3 py-2 text-sm font-medium transition-colors">
          Promotions
        </a>
        <a href="#" class="text-gray-700 hover:text-indigo-600 px-3 py-2 text-sm font-medium transition-colors">
          Nouveautés
        </a>
        <a href="#" class="text-gray-700 hover:text-indigo-600 px-3 py-2 text-sm font-medium transition-colors">
          Contact
        </a>
      </div>

      <!-- Mobile Menu -->
      <div v-if="mobileMenuOpen" class="md:hidden border-t border-gray-200">
        <div class="px-2 pt-2 pb-3 space-y-1">
          <router-link 
            to="/" 
            class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50 transition-colors"
            @click="mobileMenuOpen = false"
          >
            Accueil
          </router-link>
          <router-link 
            to="/products" 
            class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50 transition-colors"
            @click="mobileMenuOpen = false"
          >
            Produits
          </router-link>
          <router-link 
            to="/categories" 
            class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50 transition-colors"
            @click="mobileMenuOpen = false"
          >
            Catégories
          </router-link>
          <a href="#" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50 transition-colors">
            Promotions
          </a>
          <a href="#" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50 transition-colors">
            Nouveautés
          </a>
          <a href="#" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-indigo-600 hover:bg-gray-50 transition-colors">
            Contact
          </a>
        </div>
      </div>
    </nav>
  </header>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { useCartStore } from '../stores/cart'

const router = useRouter()
const authStore = useAuthStore()
const cartStore = useCartStore()

const userMenuOpen = ref(false)
const mobileMenuOpen = ref(false)

const toggleUserMenu = () => {
  userMenuOpen.value = !userMenuOpen.value
}

const toggleMobileMenu = () => {
  mobileMenuOpen.value = !mobileMenuOpen.value
}

const handleLogout = async () => {
  await authStore.logout()
  userMenuOpen.value = false
  router.push('/')
}

// Close menus when clicking outside
document.addEventListener('click', (e) => {
  if (!e.target.closest('.relative')) {
    userMenuOpen.value = false
    mobileMenuOpen.value = false
  }
})
</script>
