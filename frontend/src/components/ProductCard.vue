<template>
  <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden group">
    <!-- Product Image -->
    <div class="relative h-48 overflow-hidden">
      <img 
        :src="product.image_principale || '/placeholder.png'" 
        :alt="product.nom"
        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
      >
      <!-- Badge for discount -->
      <div 
        v-if="product.prix_compare && product.prix_compare > product.prix" 
        class="absolute top-2 right-2 bg-red-500 text-white px-2 py-1 rounded-md text-xs font-bold"
      >
        -{{ Math.round((1 - product.prix / product.prix_compare) * 100) }}%
      </div>
      <!-- Badge for out of stock -->
      <div 
        v-if="product.est_en_rupture || product.quantite_stock <= 0" 
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
          {{ product.categorie?.nom }}
        </span>
      </div>

      <!-- Product Name -->
      <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2">
        <router-link 
          :to="`/products/${product.slug}`"
          class="hover:text-indigo-600 transition-colors"
        >
          {{ product.nom }}
        </router-link>
      </h3>

      <!-- Price -->
      <div class="mb-3">
        <div v-if="product.prix_compare && product.prix_compare > product.prix" class="flex items-center space-x-2">
          <span class="text-gray-400 line-through text-sm">
            {{ product.prix_compare }} €
          </span>
          <span class="text-2xl font-bold text-red-600">
            {{ product.prix }} €
          </span>
        </div>
        <div v-else class="text-2xl font-bold text-gray-900">
          {{ product.prix }} €
        </div>
      </div>

      <!-- Stock Info -->
      <div class="mb-4">
        <div class="flex items-center text-sm">
          <template v-if="!product.est_en_rupture && product.quantite_stock > 0">
            <svg 
              class="w-4 h-4 text-green-500 mr-1" 
              fill="currentColor" 
              viewBox="0 0 20 20"
            >
              <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
            <span class="text-green-600">
              En stock ({{ product.quantite_stock }})
            </span>
          </template>
          <template v-else>
            <svg 
              class="w-4 h-4 text-red-500 mr-1" 
              fill="currentColor" 
              viewBox="0 0 20 20"
            >
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
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
          :disabled="product.est_en_rupture || product.quantite_stock <= 0 || loading"
          class="flex-1 bg-green-600 text-white px-4 py-2 rounded-md text-center font-medium hover:bg-green-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors"
        >
          <svg v-if="loading" class="animate-spin h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
          </svg>
          {{ (product.est_en_rupture || product.quantite_stock <= 0) ? 'Indisponible' : 'Ajouter' }}
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
  if (props.product.est_en_rupture || props.product.quantite_stock <= 0) return
  
  loading.value = true
  const result = await cartStore.addToCart({
    produit_id: props.product.id,
    quantite: 1,
    attributs_produit: {}
  })
  
  loading.value = false
  
  if (result.success) {
    console.log('Product added to cart:', result.message)
  } else {
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
