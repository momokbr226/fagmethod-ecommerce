import { defineStore } from 'pinia'
import axios from 'axios'

export const useAdminStore = defineStore('admin', {
  state: () => ({
    dashboard: null,
    statistiquesVentes: null,
    statistiquesCatalogue: null,
    alertes: null,
    utilisateurs: [],
    produits: [],
    commandes: [],
    loading: false,
    error: null
  }),

  getters: {
    statistiquesGenerales: (state) => state.dashboard?.statistiques || {},
    commandesRecentes: (state) => state.dashboard?.commandes_recentes || [],
    produitsPopulaires: (state) => state.dashboard?.produits_populaires || []
  },

  actions: {
    async fetchDashboard(periode = 30) {
      this.loading = true
      this.error = null
      
      try {
        const response = await axios.get('/api/v1/admin/dashboard', { params: { periode } })
        this.dashboard = response.data
        return { success: true, data: response.data }
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors du chargement du dashboard'
        return { success: false, error: this.error }
      } finally {
        this.loading = false
      }
    },

    async fetchStatistiquesVentes(periode = 30) {
      this.loading = true
      this.error = null
      
      try {
        const response = await axios.get('/api/v1/admin/statistiques/ventes', { 
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
    },

    async fetchStatistiquesCatalogue() {
      this.loading = true
      this.error = null
      
      try {
        const response = await axios.get('/api/v1/admin/statistiques/catalogue')
        this.statistiquesCatalogue = response.data
        return { success: true, data: response.data }
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors du chargement'
        return { success: false, error: this.error }
      } finally {
        this.loading = false
      }
    },

    async fetchAlertes() {
      this.loading = true
      this.error = null
      
      try {
        const response = await axios.get('/api/v1/admin/alertes')
        this.alertes = response.data.alertes
        return { success: true, data: response.data }
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors du chargement des alertes'
        return { success: false, error: this.error }
      } finally {
        this.loading = false
      }
    },

    async fetchUtilisateurs(filters = {}) {
      this.loading = true
      this.error = null
      
      try {
        const response = await axios.get('/api/v1/admin/utilisateurs', { params: filters })
        this.utilisateurs = response.data.utilisateurs
        return { success: true, data: response.data }
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors du chargement des utilisateurs'
        return { success: false, error: this.error }
      } finally {
        this.loading = false
      }
    },

    async fetchProduits(filters = {}) {
      this.loading = true
      this.error = null
      
      try {
        const response = await axios.get('/api/v1/admin/produits', { params: filters })
        this.produits = response.data.produits
        return { success: true, data: response.data }
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors du chargement des produits'
        return { success: false, error: this.error }
      } finally {
        this.loading = false
      }
    },

    async fetchCommandes(filters = {}) {
      this.loading = true
      this.error = null
      
      try {
        const response = await axios.get('/api/v1/admin/commandes', { params: filters })
        this.commandes = response.data.commandes
        return { success: true, data: response.data }
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors du chargement des commandes'
        return { success: false, error: this.error }
      } finally {
        this.loading = false
      }
    }
  }
})
