import { defineStore } from 'pinia'
import axios from 'axios'

export const useProductsStore = defineStore('products', {
  state: () => ({
    products: [],
    categories: [],
    featuredProducts: [],
    currentProduct: null,
    loading: false,
    pagination: {
      currentPage: 1,
      lastPage: 1,
      perPage: 12,
      total: 0
    },
    filters: {
      search: '',
      category: null,
      minPrice: null,
      maxPrice: null,
      sortBy: 'created_at',
      sortOrder: 'desc'
    }
  }),

  getters: {
    allProducts: (state) => state.products,
    allCategories: (state) => state.categories,
    featuredProductsList: (state) => state.featuredProducts,
    productById: (state) => (id) => state.products.find(p => p.id === id),
    isLoading: (state) => state.loading,
    currentFilters: (state) => state.filters,
    getError: (state) => state.error
  },

  actions: {
    async fetchProducts(params = {}) {
      this.loading = true
      this.error = null
      
      try {
        const response = await axios.get('/api/v1/produits', { params })
        
        this.products = response.data.produits || response.data
        this.pagination = {
          currentPage: response.data.current_page || 1,
          lastPage: response.data.last_page || null,
          totalPages: response.data.total_pages || 1,
          total: response.data.total || 0
        }
        
        return { success: true, data: response.data }
      } catch (error) {
        this.loading = false
        this.error = error.response?.data?.message || 'Erreur lors de la récupération des produits'
        return { success: false, error: this.error }
      } finally {
        this.loading = false
      }
    },

    async fetchFeaturedProducts(limit = 8) {
      this.loading = true
      try {
        const response = await axios.get('/api/v1/products/featured', { params: { limit } })
        this.featuredProducts = response.data.produits || response.data.data || []
        
        return { success: true }
      } catch (error) {
        console.error('Fetch featured products error:', error)
        return { success: false, error: error.response?.data?.message || 'Erreur lors du chargement' }
      } finally {
        this.loading = false
      }
    },

    async fetchCategories() {
      this.loading = true
      try {
        const response = await axios.get('/api/v1/categories')
        this.categories = response.data.categories || response.data.data || []
        
        return { success: true }
      } catch (error) {
        console.error('Fetch categories error:', error)
        return { success: false, error: error.response?.data?.message || 'Erreur lors du chargement' }
      } finally {
        this.loading = false
      }
    },

    async fetchProductBySlug(slug) {
      this.loading = true
      try {
        const response = await axios.get(`/api/v1/products/${slug}`)
        this.currentProduct = response.data.data
        
        return { success: true, data: response.data.data }
      } catch (error) {
        console.error('Fetch product error:', error)
        return { success: false, error: error.response?.data?.message || 'Produit non trouvé' }
      } finally {
        this.loading = false
      }
    },

    async fetchProductsByCategory(categorySlug, params = {}) {
      this.loading = true
      try {
        const response = await axios.get(`/api/v1/products/category/${categorySlug}`, { params })
        const { data, meta } = response.data
        
        this.products = data
        this.pagination = {
          currentPage: meta.current_page,
          lastPage: meta.last_page,
          perPage: meta.per_page,
          total: meta.total
        }
        
        return { success: true }
      } catch (error) {
        console.error('Fetch category products error:', error)
        return { success: false, error: error.response?.data?.message || 'Erreur lors du chargement' }
      } finally {
        this.loading = false
      }
    },

    async searchProducts(query, params = {}) {
      this.loading = true
      try {
        const response = await axios.get('/api/v1/products/search', { 
          params: { q: query, ...params } 
        })
        const { data, meta } = response.data
        
        this.products = data
        this.pagination = {
          currentPage: meta.current_page,
          lastPage: meta.last_page,
          perPage: meta.per_page,
          total: meta.total
        }
        
        return { success: true }
      } catch (error) {
        console.error('Search products error:', error)
        return { success: false, error: error.response?.data?.message || 'Erreur lors de la recherche' }
      } finally {
        this.loading = false
      }
    },

    setFilters(filters) {
      this.filters = { ...this.filters, ...filters }
    },

    clearFilters() {
      this.filters = {
        search: '',
        category: null,
        minPrice: null,
        maxPrice: null,
        sortBy: 'created_at',
        sortOrder: 'desc'
      }
    },

    setCurrentProduct(product) {
      this.currentProduct = product
    },

    clearCurrentProduct() {
      this.currentProduct = null
    }
  }
})
