<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <h1 class="text-2xl font-bold text-gray-900">Nos Catégories</h1>
        <p class="mt-2 text-gray-600">
          Explorez notre large gamme de produits informatiques organisés par catégorie.
        </p>
      </div>
    </div>

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

    <!-- Categories Grid -->
    <div v-else class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <!-- Category Card -->
        <router-link 
          v-for="category in productsStore.allCategories" 
          :key="category.id"
          :to="`/categories/${category.slug}`"
          class="group block p-6 bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300"
        >
          <div class="text-center">
            <!-- Category Image -->
            <div class="w-20 h-20 mx-auto mb-4 rounded-lg flex items-center justify-center overflow-hidden" :style="{ backgroundColor: category.couleur || '#4F46E5' }">
              <img 
                v-if="category.image"
                :src="category.image" 
                :alt="category.nom"
                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
              >
              <div v-else class="w-12 h-12">
                <svg class="w-full h-full text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
              </div>
            </div>

            <!-- Category Info -->
            <h3 class="mt-4 text-lg font-semibold text-gray-900 group-hover:text-indigo-600 transition-colors">
              {{ category.nom }}
            </h3>
            <p class="mt-2 text-sm text-gray-500 line-clamp-3">
              {{ category.description }}
            </p>

            <!-- Product Count -->
            <div v-if="category.produits_count" class="mt-4">
              <span class="text-sm text-indigo-600 font-medium">
                {{ category.produits_count }} produit{{ category.produits_count > 1 ? 's' : '' }}
              </span>
            </div>
          </div>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useProductsStore } from '../stores/products'

const productsStore = useProductsStore()

onMounted(async () => {
  await productsStore.fetchCategories()
})
</script>

<style scoped>
.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
