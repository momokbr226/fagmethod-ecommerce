<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Espace Fournisseur</h1>
        <p class="text-gray-600 mt-2">Tableau de bord et gestion de vos produits</p>
      </div>

      <!-- Statistiques principales -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <StatCard
          title="Total Produits"
          :value="statistiques.total_produits || 0"
          icon="📦"
          iconColor="text-blue-600"
        />
        <StatCard
          title="Produits Actifs"
          :value="statistiques.produits_actifs || 0"
          icon="✅"
          iconColor="text-green-600"
        />
        <StatCard
          title="Stock Faible"
          :value="statistiques.produits_stock_faible || 0"
          icon="⚠️"
          iconColor="text-orange-600"
        />
        <StatCard
          title="Rupture"
          :value="statistiques.produits_rupture || 0"
          icon="❌"
          iconColor="text-red-600"
        />
      </div>

      <!-- Statistiques ventes -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <StatCard
          title="Total Ventes"
          :value="statistiques.total_ventes || 0"
          icon="🛒"
          iconColor="text-purple-600"
        />
        <StatCard
          title="Quantité Vendue"
          :value="statistiques.quantite_vendue || 0"
          icon="📊"
          iconColor="text-indigo-600"
        />
        <StatCard
          title="Chiffre d'Affaires"
          :value="formatPrice(statistiques.chiffre_affaires || 0)"
          icon="💰"
          iconColor="text-green-600"
        />
      </div>

      <!-- Alertes -->
      <div v-if="statistiques.produits_stock_faible > 0 || statistiques.produits_rupture > 0" 
           class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-8">
        <div class="flex items-start">
          <span class="text-2xl mr-3">⚠️</span>
          <div class="flex-1">
            <h3 class="font-semibold text-orange-900 mb-2">Alertes Stock</h3>
            <p v-if="statistiques.produits_stock_faible > 0" class="text-sm text-orange-800">
              {{ statistiques.produits_stock_faible }} produit(s) en stock faible
            </p>
            <p v-if="statistiques.produits_rupture > 0" class="text-sm text-orange-800">
              {{ statistiques.produits_rupture }} produit(s) en rupture de stock
            </p>
          </div>
          <router-link 
            to="/fournisseur/stock"
            class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition text-sm"
          >
            Gérer le stock
          </router-link>
        </div>
      </div>

      <!-- Produits populaires -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Produits les Plus Vendus</h2>
        <div class="space-y-4">
          <div v-for="produit in produitsPopulaires" :key="produit.id" 
               class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
            <div class="flex-1">
              <h3 class="font-semibold text-gray-900">{{ produit.nom }}</h3>
              <p class="text-sm text-gray-600">Réf: {{ produit.reference }}</p>
            </div>
            <div class="text-right">
              <p class="font-semibold text-gray-900">{{ produit.ventes }} ventes</p>
              <p class="text-sm text-gray-600">Stock: {{ produit.quantite_stock }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Actions rapides -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <router-link 
          to="/fournisseur/produits"
          class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition text-center"
        >
          <div class="text-4xl mb-3">📦</div>
          <h3 class="font-semibold text-gray-900 mb-2">Mes Produits</h3>
          <p class="text-sm text-gray-600">Gérer mon catalogue</p>
        </router-link>

        <router-link 
          to="/fournisseur/commandes"
          class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition text-center"
        >
          <div class="text-4xl mb-3">📋</div>
          <h3 class="font-semibold text-gray-900 mb-2">Commandes</h3>
          <p class="text-sm text-gray-600">Voir les commandes</p>
        </router-link>

        <router-link 
          to="/fournisseur/statistiques"
          class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition text-center"
        >
          <div class="text-4xl mb-3">📊</div>
          <h3 class="font-semibold text-gray-900 mb-2">Statistiques</h3>
          <p class="text-sm text-gray-600">Analyser les ventes</p>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted } from 'vue'
import { useFournisseurStore } from '@/stores/fournisseur'
import StatCard from '@/components/StatCard.vue'

const fournisseurStore = useFournisseurStore()

const statistiques = computed(() => fournisseurStore.dashboard?.statistiques || {})
const produitsPopulaires = computed(() => fournisseurStore.dashboard?.produits_populaires || [])

onMounted(async () => {
  await fournisseurStore.fetchDashboard()
})

const formatPrice = (price) => {
  return new Intl.NumberFormat('fr-FR', {
    style: 'currency',
    currency: 'EUR'
  }).format(price)
}
</script>
