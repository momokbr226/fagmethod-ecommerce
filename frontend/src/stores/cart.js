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
        const response = await axios.get('/api/v1/cart')
        // The response structure is { data: { items: [...], total: "...", items_count: N } }
        const cartData = response.data.data || response.data
        
        this.items = cartData.items || []
        this.total = cartData.total || 0
        this.itemCount = cartData.items_count || 0
        
      } catch (error) {
        console.error('Fetch cart error:', error)
        console.error('Error response:', error.response?.data)
        console.error('Error status:', error.response?.status)
      } finally {
        this.loading = false
      }
    },

    async addToCart(productData) {
      this.loading = true
      try {
        const response = await axios.post('/api/v1/cart/add', productData)
        // The response structure is { message: "...", data: { items: [...], total: "...", items_count: N } }
        const cartData = response.data.data || response.data
        
        this.items = cartData.items || []
        this.total = cartData.total || 0
        this.itemCount = cartData.items_count || 0
        
        return { success: true, message: response.data.message }
      } catch (error) {
        console.error('Add to cart error:', error)
        console.error('Error response:', error.response?.data)
        console.error('Error status:', error.response?.status)
        const message = error.response?.data?.message || 'Erreur lors de l\'ajout au panier'
        return { success: false, error: message }
      } finally {
        this.loading = false
      }
    },

    async updateCartItem(itemId, quantity) {
      this.loading = true
      try {
        const response = await axios.put(`/api/v1/cart/update/${itemId}`, { quantity })
        // The response structure is { data: { items: [...], total: "...", items_count: N } }
        const cartData = response.data.data || response.data
        
        this.items = cartData.items || []
        this.total = cartData.total || 0
        this.itemCount = cartData.items_count || 0
        
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
        const response = await axios.delete(`/api/v1/cart/remove/${itemId}`)
        // The response structure is { data: { items: [...], total: "...", items_count: N } }
        const cartData = response.data.data || response.data
        
        this.items = cartData.items || []
        this.total = cartData.total || 0
        this.itemCount = cartData.items_count || 0
        
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
        const response = await axios.delete('/api/v1/cart/clear')
        
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
        const response = await axios.get('/api/v1/cart/count')
        const countData = response.data
        
        this.itemCount = countData.count || 0
        this.total = countData.total || 0
        
        return countData
      } catch (error) {
        console.error('Get cart count error:', error)
      }
    }
  }
})
