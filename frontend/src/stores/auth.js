import { defineStore } from 'pinia'
import axios from 'axios'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    authToken: localStorage.getItem('token') || null,
    isAuthenticated: false,
    loading: false,
    error: null
  }),

  getters: {
    currentUser: (state) => state.user,
    isLoggedIn: (state) => !!state.authToken && !!state.user
  },

  actions: {
    async register(credentials) {
      this.loading = true
      this.error = null
      
      try {
        const response = await axios.post('/api/v1/auth/register', credentials)
        const { utilisateur, token } = response.data
        
        this.user = utilisateur
        this.authToken = token
        this.isAuthenticated = true
        
        localStorage.setItem('token', token)
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
        
        return { success: true, data: response.data }
      } catch (error) {
        this.loading = false
        this.error = error.response?.data?.message || 'Erreur lors de l\'inscription'
        return { success: false, error: this.error }
      }
    },

    async login(credentials) {
      this.loading = true
      this.error = null
      
      try {
        const response = await axios.post('/api/v1/auth/login', credentials)
        const { user, token } = response.data
        
        this.user = user
        this.authToken = token
        this.isAuthenticated = true
        
        localStorage.setItem('token', token)
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
        
        return { success: true, data: response.data }
      } catch (error) {
        this.loading = false
        this.error = error.response?.data?.message || 'Identifiants invalides'
        return { success: false, error: this.error }
      }
    },

    async logout() {
      try {
        await axios.post('/api/v1/auth/logout')
      } catch (error) {
        console.error('Logout error:', error)
      } finally {
        this.user = null
        this.authToken = null
        this.isAuthenticated = false
        
        localStorage.removeItem('token')
        delete axios.defaults.headers.common['Authorization']
      }
    },

    async fetchProfile() {
      if (!this.authToken) return
      
      this.loading = true
      
      try {
        const response = await axios.get('/api/v1/auth/profile')
        
        this.user = response.data.utilisateur
        
        return { success: true, data: response.data }
      } catch (error) {
        this.loading = false
        this.error = error.response?.data?.message || 'Erreur lors de la récupération du profil'
        return { success: false, error: this.error }
      } finally {
        this.loading = false
      }
    },

    initializeAuth() {
      const token = localStorage.getItem('token')
      if (token) {
        this.authToken = token
        this.isAuthenticated = true
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
        this.fetchProfile()
      }
    }
  }
})
