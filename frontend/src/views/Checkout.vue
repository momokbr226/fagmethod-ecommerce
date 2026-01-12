<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <h1 class="text-2xl font-bold text-gray-900">Finaliser la commande</h1>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Checkout Form -->
        <div class="lg:col-span-2">
          <!-- Shipping Address -->
          <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Adresse de livraison</h3>
              
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700">Sélectionner une adresse</label>
                  <select 
                    v-model="checkoutForm.shipping_address_id"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-gray-900"
                  >
                    <option value="">Choisir une adresse</option>
                    <option value="1">Adresse principale</option>
                  </select>
                </div>
                
                <button 
                  type="button"
                  class="text-indigo-600 hover:text-indigo-500 text-sm font-medium"
                >
                  + Ajouter une nouvelle adresse
                </button>
              </div>
            </div>
          </div>

          <!-- Billing Address -->
          <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Adresse de facturation</h3>
              
              <div class="space-y-4">
                <div>
                  <label class="flex items-center">
                    <input
                      type="checkbox"
                      v-model="sameAsShipping"
                      class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                    >
                    <span class="ml-2 text-sm text-gray-600">Même adresse que la livraison</span>
                  </label>
                </div>
                
                <div v-if="!sameAsShipping">
                  <select 
                    v-model="checkoutForm.billing_address_id"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-gray-900"
                  >
                    <option value="">Choisir une adresse</option>
                    <option value="1">Adresse principale</option>
                  </select>
                </div>
              </div>
            </div>
          </div>

          <!-- Payment Method -->
          <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Moyen de paiement</h3>
              
              <div class="space-y-3">
                <label class="flex items-center border border-gray-300 rounded-lg p-4 cursor-pointer hover:bg-gray-50">
                  <input
                    type="radio"
                    v-model="checkoutForm.payment_method"
                    value="credit_card"
                    class="mr-3"
                  >
                  <div>
                    <div class="font-medium text-gray-900">Carte de crédit</div>
                    <div class="text-sm text-gray-500">Visa, Mastercard, American Express</div>
                  </div>
                </label>
                
                <label class="flex items-center border border-gray-300 rounded-lg p-4 cursor-pointer hover:bg-gray-50">
                  <input
                    type="radio"
                    v-model="checkoutForm.payment_method"
                    value="paypal"
                    class="mr-3"
                  >
                  <div>
                    <div class="font-medium text-gray-900">PayPal</div>
                    <div class="text-sm text-gray-500">Paiement sécurisé via PayPal</div>
                  </div>
                </label>
                
                <label class="flex items-center border border-gray-300 rounded-lg p-4 cursor-pointer hover:bg-gray-50">
                  <input
                    type="radio"
                    v-model="checkoutForm.payment_method"
                    value="bank_transfer"
                    class="mr-3"
                  >
                  <div>
                    <div class="font-medium text-gray-900">Virement bancaire</div>
                    <div class="text-sm text-gray-500">Paiement par virement</div>
                  </div>
                </label>
              </div>
            </div>
          </div>

          <!-- Order Notes -->
          <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Notes de commande</h3>
              
              <textarea
                v-model="checkoutForm.notes"
                rows="4"
                placeholder="Instructions spéciales pour votre commande..."
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-gray-900"
              ></textarea>
            </div>
          </div>
        </div>

        <!-- Order Summary -->
        <div class="lg:col-span-1">
          <div class="bg-white shadow rounded-lg sticky top-6">
            <div class="px-4 py-5 sm:p-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Récapitulatif</h3>
              
              <!-- Cart Items -->
              <div class="space-y-4 mb-6">
                <div v-for="item in cartStore.cartItems" :key="item.id" class="flex justify-between">
                  <div class="flex-1">
                    <h4 class="text-sm font-medium text-gray-900">{{ item.produit?.nom }}</h4>
                    <p class="text-sm text-gray-500">Quantité: {{ item.quantite }}</p>
                  </div>
                  <div class="text-sm font-medium text-gray-900">
                    {{ item.sous_total }} €
                  </div>
                </div>
              </div>

              <!-- Order Total -->
              <div class="border-t border-gray-200 pt-4">
                <div class="flex justify-between text-sm text-gray-600 mb-2">
                  <span>Sous-total</span>
                  <span>{{ cartStore.cartTotal }}</span>
                </div>
                <div class="flex justify-between text-sm text-gray-600 mb-2">
                  <span>Livraison</span>
                  <span>Gratuite</span>
                </div>
                <div class="flex justify-between text-sm text-gray-600 mb-4">
                  <span>Taxes</span>
                  <span>0,00 €</span>
                </div>
                <div class="flex justify-between text-lg font-medium text-gray-900">
                  <span>Total</span>
                  <span>{{ cartStore.cartTotal }}</span>
                </div>
              </div>

              <!-- Place Order Button -->
              <button
                @click="placeOrder"
                :disabled="!isFormValid || loading"
                class="w-full bg-indigo-600 text-white px-4 py-3 rounded-md text-base font-medium hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors mt-6"
              >
                <svg v-if="loading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V4a8 8 0 00-8 8v4a8 8 0 0018 8z"></path>
                </svg>
                {{ loading ? 'Traitement...' : 'Confirmer la commande' }}
              </button>

              <!-- Terms -->
              <p class="mt-4 text-xs text-gray-500 text-center">
                En confirmant votre commande, vous acceptez nos 
                <router-link to="/terms" class="text-indigo-600 hover:text-indigo-500">
                  conditions générales de vente
                </router-link>
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useCartStore } from '../stores/cart'
import { useAuthStore } from '../stores/auth'
import { useOrdersStore } from '../stores/orders'

const router = useRouter()
const cartStore = useCartStore()
const authStore = useAuthStore()
const ordersStore = useOrdersStore()

const loading = ref(false)
const sameAsShipping = ref(true)

const checkoutForm = ref({
  shipping_address_id: '',
  billing_address_id: '',
  payment_method: 'credit_card',
  notes: ''
})

const isFormValid = computed(() => {
  return checkoutForm.value.shipping_address_id && 
         (sameAsShipping.value || checkoutForm.value.billing_address_id) &&
         checkoutForm.value.payment_method
})

const placeOrder = async () => {
  if (!isFormValid.value) return

  loading.value = true

  try {
    const orderData = {
      shipping_address_id: checkoutForm.value.shipping_address_id,
      billing_address_id: sameAsShipping.value ? checkoutForm.value.shipping_address_id : checkoutForm.value.billing_address_id,
      payment_method: checkoutForm.value.payment_method,
      notes: checkoutForm.value.notes
    }

    console.log('Creating order with data:', orderData)

    // Create order
    const result = await ordersStore.createOrder(orderData)
    
    if (result.success) {
      // Clear cart
      await cartStore.clearCart()
      
      // Show success message
      alert('Commande confirmée avec succès! Numéro de commande: ' + (result.data.data?.order_number || 'N/A'))
      
      // Redirect to profile to see orders
      router.push('/profile')
    } else {
      alert('Erreur lors de la création de la commande: ' + result.error)
    }
    
  } catch (error) {
    console.error('Order creation error:', error)
    alert('Erreur lors de la création de la commande')
  } finally {
    loading.value = false
  }
}

onMounted(async () => {
  // Check if user is authenticated
  if (!authStore.isLoggedIn) {
    router.push('/login')
    return
  }

  // Fetch cart data
  await cartStore.fetchCart()
  
  // Check if cart is empty
  if (cartStore.cartItems.length === 0) {
    router.push('/cart')
    return
  }
})
</script>
