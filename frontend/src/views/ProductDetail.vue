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
                :src="product.image_principale || '/placeholder.png'" 
                :alt="product.nom"
                class="w-full h-full object-cover"
              >
            </div>
            
            <!-- Gallery Images -->
            <div v-if="product.images_supplementaires && product.images_supplementaires.length > 0" class="grid grid-cols-4 gap-2 mt-4">
              <img 
                v-for="(image, index) in product.images_supplementaires" 
                :key="index"
                :src="image" 
                :alt="`${product.nom} - Image ${index + 1}`"
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
                {{ product.categorie?.nom }}
              </span>
            </div>

            <!-- Title -->
            <h1 class="text-2xl font-bold text-gray-900 mb-2">
              {{ product.nom }}
            </h1>

            <!-- SKU -->
            <p class="text-sm text-gray-500 mb-4">
              Réf: {{ product.reference }}
            </p>

            <!-- Description -->
            <div class="mb-6">
              <h3 class="text-lg font-medium text-gray-900 mb-2">Description</h3>
              <div class="prose prose text-gray-600" v-html="product.description"></div>
            </div>

            <!-- Price -->
            <div class="mb-6">
              <div v-if="product.prix_compare && product.prix_compare > product.prix" class="flex items-center space-x-3">
                <span class="text-gray-400 line-through text-lg">
                  {{ product.prix_compare }} €
                </span>
                <span class="text-3xl font-bold text-red-600">
                  {{ product.prix }} €
                </span>
                <span class="bg-red-100 text-red-800 text-xs font-medium px-2 py-1 rounded">
                  -{{ Math.round((1 - product.prix / product.prix_compare) * 100) }}%
                </span>
              </div>
              <div v-else class="text-3xl font-bold text-gray-900">
                {{ product.prix }} €
              </div>
            </div>

            <!-- Stock Info -->
            <div class="mb-6">
              <div class="flex items-center">
                <template v-if="!product.est_en_rupture && product.quantite_stock > 0">
                  <svg 
                    class="w-5 h-5 text-green-500 mr-2" 
                    fill="currentColor" 
                    viewBox="0 0 20 20"
                  >
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                  </svg>
                  <span class="text-green-600 font-medium">
                    En stock ({{ product.quantite_stock }} unités)
                  </span>
                </template>
                <template v-else>
                  <svg 
                    class="w-5 h-5 text-red-500 mr-2" 
                    fill="currentColor" 
                    viewBox="0 0 20 20"
                  >
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                  </svg>
                  <span class="text-red-600 font-medium">Rupture de stock</span>
                </template>
              </div>
            </div>

            <!-- Attributes -->
            <div v-if="product.attributs && Object.keys(product.attributs).length > 0" class="mb-6">
              <h3 class="text-lg font-medium text-gray-900 mb-3">Caractéristiques</h3>
              <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div v-for="(value, key) in product.attributs" :key="key" class="bg-gray-50 px-4 py-3 rounded">
                  <dt class="text-sm font-medium text-gray-500 capitalize">{{ key }}</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ value }}</dd>
                </div>
              </dl>
            </div>

            <!-- Add to Cart Button -->
            <div class="mt-8">
              <button
                @click="addToCart"
                :disabled="product.est_en_rupture || product.quantite_stock <= 0 || loading"
                class="w-full bg-green-600 text-white px-6 py-3 rounded-md text-lg font-medium hover:bg-green-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors"
              >
                <svg v-if="loading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                {{ (product.est_en_rupture || product.quantite_stock <= 0) ? 'Indisponible' : 'Ajouter au panier' }}
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
  if (product.value.est_en_rupture || product.value.quantite_stock <= 0) return
  
  loading.value = true
  const result = await cartStore.addToCart({
    produit_id: product.value.id,
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

onMounted(async () => {
  const slug = route.params.slug
  if (slug) {
    const result = await productsStore.fetchProductBySlug(slug)
    if (result.success) {
      product.value = result.data
      
      // Fetch related products from same category
      if (product.value.categorie) {
        await productsStore.fetchProductsByCategory(product.value.categorie.slug, { limit: 4 })
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
