<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Loading State -->
    <div v-if="productsStore.isLoading" class="text-center py-12">
      <div class="inline-flex items-center">
        <svg class="animate-spin h-8 w-8 text-indigo-600" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V4a8 8 0 00-8 8v4a8 8 0 0018 8z"></path>
        </svg>
        <span class="ml-2 text-lg text-gray-600">Chargement...</span>
      </div>
    </div>

    <!-- Product Detail -->
    <div v-else-if="product" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="lg:grid lg:grid-cols-2 lg:gap-x-8">
        <!-- Product Images -->
        <div class="lg:col-span-1">
          <div class="space-y-4">
            <!-- Main Image -->
            <div class="aspect-w-1 aspect-h-1 bg-gray-200 rounded-lg overflow-hidden">
              <img 
                :src="product.image" 
                :alt="product.name"
                class="w-full h-full object-cover"
              >
            </div>
            
            <!-- Gallery Images -->
            <div v-if="product.images && product.images.length > 0" class="grid grid-cols-4 gap-2 mt-4">
              <img 
                v-for="(image, index) in product.images" 
                :key="index"
                :src="image" 
                :alt="`${product.name} - Image ${index + 1}`"
                class="w-full h-24 object-cover rounded cursor-pointer hover:opacity-75"
                @click="selectedImage = image"
              >
            </div>
            </div>
        </div>

        <!-- Product Info -->
        <div class="lg:col-span-1">
          <div class="bg-white rounded-lg shadow-lg p-6">
            <!-- Category -->
            <div class="mb-4">
              <span class="text-sm text-gray-500 uppercase tracking-wide">
                {{ product.category?.name }}
              </span>
            </div>

            <!-- Title -->
            <h1 class="text-2xl font-bold text-gray-900 mb-2">
              {{ product.name }}
            </h1>

            <!-- SKU -->
            <p class="text-sm text-gray-500 mb-4">
              SKU: {{ product.sku }}
            </p>

            <!-- Description -->
            <div class="mb-6">
              <h3 class="text-lg font-medium text-gray-900 mb-2">Description</h3>
              <div class="prose prose text-gray-600" v-html="product.description"></div>
            </div>

            <!-- Price -->
            <div class="mb-6">
              <div v-if="product.has_discount" class="flex items-center space-x-3">
                <span class="text-gray-400 line-through text-lg">
                  {{ product.formatted_compare_price }}
                </span>
                <span class="text-3xl font-bold text-red-600">
                  {{ product.formatted_price }}
                </span>
                <span class="bg-red-100 text-red-800 text-xs font-medium px-2 py-1 rounded">
                  -{{ product.discount_percentage }}%
                </span>
              </div>
              <div v-else class="text-3xl font-bold text-gray-900">
                {{ product.formatted_price }}
              </div>
            </div>

            <!-- Stock Info -->
            <div class="mb-6">
              <div class="flex items-center">
                <template v-if="!product.is_out_of_stock">
                  <svg 
                    class="w-5 h-5 text-green-500 mr-2" 
                    fill="currentColor" 
                    viewBox="0 0 20 20"
                  >
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010-1.414l-8-8a1 1 0 00-1.414 0l-8 8a1 1 0 011.414 0l8-8a1 1 0 011.414 0z" clip-rule="evenodd" />
                  </svg>
                  <span class="text-green-600 font-medium">
                    En stock ({{ product.stock_quantity }} unités)
                  </span>
                </template>
                <template v-else>
                  <svg 
                    class="w-5 h-5 text-red-500 mr-2" 
                    fill="currentColor" 
                    viewBox="0 0 20 20"
                  >
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-3 8a3 3 0 00-3 3v1a1 1 0 001 1h1a1 1 0 001 1v3a1 1 0 001 1h1a1 1 0 001 1v-3a3 3 0 00-3-3H6a3 3 0 00-3 3z" clip-rule="evenodd" />
                  </svg>
                  <span class="text-red-600 font-medium">Rupture de stock</span>
                </template>
              </div>
            </div>

            <!-- Attributes -->
            <div v-if="product.attributes && Object.keys(product.attributes).length > 0" class="mb-6">
              <h3 class="text-lg font-medium text-gray-900 mb-3">Caractéristiques</h3>
              <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div v-for="(value, key) in product.attributes" :key="key" class="bg-gray-50 px-4 py-3 rounded">
                  <dt class="text-sm font-medium text-gray-500 capitalize">{{ key }}</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ value }}</dd>
                </div>
              </dl>
            </div>

            <!-- Add to Cart Button -->
            <div class="mt-8">
              <button
                @click="addToCart"
                :disabled="product.is_out_of_stock || loading"
                class="w-full bg-green-600 text-white px-6 py-3 rounded-md text-lg font-medium hover:bg-green-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors"
              >
                <svg v-if="loading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V4a8 8 0 00-8 8v4a8 8 0 0018 8z"></path>
                </svg>
                {{ product.is_out_of_stock ? 'Indisponible' : 'Ajouter au panier' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Product Recommendations -->
    <div v-if="relatedProducts.length > 0" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
      <h2 class="text-2xl font-bold text-gray-900 mb-8 text-center">
        Produits Similaires
      </h2>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
        <ProductCard 
          v-for="relatedProduct in relatedProducts" 
          :key="relatedProduct.id"
          :product="relatedProduct"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useProductsStore } from '../stores/products'
import { useCartStore } from '../stores/cart'
import ProductCard from '../components/ProductCard.vue'

const route = useRoute()
const productsStore = useProductsStore()
const cartStore = useCartStore()

const product = ref(null)
const selectedImage = ref(null)
const loading = ref(false)
const relatedProducts = ref([])

const addToCart = async () => {
  if (product.value.is_out_of_stock) return
  
  loading.value = true
  const result = await cartStore.addToCart({
    produit_id: product.value.id,
    quantite: 1,
    attributs_produit: {}
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

onMounted(async () => {
  const slug = route.params.slug
  if (slug) {
    const result = await productsStore.fetchProductBySlug(slug)
    if (result.success) {
      product.value = result.data
      
      // Fetch related products from same category
      if (product.value.category) {
        await productsStore.fetchProductsByCategory(product.value.category.slug, { limit: 4 })
        relatedProducts.value = productsStore.allProducts.filter(p => p.id !== product.value.id).slice(0, 4)
      }
    }
  }
})
</script>

<style scoped>
.prose {
  max-width: none;
}
.prose p {
  margin-bottom: 1rem;
}
</style>
