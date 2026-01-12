<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0 md:space-x-4">
          <!-- Search -->
          <div class="flex-1">
            <div class="relative">
              <input
                v-model="searchQuery"
                @keyup.enter="handleSearch"
                type="text"
                placeholder="Rechercher des produits..."
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
              >
              <button 
                @click="handleSearch"
                class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700"
              >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
              </button>
            </div>
          </div>

          <!-- Filters -->
          <div class="flex flex-wrap gap-4">
            <!-- Category Filter -->
            <select 
              v-model="filters.category"
              @change="applyFilters"
              class="px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            >
              <option value="">Toutes les catégories</option>
              <option 
                v-for="category in productsStore.allCategories" 
                :key="category.id"
                :value="category.id"
              >
                {{ category.name }}
              </option>
            </select>

            <!-- Price Range -->
            <div class="flex items-center space-x-2">
              <input
                v-model="filters.minPrice"
                @change="applyFilters"
                type="number"
                placeholder="Prix min"
                class="w-24 px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
              >
              <span class="text-gray-500">-</span>
              <input
                v-model="filters.maxPrice"
                @change="applyFilters"
                type="number"
                placeholder="Prix max"
                class="w-24 px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
              >
            </div>

            <!-- Sort -->
            <select 
              v-model="filters.sortBy"
              @change="applyFilters"
              class="px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            >
              <option value="created_at">Plus récents</option>
              <option value="name">Nom (A-Z)</option>
              <option value="price">Prix croissant</option>
              <option value="price_desc">Prix décroissant</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- Products Grid -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
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

      <!-- Products -->
      <div v-else-if="productsStore.allProducts.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
        <ProductCard 
          v-for="product in productsStore.allProducts" 
          :key="product.id"
          :product="product"
        />
      </div>

      <!-- Empty State -->
      <div v-else class="text-center py-12">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 5.656l-6.828 6.829a4 4 0 00-5.656 0l6.829-6.828a4 4 0 001.656 5.656z" />
        </svg>
        <h3 class="mt-2 text-lg font-medium text-gray-900">Aucun produit trouvé</h3>
        <p class="mt-2 text-gray-500">
          Essayez de modifier vos filtres ou votre recherche.
        </p>
        <button 
          @click="clearFilters"
          class="mt-4 bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700"
        >
          Réinitialiser les filtres
        </button>
      </div>

      <!-- Pagination -->
      <div v-if="productsStore.pagination.lastPage > 1" class="mt-12 flex justify-center">
        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
          <button
            @click="changePage(productsStore.pagination.currentPage - 1)"
            :disabled="productsStore.pagination.currentPage === 1"
            class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50"
          >
            Précédent
          </button>
          
          <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
            Page {{ productsStore.pagination.currentPage }} sur {{ productsStore.pagination.lastPage }}
          </span>
          
          <button
            @click="changePage(productsStore.pagination.currentPage + 1)"
            :disabled="productsStore.pagination.currentPage === productsStore.pagination.lastPage"
            class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50"
          >
            Suivant
          </button>
        </nav>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useProductsStore } from '../stores/products'
import ProductCard from '../components/ProductCard.vue'

const route = useRoute()
const router = useRouter()
const productsStore = useProductsStore()

const searchQuery = ref('')
const searchTimeout = ref(null)

const filters = ref({
  category: '',
  minPrice: null,
  maxPrice: null,
  sortBy: 'created_at'
})

// Watch for route changes to handle category filtering
watch(() => route.params.categorySlug, async (newSlug) => {
  if (newSlug) {
    filters.value.category = newSlug
    await productsStore.fetchProductsByCategory(newSlug, {
      ...filters.value,
      category: undefined // Don't double apply category filter
    })
  }
}, { immediate: true })

const handleSearch = () => {
  clearTimeout(searchTimeout.value)
  searchTimeout.value = setTimeout(async () => {
    if (searchQuery.value.trim()) {
      await productsStore.searchProducts(searchQuery.value, filters.value)
    } else {
      await productsStore.fetchProducts(filters.value)
    }
  }, 500)
}

const applyFilters = async () => {
  const currentFilters = { ...filters.value }
  
  if (route.params.categorySlug) {
    await productsStore.fetchProductsByCategory(route.params.categorySlug, {
      ...currentFilters,
      category: undefined
    })
  } else {
    await productsStore.fetchProducts(currentFilters)
  }
}

const clearFilters = async () => {
  searchQuery.value = ''
  filters.value = {
    category: '',
    minPrice: null,
    maxPrice: null,
    sortBy: 'created_at'
  }
  
  productsStore.clearFilters()
  await productsStore.fetchProducts()
  
  if (route.params.categorySlug) {
    router.push('/products')
  }
}

const changePage = async (page) => {
  if (page >= 1 && page <= productsStore.pagination.lastPage) {
    const currentFilters = { ...filters.value }
    
    if (route.params.categorySlug) {
      await productsStore.fetchProductsByCategory(route.params.categorySlug, {
        ...currentFilters,
        page,
        category: undefined
      })
    } else if (searchQuery.value.trim()) {
      await productsStore.searchProducts(searchQuery.value, {
        ...currentFilters,
        page
      })
    } else {
      await productsStore.fetchProducts({
        ...currentFilters,
        page
      })
    }
  }
}

onMounted(async () => {
  // First fetch categories
  await productsStore.fetchCategories()
  
  // Then fetch products
  if (route.params.categorySlug) {
    filters.value.category = route.params.categorySlug
    await productsStore.fetchProductsByCategory(route.params.categorySlug, {
      ...filters.value,
      category: undefined
    })
  } else {
    await productsStore.fetchProducts(filters.value)
  }
})
</script>
