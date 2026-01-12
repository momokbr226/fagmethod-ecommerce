<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <h1 class="text-2xl font-bold text-gray-900">Mon Profil</h1>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Profile Menu -->
        <div class="lg:col-span-1">
          <div class="bg-white shadow rounded-lg">
            <div class="p-6">
              <h2 class="text-lg font-medium text-gray-900 mb-4">Menu</h2>
              <nav class="space-y-1">
                <button 
                  @click="activeTab = 'profile'"
                  :class="activeTab === 'profile' ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:text-gray-900'"
                  class="w-full text-left border-l-4 py-2 pl-3 pr-4 text-sm font-medium"
                >
                  Informations personnelles
                </button>
                <button 
                  @click="activeTab = 'orders'"
                  :class="activeTab === 'orders' ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:text-gray-900'"
                  class="w-full text-left border-l-4 py-2 pl-3 pr-4 text-sm font-medium"
                >
                  Mes commandes
                </button>
                <button 
                  @click="activeTab = 'addresses'"
                  :class="activeTab === 'addresses' ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:text-gray-900'"
                  class="w-full text-left border-l-4 py-2 pl-3 pr-4 text-sm font-medium"
                >
                  Adresses
                </button>
                <button 
                  @click="activeTab = 'security'"
                  :class="activeTab === 'security' ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:text-gray-900'"
                  class="w-full text-left border-l-4 py-2 pl-3 pr-4 text-sm font-medium"
                >
                  Sécurité
                </button>
              </nav>
            </div>
          </div>
        </div>

        <!-- Profile Content -->
        <div class="lg:col-span-2">
          <!-- Personal Information -->
          <div v-if="activeTab === 'profile'" class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Informations personnelles</h3>
              
              <form @submit.prevent="updateProfile" class="space-y-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                  <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">
                      Nom complet
                    </label>
                    <input
                      id="name"
                      v-model="profileForm.name"
                      type="text"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-gray-900"
                    >
                  </div>
                  <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">
                      Adresse email
                    </label>
                    <input
                      id="email"
                      v-model="profileForm.email"
                      type="email"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-gray-900"
                    >
                  </div>
                </div>

                <div class="flex justify-end">
                  <button
                    type="submit"
                    :disabled="loading"
                    class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 disabled:opacity-50"
                  >
                    <svg v-if="loading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V4a8 8 0 00-8 8v4a8 8 0 0018 8z"></path>
                    </svg>
                    {{ loading ? 'Mise à jour...' : 'Mettre à jour' }}
                  </button>
                </div>
              </form>
            </div>
          </div>

          <!-- Orders -->
          <div v-if="activeTab === 'orders'" class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Mes commandes</h3>
              
              <div v-if="ordersStore.isLoading" class="text-center py-12">
                <div class="inline-flex items-center">
                  <svg class="animate-spin h-8 w-8 text-indigo-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V4a8 8 0 00-8 8v4a8 8 0 0018 8z"></path>
                  </svg>
                  <span class="ml-2 text-lg text-gray-600">Chargement...</span>
                </div>
              </div>

              <div v-else-if="ordersStore.allOrders.length === 0" class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <h3 class="mt-2 text-lg font-medium text-gray-900">Aucune commande</h3>
                <p class="mt-2 text-gray-500">Vous n'avez pas encore passé de commande.</p>
                <router-link 
                  to="/products" 
                  class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700"
                >
                  Commencer mes achats
                </router-link>
              </div>

              <div v-else class="space-y-4">
                <div v-for="order in ordersStore.allOrders" :key="order.id" class="border border-gray-200 rounded-lg p-4">
                  <div class="flex justify-between items-start">
                    <div>
                      <h4 class="text-lg font-medium text-gray-900">{{ order.order_number }}</h4>
                      <p class="text-sm text-gray-500">{{ order.created_at }}</p>
                      <p class="text-sm font-medium text-gray-900">{{ order.formatted_total_amount }}</p>
                    </div>
                    <div class="text-right">
                      <span 
                        :class="getStatusClass(order.status)"
                        class="px-2 py-1 text-xs font-medium rounded-full"
                      >
                        {{ order.status_label }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Addresses -->
          <div v-if="activeTab === 'addresses'" class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Mes adresses</h3>
              <p class="text-gray-600">Gestion des adresses de livraison et de facturation.</p>
            </div>
          </div>

          <!-- Security -->
          <div v-if="activeTab === 'security'" class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Sécurité</h3>
              
              <form @submit.prevent="changePassword" class="space-y-6">
                <div>
                  <label for="current_password" class="block text-sm font-medium text-gray-700">
                    Mot de passe actuel
                  </label>
                  <input
                    id="current_password"
                    v-model="profileForm.current_password"
                    type="password"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-gray-900"
                  >
                </div>

                <div>
                  <label for="new_password" class="block text-sm font-medium text-gray-700">
                    Nouveau mot de passe
                  </label>
                  <input
                    id="new_password"
                    v-model="profileForm.new_password"
                    type="password"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-gray-900"
                  >
                </div>

                <div>
                  <label for="confirm_password" class="block text-sm font-medium text-gray-700">
                    Confirmer le nouveau mot de passe
                  </label>
                  <input
                    id="confirm_password"
                    v-model="profileForm.confirm_password"
                    type="password"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-gray-900"
                  >
                </div>

                <div class="flex justify-end">
                  <button
                    type="submit"
                    :disabled="loading"
                    class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 disabled:opacity-50"
                  >
                    <svg v-if="loading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V4a8 8 0 00-8 8v4a8 8 0 0018 8z"></path>
                    </svg>
                    {{ loading ? 'Mise à jour...' : 'Changer le mot de passe' }}
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '../stores/auth'
import { useOrdersStore } from '../stores/orders'

const authStore = useAuthStore()
const ordersStore = useOrdersStore()

const activeTab = ref('profile')
const loading = ref(false)

const profileForm = ref({
  name: '',
  email: '',
  current_password: '',
  new_password: '',
  confirm_password: ''
})

const getStatusClass = (status) => {
  const statusClasses = {
    'pending': 'bg-yellow-100 text-yellow-800',
    'processing': 'bg-blue-100 text-blue-800',
    'completed': 'bg-green-100 text-green-800',
    'cancelled': 'bg-red-100 text-red-800'
  }
  return statusClasses[status] || 'bg-gray-100 text-gray-800'
}

const updateProfile = async () => {
  loading.value = true
  
  try {
    // TODO: Implement profile update API call
    await new Promise(resolve => setTimeout(resolve, 1000))
    
    alert('Profil mis à jour avec succès!')
  } catch (error) {
    console.error('Profile update error:', error)
    alert('Erreur lors de la mise à jour du profil')
  } finally {
    loading.value = false
  }
}

const changePassword = async () => {
  if (profileForm.value.new_password !== profileForm.value.confirm_password) {
    alert('Les mots de passe ne correspondent pas')
    return
  }
  
  loading.value = true
  
  try {
    // TODO: Implement password change API call
    await new Promise(resolve => setTimeout(resolve, 1000))
    
    alert('Mot de passe changé avec succès!')
    
    // Clear password fields
    profileForm.value.current_password = ''
    profileForm.value.new_password = ''
    profileForm.value.confirm_password = ''
  } catch (error) {
    console.error('Password change error:', error)
    alert('Erreur lors du changement de mot de passe')
  } finally {
    loading.value = false
  }
}

const fetchOrders = async () => {
  console.log('=== Fetching orders ===')
  await ordersStore.fetchOrders()
}

onMounted(() => {
  // Set profile form data
  if (authStore.currentUser) {
    profileForm.value.name = authStore.currentUser.name
    profileForm.value.email = authStore.currentUser.email
  }
  
  // Fetch orders
  fetchOrders()
})
</script>
