import { defineStore } from 'pinia'
import axios from 'axios'

export const useCartStore = defineStore('cart', {
  state: () => ({
    items: [],
    total: 0,
    itemCount: 0,
    loading: false
  }),

  getters: {
    cartTotal: (state) => state.total,
    cartItemCount: (state) => state.itemCount,
    cartItems: (state) => state.items
  },

  actions: {
    async fetchCart() {
      this.loading = true
      try {
        const response = await axios.get('/api/v1/paniers')
        const cartData = response.data.panier || response.data.data || response.data
        
        this.items = cartData.articles || []
        this.total = cartData.total || cartData.sous_total || 0
        this.itemCount = cartData.nombre_articles || this.items.length || 0
        
      } catch (error) {
        console.error('Fetch cart error:', error)
        // Si erreur 401, le panier est vide (non authentifié)
        if (error.response?.status === 401) {
          this.items = []
          this.total = 0
          this.itemCount = 0
        }
      } finally {
        this.loading = false
      }
    },

    async addToCart(productData) {
      this.loading = true
      try {
        const response = await axios.post('/api/v1/paniers/ajouter', productData)
        const cartData = response.data.panier || response.data.data || response.data
        
        this.items = cartData.articles || []
        this.total = cartData.total || cartData.sous_total || 0
        this.itemCount = cartData.nombre_articles || this.items.length || 0
        
        return { success: true, message: response.data.message }
      } catch (error) {
        console.error('Add to cart error:', error)
        console.error('Error response:', error.response?.data)
        this.error = error.response?.data?.message || 'Erreur lors de l\'ajout au panier'
        return { success: false, error: this.error }
      } finally {
        this.loading = false
      }
    },

    async updateCartItem(itemId, quantite) {
      this.loading = true
      try {
        const response = await axios.put(`/api/v1/paniers/${itemId}`, { quantite })
        const cartData = response.data.panier || response.data.data || response.data
        
        this.items = cartData.articles || []
        this.total = cartData.total || cartData.sous_total || 0
        this.itemCount = cartData.nombre_articles || this.items.length || 0
        
        return { success: true, message: response.data.message }
      } catch (error) {
        const message = error.response?.data?.message || 'Erreur lors de la mise à jour'
        return { success: false, error: message }
      } finally {
        this.loading = false
      }
    },

    async removeFromCart(itemId) {
      this.loading = true
      try {
        const response = await axios.delete(`/api/v1/paniers/${itemId}`)
        const cartData = response.data.panier || response.data.data || response.data
        
        this.items = cartData.articles || []
        this.total = cartData.total || cartData.sous_total || 0
        this.itemCount = cartData.nombre_articles || this.items.length || 0
        
        return { success: true, message: response.data.message }
      } catch (error) {
        const message = error.response?.data?.message || 'Erreur lors de la suppression'
        return { success: false, error: message }
      } finally {
        this.loading = false
      }
    },

    async clearCart() {
      this.loading = true
      try {
        const response = await axios.delete('/api/v1/paniers/vider')
        
        this.items = []
        this.total = 0
        this.itemCount = 0
        
        return { success: true, message: response.data.message }
      } catch (error) {
        const message = error.response?.data?.message || 'Erreur lors du vidage'
        return { success: false, error: message }
      } finally {
        this.loading = false
      }
    },

    async getCartCount() {
      try {
        const response = await axios.get('/api/v1/paniers/count')
        const countData = response.data
        
        this.itemCount = countData.nombre_articles || countData.count || 0
        this.total = countData.total || 0
        
        return countData
      } catch (error) {
        console.error('Get cart count error:', error)
      }
    }
  }
})
