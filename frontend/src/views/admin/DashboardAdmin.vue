<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Tableau de Bord Administrateur</h1>
        <p class="text-gray-600 mt-2">Vue d'ensemble de la plateforme FAGMETHOD</p>
      </div>

      <!-- Statistiques Utilisateurs -->
      <div class="mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Utilisateurs</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
          <StatCard
            title="Total Utilisateurs"
            :value="stats.utilisateurs?.total || 0"
            icon="👥"
            iconColor="text-blue-600"
          />
          <StatCard
            title="Clients"
            :value="stats.utilisateurs?.clients || 0"
            icon="🛍️"
            iconColor="text-green-600"
          />
          <StatCard
            title="Fournisseurs"
            :value="stats.utilisateurs?.fournisseurs || 0"
            icon="🏭"
            iconColor="text-purple-600"
          />
          <StatCard
            title="Nouveaux ce mois"
            :value="stats.utilisateurs?.nouveaux_ce_mois || 0"
            icon="✨"
            iconColor="text-yellow-600"
          />
        </div>
      </div>

      <!-- Statistiques Produits -->
      <div class="mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Produits</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
          <StatCard
            title="Total Produits"
            :value="stats.produits?.total || 0"
            icon="📦"
            iconColor="text-blue-600"
          />
          <StatCard
            title="Actifs"
            :value="stats.produits?.actifs || 0"
            icon="✅"
            iconColor="text-green-600"
          />
          <StatCard
            title="Stock Faible"
            :value="stats.produits?.stock_faible || 0"
            icon="⚠️"
            iconColor="text-orange-600"
          />
          <StatCard
            title="Rupture"
            :value="stats.produits?.rupture || 0"
            icon="❌"
            iconColor="text-red-600"
          />
        </div>
      </div>

      <!-- Statistiques Commandes -->
      <div class="mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Commandes</h2>
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
          <StatCard
            title="Total"
            :value="stats.commandes?.total || 0"
            icon="📋"
            iconColor="text-gray-600"
          />
          <StatCard
            title="En Attente"
            :value="stats.commandes?.en_attente || 0"
            icon="⏳"
            iconColor="text-yellow-600"
          />
          <StatCard
            title="En Préparation"
            :value="stats.commandes?.en_preparation || 0"
            icon="📦"
            iconColor="text-blue-600"
          />
          <StatCard
            title="Expédiées"
            :value="stats.commandes?.expedie || 0"
            icon="🚚"
            iconColor="text-purple-600"
          />
          <StatCard
            title="Livrées"
            :value="stats.commandes?.livre || 0"
            icon="✅"
            iconColor="text-green-600"
          />
        </div>
      </div>

      <!-- Statistiques Ventes -->
      <div class="mb-8">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Ventes</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
          <StatCard
            title="CA Total"
            :value="formatPrice(stats.ventes?.chiffre_affaires_total || 0)"
            icon="💰"
            iconColor="text-green-600"
          />
          <StatCard
            title="CA Période"
            :value="formatPrice(stats.ventes?.chiffre_affaires_periode || 0)"
            icon="📈"
            iconColor="text-blue-600"
          />
          <StatCard
            title="Ventes Période"
            :value="stats.ventes?.nombre_ventes_periode || 0"
            icon="🛒"
            iconColor="text-purple-600"
          />
          <StatCard
            title="Panier Moyen"
            :value="formatPrice(stats.ventes?.panier_moyen || 0)"
            icon="💳"
            iconColor="text-indigo-600"
          />
        </div>
      </div>

      <!-- Alertes -->
      <div v-if="alertes" class="bg-red-50 border border-red-200 rounded-lg p-6 mb-8">
        <h2 class="text-lg font-semibold text-red-900 mb-4">🚨 Alertes</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div v-if="alertes.produits_stock_faible > 0" class="flex items-center">
            <span class="text-orange-600 mr-2">⚠️</span>
            <span class="text-gray-900">{{ alertes.produits_stock_faible }} produits en stock faible</span>
          </div>
          <div v-if="alertes.produits_rupture > 0" class="flex items-center">
            <span class="text-red-600 mr-2">❌</span>
            <span class="text-gray-900">{{ alertes.produits_rupture }} produits en rupture</span>
          </div>
          <div v-if="alertes.commandes_en_attente > 0" class="flex items-center">
            <span class="text-yellow-600 mr-2">⏳</span>
            <span class="text-gray-900">{{ alertes.commandes_en_attente }} commandes en attente</span>
          </div>
          <div v-if="alertes.commandes_a_expedier > 0" class="flex items-center">
            <span class="text-blue-600 mr-2">📦</span>
            <span class="text-gray-900">{{ alertes.commandes_a_expedier }} commandes à expédier</span>
          </div>
        </div>
      </div>

      <!-- Commandes récentes -->
      <div class="mb-8">
        <TableauCommandes
          title="Commandes Récentes"
          :commandes="commandesRecentes"
          @view="voirCommande"
        />
      </div>

      <!-- Produits populaires -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Produits les Plus Vendus</h2>
        <div class="space-y-3">
          <div v-for="produit in produitsPopulaires" :key="produit.id" 
               class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
            <div class="flex-1">
              <h3 class="font-semibold text-gray-900">{{ produit.nom }}</h3>
              <p class="text-sm text-gray-600">{{ produit.reference }}</p>
            </div>
            <div class="text-right">
              <p class="font-semibold text-green-600">{{ produit.ventes }} ventes</p>
              <p class="text-sm text-gray-600">{{ formatPrice(produit.prix) }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Actions rapides -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-8">
        <router-link 
          to="/admin/utilisateurs"
          class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition text-center"
        >
          <div class="text-4xl mb-3">👥</div>
          <h3 class="font-semibold text-gray-900">Utilisateurs</h3>
        </router-link>

        <router-link 
          to="/admin/produits"
          class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition text-center"
        >
          <div class="text-4xl mb-3">📦</div>
          <h3 class="font-semibold text-gray-900">Produits</h3>
        </router-link>

        <router-link 
          to="/admin/commandes"
          class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition text-center"
        >
          <div class="text-4xl mb-3">📋</div>
          <h3 class="font-semibold text-gray-900">Commandes</h3>
        </router-link>

        <router-link 
          to="/admin/statistiques"
          class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition text-center"
        >
          <div class="text-4xl mb-3">📊</div>
          <h3 class="font-semibold text-gray-900">Statistiques</h3>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAdminStore } from '@/stores/admin'
import StatCard from '@/components/StatCard.vue'
import TableauCommandes from '@/components/TableauCommandes.vue'

const router = useRouter()
const adminStore = useAdminStore()

const stats = computed(() => adminStore.dashboard?.statistiques || {})
const commandesRecentes = computed(() => adminStore.dashboard?.commandes_recentes || [])
const produitsPopulaires = computed(() => adminStore.dashboard?.produits_populaires || [])
const alertes = ref(null)

onMounted(async () => {
  await adminStore.fetchDashboard()
  const result = await adminStore.fetchAlertes()
  if (result.success) {
    alertes.value = result.data.alertes
  }
})

const voirCommande = (id) => {
  router.push(`/admin/commandes/${id}`)
}

const formatPrice = (price) => {
  return new Intl.NumberFormat('fr-FR', {
    style: 'currency',
    currency: 'EUR'
  }).format(price)
}
</script>
