<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-green-600 to-emerald-600 text-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
        <div class="text-center">
          <h1 class="text-4xl font-extrabold tracking-tight sm:text-5xl md:text-6xl">
            FAGMETHOD
          </h1>
          <p class="mt-6 max-w-2xl mx-auto text-xl text-green-100">
            Votre spécialiste en matériel informatique professionnel
          </p>
          <div class="mt-10 flex justify-center space-x-4">
            <router-link 
              to="/products" 
              class="bg-white text-green-600 px-8 py-3 rounded-md text-base font-medium hover:bg-green-50 transition-colors"
            >
              Découvrir nos produits
            </router-link>
            <router-link 
              to="/categories" 
              class="bg-transparent border-2 border-white text-white px-8 py-3 rounded-md text-base font-medium hover:bg-white hover:text-green-600 transition-colors"
            >
              Parcourir les catégories
            </router-link>
          </div>
        </div>
      </div>
    </section>

    <!-- Featured Products -->
    <section class="py-16 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <h2 class="text-3xl font-extrabold text-gray-900">
            Produits Vedettes
          </h2>
          <p class="mt-4 max-w-2xl mx-auto text-xl text-gray-500">
            Découvrez notre sélection de produits informatiques de haute qualité
          </p>
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

        <!-- Featured Products Grid -->
        <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
          <ProductCard 
            v-for="product in productsStore.featuredProducts" 
            :key="product.id"
            :product="product"
          />
        </div>

        <!-- View All Products -->
        <div v-if="!productsStore.isLoading && productsStore.featuredProducts.length > 0" class="text-center mt-12">
          <router-link 
            to="/products" 
            class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 transition-colors"
          >
            Voir tous les produits
            <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4-4m4 4H3" />
            </svg>
          </router-link>
        </div>
      </div>
    </section>

    <!-- Categories Preview -->
    <section class="py-16 bg-gray-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <h2 class="text-3xl font-extrabold text-gray-900">
            Nos Catégories
          </h2>
          <p class="mt-4 max-w-2xl mx-auto text-xl text-gray-500">
            Explorez notre large gamme de produits informatiques
          </p>
        </div>

        <!-- Categories Grid -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
          <router-link 
            v-for="category in productsStore.allCategories.slice(0, 10)" 
            :key="category.id"
            :to="`/categories/${category.slug}`"
            class="group block p-6 bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300"
          >
            <div class="text-center">
              <img 
                v-if="category.image"
                :src="category.image" 
                :alt="category.name"
                class="w-16 h-16 mx-auto mb-4 object-cover group-hover:scale-110 transition-transform duration-300"
              >
              <div v-else class="w-16 h-16 mx-auto mb-4 bg-indigo-100 rounded-lg flex items-center justify-center">
                <svg class="w-8 h-8 text-indigo-600" fill="none" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2zm-7 7a3 3 0 00-3 3v1a1 1 0 001 1h1a1 1 0 001 1v3a1 1 0 001 1h1a1 1 0 001 1v-3a3 3 0 00-3-3H6a3 3 0 00-3 3z" />
                </svg>
              </div>
              <h3 class="mt-4 text-lg font-semibold text-gray-900 group-hover:text-indigo-600 transition-colors">
                {{ category.name }}
              </h3>
              <p class="mt-2 text-sm text-gray-500">
                {{ category.description }}
              </p>
            </div>
          </router-link>
        </div>

        <!-- View All Categories -->
        <div class="text-center mt-12">
          <router-link 
            to="/categories" 
            class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 transition-colors"
          >
            Voir toutes les catégories
            <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4-4m4 4H3" />
            </svg>
          </router-link>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useProductsStore } from '../stores/products'
import ProductCard from '../components/ProductCard.vue'

const productsStore = useProductsStore()

onMounted(async () => {
  // Fetch featured products and categories on component mount
  await Promise.all([
    productsStore.fetchFeaturedProducts(8),
    productsStore.fetchCategories()
  ])
})
</script>
