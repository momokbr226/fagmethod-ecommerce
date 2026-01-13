import { defineStore } from 'pinia'
import axios from 'axios'

export const useFournisseurStore = defineStore('fournisseur', {
  state: () => ({
    dashboard: null,
    produits: [],
    produitsStockFaible: [],
    produitsRupture: [],
    commandes: [],
    statistiquesVentes: null,
    loading: false,
    error: null
  }),

  getters: {
    statistiques: (state) => state.dashboard?.statistiques || {},
    produitsPopulaires: (state) => state.dashboard?.produits_populaires || []
  },

  actions: {
    async fetchDashboard() {
      this.loading = true
      this.error = null
      
      try {
        const response = await axios.get('/api/v1/fournisseur/dashboard')
        this.dashboard = response.data
        return { success: true, data: response.data }
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors du chargement du dashboard'
        return { success: false, error: this.error }
      } finally {
        this.loading = false
      }
    },

    async fetchMesProduits(filters = {}) {
      this.loading = true
      this.error = null
      
      try {
        const response = await axios.get('/api/v1/fournisseur/produits', { params: filters })
        this.produits = response.data.produits
        return { success: true, data: response.data }
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors du chargement des produits'
        return { success: false, error: this.error }
      } finally {
        this.loading = false
      }
    },

    async fetchProduitsStockFaible() {
      this.loading = true
      this.error = null
      
      try {
        const response = await axios.get('/api/v1/fournisseur/produits/stock-faible')
        this.produitsStockFaible = response.data.produits
        return { success: true, data: response.data }
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors du chargement'
        return { success: false, error: this.error }
      } finally {
        this.loading = false
      }
    },

    async fetchProduitsRupture() {
      this.loading = true
      this.error = null
      
      try {
        const response = await axios.get('/api/v1/fournisseur/produits/rupture')
        this.produitsRupture = response.data.produits
        return { success: true, data: response.data }
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors du chargement'
        return { success: false, error: this.error }
      } finally {
        this.loading = false
      }
    },

    async updateStock(produitId, data) {
      this.loading = true
      this.error = null
      
      try {
        const response = await axios.put(`/api/v1/fournisseur/produits/${produitId}/stock`, data)
        return { success: true, data: response.data }
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors de la mise à jour du stock'
        return { success: false, error: this.error }
      } finally {
        this.loading = false
      }
    },

    async fetchCommandes(filters = {}) {
      this.loading = true
      this.error = null
      
      try {
        const response = await axios.get('/api/v1/fournisseur/commandes', { params: filters })
        this.commandes = response.data.commandes
        return { success: true, data: response.data }
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors du chargement des commandes'
        return { success: false, error: this.error }
      } finally {
        this.loading = false
      }
    },

    async fetchStatistiquesVentes(periode = 30) {
      this.loading = true
      this.error = null
      
      try {
        const response = await axios.get('/api/v1/fournisseur/statistiques/ventes', { 
          params: { periode } 
        })
        this.statistiquesVentes = response.data
        return { success: true, data: response.data }
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors du chargement des statistiques'
        return { success: false, error: this.error }
      } finally {
        this.loading = false
      }
    }
  }
})
