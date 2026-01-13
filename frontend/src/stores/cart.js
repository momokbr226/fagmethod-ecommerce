import { defineStore } from 'pinia'
import axios from 'axios'

export const useCartStore = defineStore('cart', {
  state: () => ({
    items: [],
    sousTotal: 0,
    montantTva: 0,
    fraisLivraison: 0,
    remise: 0,
    total: 0,
    itemCount: 0,
    codePromo: null,
    loading: false,
    error: null
  }),

  getters: {
    cartTotal: (state) => state.total,
    cartItemCount: (state) => state.itemCount,
    cartItems: (state) => state.items,
    cartResume: (state) => ({
      sous_total: state.sousTotal,
      montant_tva: state.montantTva,
      frais_livraison: state.fraisLivraison,
      remise: state.remise,
      total: state.total
    })
  },

  actions: {
    async fetchCart() {
      this.loading = true
      try {
        const response = await axios.get('/api/v1/paniers')
        const cartData = response.data.panier || response.data.data || response.data
        const resume = response.data.resume || {}
        
        this.items = cartData.articles || []
        this.sousTotal = resume.sous_total || cartData.sous_total || 0
        this.montantTva = resume.montant_tva || cartData.montant_tva || 0
        this.fraisLivraison = resume.frais_livraison || cartData.frais_livraison || 0
        this.remise = resume.remise || cartData.remise || 0
        this.total = resume.total || cartData.total || 0
        this.itemCount = response.data.nombre_articles || cartData.nombre_articles || this.items.length || 0
        this.codePromo = cartData.code_promo || null
        
      } catch (error) {
        console.error('Fetch cart error:', error)
        // Si erreur 401, le panier est vide (non authentifié)
        if (error.response?.status === 401) {
          this.items = []
          this.sousTotal = 0
          this.montantTva = 0
          this.fraisLivraison = 0
          this.remise = 0
          this.total = 0
          this.itemCount = 0
          this.codePromo = null
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
    },

    async appliquerCodePromo(code) {
      this.loading = true
      this.error = null
      
      try {
        const response = await axios.post('/api/v1/paniers/code-promo', { code_promo: code })
        const cartData = response.data.panier || response.data.data || response.data
        
        this.items = cartData.articles || []
        this.sousTotal = cartData.sous_total || 0
        this.montantTva = cartData.montant_tva || 0
        this.fraisLivraison = cartData.frais_livraison || 0
        this.remise = cartData.remise || response.data.remise || 0
        this.total = cartData.total || 0
        this.itemCount = cartData.nombre_articles || this.items.length || 0
        this.codePromo = code
        
        return { success: true, message: response.data.message }
      } catch (error) {
        this.error = error.response?.data?.message || 'Code promo invalide'
        return { success: false, error: this.error }
      } finally {
        this.loading = false
      }
    },

    async retirerCodePromo() {
      this.loading = true
      this.error = null
      
      try {
        const response = await axios.delete('/api/v1/paniers/code-promo')
        const cartData = response.data.panier || response.data.data || response.data
        
        this.items = cartData.articles || []
        this.sousTotal = cartData.sous_total || 0
        this.montantTva = cartData.montant_tva || 0
        this.fraisLivraison = cartData.frais_livraison || 0
        this.remise = 0
        this.total = cartData.total || 0
        this.itemCount = cartData.nombre_articles || this.items.length || 0
        this.codePromo = null
        
        return { success: true, message: response.data.message }
      } catch (error) {
        this.error = error.response?.data?.message || 'Erreur lors de la suppression du code promo'
        return { success: false, error: this.error }
      } finally {
        this.loading = false
      }
    }
  }
})
