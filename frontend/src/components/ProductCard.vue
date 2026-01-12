<template>
  <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden group">
    <!-- Product Image -->
    <div class="relative h-48 overflow-hidden">
      <img 
        :src="product.image" 
        :alt="product.name"
        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
      >
      <!-- Badge for discount -->
      <div 
        v-if="product.has_discount" 
        class="absolute top-2 right-2 bg-red-500 text-white px-2 py-1 rounded-md text-xs font-bold"
      >
        -{{ product.discount_percentage }}%
      </div>
      <!-- Badge for out of stock -->
      <div 
        v-if="product.is_out_of_stock" 
        class="absolute inset-0 bg-black bg-opacity-75 flex items-center justify-center"
      >
        <span class="text-white font-bold">Rupture de stock</span>
      </div>
    </div>

    <!-- Product Info -->
    <div class="p-4">
      <!-- Category -->
      <div class="mb-2">
        <span class="text-xs text-gray-500 uppercase tracking-wide">
          {{ product.category?.name }}
        </span>
      </div>

      <!-- Product Name -->
      <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2">
        <router-link 
          :to="`/products/${product.slug}`"
          class="hover:text-indigo-600 transition-colors"
        >
          {{ product.name }}
        </router-link>
      </h3>

      <!-- Price -->
      <div class="mb-3">
        <div v-if="product.has_discount" class="flex items-center space-x-2">
          <span class="text-gray-400 line-through text-sm">
            {{ product.formatted_compare_price }}
          </span>
          <span class="text-2xl font-bold text-red-600">
            {{ product.formatted_price }}
          </span>
        </div>
        <div v-else class="text-2xl font-bold text-gray-900">
          {{ product.formatted_price }}
        </div>
      </div>

      <!-- Stock Info -->
      <div class="mb-4">
        <div class="flex items-center text-sm">
          <template v-if="!product.is_out_of_stock">
            <svg 
              class="w-4 h-4 text-green-500 mr-1" 
              fill="currentColor" 
              viewBox="0 0 20 20"
            >
              <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010-1.414l-8-8a1 1 0 00-1.414 0l-8 8a1 1 0 011.414 0l8-8a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
            <span class="text-green-600">
              En stock ({{ product.stock_quantity }})
            </span>
          </template>
          <template v-else>
            <svg 
              class="w-4 h-4 text-red-500 mr-1" 
              fill="currentColor" 
              viewBox="0 0 20 20"
            >
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-3 8a3 3 0 00-3 3v1a1 1 0 001 1h1a1 1 0 001 1v3a1 1 0 001 1h1a1 1 0 001 1v-3a3 3 0 00-3-3H6a3 3 0 00-3 3z" clip-rule="evenodd" />
            </svg>
            <span class="text-red-600">Rupture de stock</span>
          </template>
        </div>
      </div>

      <!-- Actions -->
      <div class="flex space-x-2">
        <router-link 
          :to="`/products/${product.slug}`"
          class="flex-1 bg-indigo-600 text-white px-4 py-2 rounded-md text-center font-medium hover:bg-indigo-700 transition-colors"
        >
          Voir les détails
        </router-link>
        <button 
          @click="addToCart"
          :disabled="product.is_out_of_stock || loading"
          class="flex-1 bg-green-600 text-white px-4 py-2 rounded-md text-center font-medium hover:bg-green-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors"
        >
          <svg v-if="loading" class="animate-spin h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V4a8 8 0 00-8 8v4a8 8 0 0018 8z"></path>
          </svg>
          {{ product.is_out_of_stock ? 'Indisponible' : 'Ajouter au panier' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useCartStore } from '../stores/cart'

const props = defineProps({
  product: {
    type: Object,
    required: true
  }
})

const cartStore = useCartStore()
const loading = ref(false)

const addToCart = async () => {
  if (props.product.is_out_of_stock) return
  
  loading.value = true
  const result = await cartStore.addToCart({
    product_id: props.product.id,
    quantity: 1,
    attributes: {}
  })
  
  loading.value = false
  
  if (result.success) {
    // Optional: Show success notification
    console.log('Product added to cart:', result.message)
  } else {
    // Optional: Show error notification
    console.error('Error adding to cart:', result.error)
  }
}
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
