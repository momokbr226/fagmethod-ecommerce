import { defineStore } from 'pinia'
import axios from 'axios'

export const useOrdersStore = defineStore('orders', {
  state: () => ({
    orders: [],
    loading: false,
    error: null
  }),

  getters: {
    allOrders: (state) => state.orders,
    isLoading: (state) => state.loading,
    hasError: (state) => state.error
  },

  actions: {
    async fetchOrders(params = {}) {
      this.loading = true
      this.error = null
      
      try {
        const response = await axios.get('/api/v1/commandes', { params })
        
        this.orders = response.data.commandes || response.data
        this.pagination = {
          currentPage: response.data.current_page || 1,
          lastPage: response.data.last_page || null,
          totalPages: response.data.total_pages || 1,
          total: response.data.total || 0
        }
        
        return { success: true, data: response.data }
      } catch (error) {
        this.loading = false
        this.error = error.response?.data?.message || 'Erreur lors de la récupération des commandes'
        return { success: false, error: this.error }
      } finally {
        this.loading = false
      }
    },

    async createOrder(orderData) {
      this.loading = true
      this.error = null
      
      try {
        const response = await axios.post('/api/v1/orders', orderData)
        console.log('Order created:', response.data)
        
        return { success: true, data: response.data }
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors de la création de la commande'
        console.error('Create order error:', error)
        return { success: false, error: this.error }
      } finally {
        this.loading = false
      }
    },

    async getOrder(orderId) {
      this.loading = true
      this.error = null
      
      try {
        const response = await axios.get(`/api/v1/orders/${orderId}`)
        console.log('Order fetched:', response.data)
        
        return { success: true, data: response.data }
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors de la récupération de la commande'
        console.error('Get order error:', error)
        return { success: false, error: this.error }
      } finally {
        this.loading = false
      }
    }
  }
})
