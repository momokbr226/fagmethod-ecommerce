import { defineStore } from 'pinia'
import axios from 'axios'

export const useClientStore = defineStore('client', {
  state: () => ({
    dashboard: null,
    commandes: [],
    commandeDetails: null,
    loading: false,
    error: null
  }),

  getters: {
    statistiques: (state) => state.dashboard?.statistiques || {},
    commandesRecentes: (state) => state.dashboard?.commandes_recentes || []
  },

  actions: {
    async fetchDashboard() {
      this.loading = true
      this.error = null
      
      try {
        const response = await axios.get('/api/v1/client/dashboard')
        this.dashboard = response.data
        return { success: true, data: response.data }
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors du chargement du dashboard'
        return { success: false, error: this.error }
      } finally {
        this.loading = false
      }
    },

    async fetchCommandes(filters = {}) {
      this.loading = true
      this.error = null
      
      try {
        const response = await axios.get('/api/v1/client/commandes', { params: filters })
        this.commandes = response.data.commandes
        return { success: true, data: response.data }
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors du chargement des commandes'
        return { success: false, error: this.error }
      } finally {
        this.loading = false
      }
    },

    async fetchCommandeDetails(id) {
      this.loading = true
      this.error = null
      
      try {
        const response = await axios.get(`/api/v1/client/commandes/${id}`)
        this.commandeDetails = response.data.commande
        return { success: true, data: response.data }
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors du chargement de la commande'
        return { success: false, error: this.error }
      } finally {
        this.loading = false
      }
    },

    async updateProfil(data) {
      this.loading = true
      this.error = null
      
      try {
        const response = await axios.put('/api/v1/client/profil', data)
        return { success: true, data: response.data }
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors de la mise à jour du profil'
        return { success: false, error: this.error }
      } finally {
        this.loading = false
      }
    },

    async changerMotDePasse(data) {
      this.loading = true
      this.error = null
      
      try {
        const response = await axios.put('/api/v1/client/mot-de-passe', data)
        return { success: true, data: response.data }
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors du changement de mot de passe'
        return { success: false, error: this.error }
      } finally {
        this.loading = false
      }
    }
  }
})
