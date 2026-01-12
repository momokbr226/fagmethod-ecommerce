import { defineStore } from 'pinia'
import axios from 'axios'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('token') || null,
    isAuthenticated: false
  }),

  getters: {
    currentUser: (state) => state.user,
    isLoggedIn: (state) => !!state.token && !!state.user
  },

  actions: {
    async register(credentials) {
      try {
        const response = await axios.post('/api/v1/auth/register', credentials)
        const { user, token } = response.data.data
        
        this.user = user
        this.token = token
        this.isAuthenticated = true
        
        localStorage.setItem('token', token)
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
        
        return { success: true }
      } catch (error) {
        return { 
          success: false, 
          error: error.response?.data?.message || 'Erreur lors de l\'inscription' 
        }
      }
    },

    async login(credentials) {
      try {
        const response = await axios.post('/api/v1/auth/login', credentials)
        const { user, token } = response.data
        
        this.user = user
        this.token = token
        this.isAuthenticated = true
        
        localStorage.setItem('token', token)
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
        
        return { success: true }
      } catch (error) {
        return { 
          success: false, 
          error: error.response?.data?.message || 'Identifiants invalides' 
        }
      }
    },

    async logout() {
      try {
        await axios.post('/api/v1/auth/logout')
      } catch (error) {
        console.error('Logout error:', error)
      } finally {
        this.user = null
        this.token = null
        this.isAuthenticated = false
        
        localStorage.removeItem('token')
        delete axios.defaults.headers.common['Authorization']
      }
    },

    async fetchProfile() {
      if (!this.token) return
      
      try {
        const response = await axios.get('/api/v1/auth/profile')
        this.user = response.data.user
      } catch (error) {
        console.error('Fetch profile error:', error)
        this.logout()
      }
    },

    initializeAuth() {
      const token = localStorage.getItem('token')
      if (token) {
        this.token = token
        this.isAuthenticated = true
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
        this.fetchProfile()
      }
    }
  }
})
