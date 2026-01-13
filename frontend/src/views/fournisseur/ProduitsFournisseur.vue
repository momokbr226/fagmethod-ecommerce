<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Header de la page -->
    <div class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="py-6">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-3xl font-bold text-gray-900">Gestion des Produits</h1>
              <p class="mt-1 text-sm text-gray-600">Gérez votre catalogue de produits</p>
            </div>
            <button 
              @click="showAddModal = true"
              class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors"
            >
              <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
              </svg>
              <span>Nouveau Produit</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Contenu principal -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Filtres et recherche -->
      <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Recherche</label>
            <input
              v-model="searchTerm"
              type="text"
              placeholder="Nom, référence..."
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500"
            >
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Catégorie</label>
            <select 
              v-model="selectedCategory"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500"
            >
              <option value="">Toutes les catégories</option>
              <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.nom }}
              </option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
            <select 
              v-model="selectedStatus"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500"
            >
              <option value="">Tous les statuts</option>
              <option value="visible">Visible</option>
              <option value="hidden">Masqué</option>
              <option value="stock">En stock</option>
              <option value="rupture">Rupture</option>
            </select>
          </div>
          <div class="flex items-end">
            <button 
              @click="resetFilters"
              class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-md transition-colors"
            >
              Réinitialiser
            </button>
          </div>
        </div>
      </div>

      <!-- Liste des produits -->
      <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
          <h2 class="text-lg font-semibold text-gray-900">
            Mes Produits ({{ filteredProducts.length }})
          </h2>
        </div>

        <!-- Loading -->
        <div v-if="loading" class="p-8 text-center">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600 mx-auto"></div>
          <p class="mt-2 text-gray-600">Chargement des produits...</p>
        </div>

        <!-- Table des produits -->
        <div v-else-if="filteredProducts.length > 0" class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Produit
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Référence
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Prix
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Stock
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Statut
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Actions
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="product in paginatedProducts" :key="product.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="h-12 w-12 flex-shrink-0">
                      <img 
                        :src="product.image_principale || '/placeholder-product.jpg'" 
                        :alt="product.nom"
                        class="h-12 w-12 rounded-lg object-cover"
                      >
                    </div>
                    <div class="ml-4">
                      <div class="text-sm font-medium text-gray-900">{{ product.nom }}</div>
                      <div class="text-sm text-gray-500">{{ product.categorie?.nom }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ product.reference }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  <div v-if="product.prix_promo && product.est_promo" class="space-y-1">
                    <div class="text-red-600 font-semibold">{{ formatPrice(product.prix_promo) }}</div>
                    <div class="text-gray-400 line-through text-xs">{{ formatPrice(product.prix) }}</div>
                  </div>
                  <div v-else class="font-semibold">{{ formatPrice(product.prix) }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <span class="text-sm text-gray-900 mr-2">{{ product.quantite_stock }}</span>
                    <span 
                      :class="getStockStatusClass(product.quantite_stock, product.seuil_alerte_stock)"
                      class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                    >
                      {{ getStockStatus(product.quantite_stock, product.seuil_alerte_stock) }}
                    </span>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span 
                    :class="product.est_visible ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                  >
                    {{ product.est_visible ? 'Visible' : 'Masqué' }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                  <button 
                    @click="editProduct(product)"
                    class="text-purple-600 hover:text-purple-900 transition-colors"
                  >
                    Modifier
                  </button>
                  <button 
                    @click="toggleVisibility(product)"
                    class="text-blue-600 hover:text-blue-900 transition-colors"
                  >
                    {{ product.est_visible ? 'Masquer' : 'Afficher' }}
                  </button>
                  <button 
                    @click="deleteProduct(product)"
                    class="text-red-600 hover:text-red-900 transition-colors"
                  >
                    Supprimer
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Aucun produit -->
        <div v-else class="p-8 text-center">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun produit</h3>
          <p class="mt-1 text-sm text-gray-500">Commencez par ajouter votre premier produit.</p>
          <div class="mt-6">
            <button 
              @click="showAddModal = true"
              class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors"
            >
              Ajouter un produit
            </button>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="filteredProducts.length > itemsPerPage" class="px-6 py-4 border-t border-gray-200">
          <div class="flex items-center justify-between">
            <div class="text-sm text-gray-700">
              Affichage de {{ ((currentPage - 1) * itemsPerPage) + 1 }} à {{ Math.min(currentPage * itemsPerPage, filteredProducts.length) }} 
              sur {{ filteredProducts.length }} produits
            </div>
            <div class="flex space-x-2">
              <button 
                @click="currentPage--"
                :disabled="currentPage === 1"
                class="px-3 py-1 border border-gray-300 rounded-md text-sm disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50"
              >
                Précédent
              </button>
              <button 
                @click="currentPage++"
                :disabled="currentPage >= totalPages"
                class="px-3 py-1 border border-gray-300 rounded-md text-sm disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50"
              >
                Suivant
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal d'ajout/modification -->
    <div v-if="showAddModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 w-full max-w-2xl mx-4 max-h-screen overflow-y-auto">
        <h3 class="text-lg font-semibold mb-4">{{ editingProduct ? 'Modifier le produit' : 'Nouveau produit' }}</h3>
        
        <form @submit.prevent="saveProduct" class="space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Nom du produit *</label>
              <input
                v-model="productForm.nom"
                type="text"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500"
              >
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Référence *</label>
              <input
                v-model="productForm.reference"
                type="text"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500"
              >
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <textarea
              v-model="productForm.description"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500"
            ></textarea>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Image principale</label>
            <div class="flex items-center space-x-4">
              <input
                type="file"
                ref="imageInput"
                @change="handleImageUpload"
                accept="image/*"
                class="hidden"
              >
              <button
                type="button"
                @click="$refs.imageInput.click()"
                class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:ring-2 focus:ring-purple-500"
              >
                Choisir une image
              </button>
              <span v-if="selectedImageName" class="text-sm text-gray-600">{{ selectedImageName }}</span>
              <div v-if="imagePreview" class="relative">
                <img :src="imagePreview" alt="Aperçu" class="h-16 w-16 object-cover rounded-lg">
                <button
                  type="button"
                  @click="removeImage"
                  class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs hover:bg-red-600"
                >
                  ×
                </button>
              </div>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Prix *</label>
              <input
                v-model="productForm.prix"
                type="number"
                step="0.01"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500"
              >
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Stock *</label>
              <input
                v-model="productForm.quantite_stock"
                type="number"
                required
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500"
              >
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Seuil d'alerte</label>
              <input
                v-model="productForm.seuil_alerte_stock"
                type="number"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-purple-500 focus:border-purple-500"
              >
            </div>
          </div>

          <div class="flex items-center space-x-4">
            <label class="flex items-center">
              <input
                v-model="productForm.est_visible"
                type="checkbox"
                class="rounded border-gray-300 text-purple-600 focus:ring-purple-500"
              >
              <span class="ml-2 text-sm text-gray-700">Produit visible</span>
            </label>
            <label class="flex items-center">
              <input
                v-model="productForm.est_vedette"
                type="checkbox"
                class="rounded border-gray-300 text-purple-600 focus:ring-purple-500"
              >
              <span class="ml-2 text-sm text-gray-700">Produit vedette</span>
            </label>
          </div>

          <div class="flex justify-end space-x-3 pt-4">
            <button 
              type="button"
              @click="closeModal"
              class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors"
            >
              Annuler
            </button>
            <button 
              type="submit"
              :disabled="saving"
              class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 disabled:opacity-50 transition-colors"
            >
              {{ saving ? 'Enregistrement...' : (editingProduct ? 'Modifier' : 'Créer') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

// État réactif
const products = ref([])
const categories = ref([])
const loading = ref(true)
const saving = ref(false)
const showAddModal = ref(false)
const editingProduct = ref(null)

// Filtres
const searchTerm = ref('')
const selectedCategory = ref('')
const selectedStatus = ref('')

// Pagination
const currentPage = ref(1)
const itemsPerPage = ref(10)

// Formulaire produit
const productForm = ref({
  nom: '',
  reference: '',
  description: '',
  prix: '',
  quantite_stock: '',
  seuil_alerte_stock: 5,
  est_visible: true,
  est_vedette: false,
  image_principale: ''
})

// Gestion d'image
const selectedImageName = ref('')
const imagePreview = ref('')
const selectedImageFile = ref(null)

// Produits filtrés
const filteredProducts = computed(() => {
  let filtered = products.value

  if (searchTerm.value) {
    const term = searchTerm.value.toLowerCase()
    filtered = filtered.filter(product => 
      product.nom.toLowerCase().includes(term) ||
      product.reference.toLowerCase().includes(term)
    )
  }

  if (selectedCategory.value) {
    filtered = filtered.filter(product => product.categorie_id == selectedCategory.value)
  }

  if (selectedStatus.value) {
    switch (selectedStatus.value) {
      case 'visible':
        filtered = filtered.filter(product => product.est_visible)
        break
      case 'hidden':
        filtered = filtered.filter(product => !product.est_visible)
        break
      case 'stock':
        filtered = filtered.filter(product => product.quantite_stock > 0)
        break
      case 'rupture':
        filtered = filtered.filter(product => product.quantite_stock === 0)
        break
    }
  }

  return filtered
})

// Pagination
const totalPages = computed(() => Math.ceil(filteredProducts.value.length / itemsPerPage.value))
const paginatedProducts = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value
  const end = start + itemsPerPage.value
  return filteredProducts.value.slice(start, end)
})

// Méthodes
const fetchProducts = async () => {
  try {
    loading.value = true
    const response = await axios.get('/api/v1/fournisseur/produits')
    products.value = response.data.produits || []
  } catch (error) {
    console.error('Erreur lors du chargement des produits:', error)
    products.value = []
  } finally {
    loading.value = false
  }
}

const fetchCategories = async () => {
  try {
    const response = await axios.get('/api/v1/categories')
    categories.value = response.data.categories || []
  } catch (error) {
    console.error('Erreur lors du chargement des catégories:', error)
  }
}

const formatPrice = (price) => {
  return new Intl.NumberFormat('fr-FR', {
    style: 'currency',
    currency: 'EUR'
  }).format(price)
}

const getStockStatus = (stock, seuil) => {
  if (stock === 0) return 'Rupture'
  if (stock <= seuil) return 'Faible'
  return 'OK'
}

const getStockStatusClass = (stock, seuil) => {
  if (stock === 0) return 'bg-red-100 text-red-800'
  if (stock <= seuil) return 'bg-yellow-100 text-yellow-800'
  return 'bg-green-100 text-green-800'
}

const resetFilters = () => {
  searchTerm.value = ''
  selectedCategory.value = ''
  selectedStatus.value = ''
  currentPage.value = 1
}

const handleImageUpload = (event) => {
  const file = event.target.files[0]
  if (file) {
    selectedImageFile.value = file
    selectedImageName.value = file.name
    
    // Créer un aperçu de l'image
    const reader = new FileReader()
    reader.onload = (e) => {
      imagePreview.value = e.target.result
    }
    reader.readAsDataURL(file)
  }
}

const removeImage = () => {
  selectedImageFile.value = null
  selectedImageName.value = ''
  imagePreview.value = ''
  productForm.value.image_principale = ''
}

const editProduct = (product) => {
  editingProduct.value = product
  productForm.value = { ...product }
  
  // Gérer l'image existante
  if (product.image_principale) {
    imagePreview.value = product.image_principale
    selectedImageName.value = 'Image existante'
  }
  
  showAddModal.value = true
}

const closeModal = () => {
  showAddModal.value = false
  editingProduct.value = null
  productForm.value = {
    nom: '',
    reference: '',
    description: '',
    prix: '',
    quantite_stock: '',
    seuil_alerte_stock: 5,
    est_visible: true,
    est_vedette: false,
    image_principale: ''
  }
  // Réinitialiser les variables d'image
  selectedImageFile.value = null
  selectedImageName.value = ''
  imagePreview.value = ''
}

const saveProduct = async () => {
  try {
    saving.value = true
    
    // Préparer les données avec FormData si une image est sélectionnée
    let data
    if (selectedImageFile.value) {
      data = new FormData()
      Object.keys(productForm.value).forEach(key => {
        let value = productForm.value[key]
        if (value !== null && value !== '') {
          // Convertir les booléens en string pour FormData
          if (typeof value === 'boolean') {
            value = value ? '1' : '0'
          }
          data.append(key, value)
        }
      })
      data.append('image', selectedImageFile.value)
    } else {
      // Nettoyer les données pour l'envoi JSON
      data = { ...productForm.value }
      // Supprimer les champs vides
      Object.keys(data).forEach(key => {
        if (data[key] === '' || data[key] === null) {
          delete data[key]
        }
      })
    }
    
    if (editingProduct.value) {
      await axios.put(`/api/v1/fournisseur/produits/${editingProduct.value.id}`, data, {
        headers: selectedImageFile.value ? { 'Content-Type': 'multipart/form-data' } : {}
      })
    } else {
      await axios.post('/api/v1/fournisseur/produits', data, {
        headers: selectedImageFile.value ? { 'Content-Type': 'multipart/form-data' } : {}
      })
    }
    
    await fetchProducts()
    closeModal()
  } catch (error) {
    console.error('Erreur lors de la sauvegarde:', error)
    
    if (error.response?.status === 422) {
      const errors = error.response.data.erreurs || {}
      const debugData = error.response.data.debug_data || {}
      const errorMessages = Object.values(errors).flat().join('\n')
      
      console.error('Debug données:', debugData)
      console.error('Erreurs validation:', errors)
      
      alert(`Erreurs de validation:\n${errorMessages}\n\n(Vérifiez la console pour les détails)`)
    } else {
      alert('Erreur lors de la sauvegarde du produit')
    }
  } finally {
    saving.value = false
  }
}

const toggleVisibility = async (product) => {
  try {
    await axios.put(`/api/v1/fournisseur/produits/${product.id}`, {
      ...product,
      est_visible: !product.est_visible
    })
    await fetchProducts()
  } catch (error) {
    console.error('Erreur lors de la modification:', error)
  }
}

const deleteProduct = async (product) => {
  if (!confirm(`Êtes-vous sûr de vouloir supprimer "${product.nom}" ?`)) return
  
  try {
    await axios.delete(`/api/v1/fournisseur/produits/${product.id}`)
    await fetchProducts()
  } catch (error) {
    console.error('Erreur lors de la suppression:', error)
    alert('Erreur lors de la suppression du produit')
  }
}

// Initialisation
onMounted(() => {
  fetchProducts()
  fetchCategories()
})
</script>
