<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <h1 class="text-2xl font-bold text-gray-900">Mon Panier</h1>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Loading State -->
      <div v-if="cartStore.loading" class="text-center py-12">
        <div class="inline-flex items-center">
          <svg class="animate-spin h-8 w-8 text-indigo-600" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V4a8 8 0 00-8 8v4a8 8 0 0018 8z"></path>
          </svg>
          <span class="ml-2 text-lg text-gray-600">Chargement...</span>
        </div>
      </div>

      <!-- Empty Cart -->
      <div v-else-if="cartStore.cartItems.length === 0" class="text-center py-12">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24">
          <path d="M3 3h2l.4 2L7 13l2 2 9-5-2-2-2m4 0h16a2 2 0 002-2v-4a2 2 0 00-2-2H6a2 2 0 00-2 2v4a2 2 0 002 2h2m4 0h16a2 2 0 002-2v-4a2 2 0 00-2-2H6a2 2 0 00-2 2v4a2 2 0 002 2h2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
        <h3 class="mt-2 text-lg font-medium text-gray-900">Votre panier est vide</h3>
        <p class="mt-2 text-gray-500">
          Ajoutez des produits à votre panier pour continuer vos achats.
        </p>
        <router-link 
          to="/products" 
          class="mt-4 inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700"
        >
          Continuer mes achats
          <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4-4m4 4H3" />
          </svg>
        </router-link>
      </div>

      <!-- Cart Items -->
      <div v-else>
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
          <div class="px-4 py-5 sm:px-6">
            <h3 class="text-lg font-medium text-gray-900">
              Articles ({{ cartStore.cartItemCount }})
            </h3>
          </div>
          
          <!-- Cart Items List -->
          <ul class="divide-y divide-gray-200">
            <li v-for="item in cartStore.cartItems" :key="item.id" class="py-6 flex">
              <!-- Product Image -->
              <div class="flex-shrink-0 w-24 h-24 border border-gray-200 rounded-md overflow-hidden">
                <img 
                  :src="item.produit?.image_principale || '/placeholder.png'" 
                  :alt="item.produit?.nom"
                  class="w-full h-full object-cover"
                >
              </div>

              <!-- Product Details -->
              <div class="ml-4 flex-1 flex flex-col">
                <div>
                  <h4 class="text-lg font-medium text-gray-900">
                    {{ item.produit?.nom }}
                  </h4>
                  <p class="mt-1 text-sm text-gray-500">
                    Réf: {{ item.produit?.reference }}
                  </p>
                  <!-- Product Attributes -->
                  <div v-if="item.attributs_produit && Object.keys(item.attributs_produit).length > 0" class="mt-2 text-sm text-gray-600">
                    <span v-for="(value, key) in item.attributs_produit" :key="key" class="mr-2">
                      {{ key }}: {{ value }}
                    </span>
                  </div>
                </div>

                <!-- Price and Quantity -->
                <div class="flex-1 flex items-end justify-between mt-4">
                  <div class="text-lg font-medium text-gray-900">
                    {{ item.prix_unitaire }} € × {{ item.quantite }}
                  </div>
                  <div class="ml-4 flex items-center space-x-2">
                    <!-- Quantity Controls -->
                    <div class="flex items-center border border-gray-300 rounded-md">
                      <button 
                        @click="updateQuantity(item.id, item.quantite - 1)"
                        :disabled="item.quantite <= 1 || updating"
                        class="px-3 py-1 text-gray-600 hover:text-gray-900 disabled:opacity-50"
                      >
                        -
                      </button>
                      <input
                        :value="item.quantite"
                        @change="updateQuantity(item.id, $event.target.value)"
                        type="number"
                        min="1"
                        class="w-16 px-2 py-1 text-center border-0 focus:ring-2 focus:ring-indigo-500 text-gray-900"
                      >
                      <button 
                        @click="updateQuantity(item.id, item.quantite + 1)"
                        :disabled="updating"
                        class="px-3 py-1 text-gray-600 hover:text-gray-900 disabled:opacity-50"
                      >
                        +
                      </button>
                    </div>

                    <!-- Remove Button -->
                    <button 
                      @click="removeFromCart(item.id)"
                      :disabled="updating"
                      class="text-red-600 hover:text-red-800 disabled:opacity-50"
                    >
                      <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0114 14.414l-4 4a2 2 0 01-2.828 0L7 10.586a2 2 0 00-2.828 0L2.172 5.828A2 2 0 01.414 4l4 4a2 2 0 012.828 0L17 7z" stroke="currentColor" />
                      </svg>
                    </button>
                  </div>
                </div>

                <!-- Item Total -->
                <div class="mt-2 text-right">
                  <p class="text-lg font-medium text-gray-900">
                    Total: {{ item.sous_total }} €
                  </p>
                </div>
              </div>
            </li>
          </ul>

          <!-- Cart Summary -->
          <div class="border-t border-gray-200 py-6 px-4 sm:px-6">
            <div class="flex justify-between text-base font-medium text-gray-900">
              <p>Sous-total</p>
              <p>{{ cartStore.cartTotal }}</p>
            </div>
            <div class="mt-2">
              <router-link 
                to="/checkout" 
                class="w-full flex justify-center items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700"
              >
                Procéder au paiement
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useCartStore } from '../stores/cart'

const cartStore = useCartStore()
const updating = ref(false)

const updateQuantity = async (itemId, quantity) => {
  if (quantity < 1) return
  
  updating.value = true
  const result = await cartStore.updateCartItem(itemId, quantity)
  updating.value = false
  
  if (!result.success) {
    // Optional: Show error notification
    console.error('Error updating cart:', result.error)
  }
}

const removeFromCart = async (itemId) => {
  if (!confirm('Êtes-vous sûr de vouloir supprimer cet article ?')) return
  
  updating.value = true
  const result = await cartStore.removeFromCart(itemId)
  updating.value = false
  
  if (result.success) {
    // Optional: Show success notification
    console.log('Item removed from cart:', result.message)
  } else {
    // Optional: Show error notification
    console.error('Error removing from cart:', result.error)
  }
}

onMounted(async () => {
  await cartStore.fetchCart()
})
</script>
