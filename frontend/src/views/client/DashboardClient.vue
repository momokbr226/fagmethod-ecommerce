<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- En-tête -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Mon Espace Client</h1>
        <p class="text-gray-600 mt-2">Bienvenue {{ utilisateur?.nom_complet }}</p>
      </div>

      <!-- Statistiques -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <StatCard
          title="Total Commandes"
          :value="statistiques.total_commandes || 0"
          icon="📦"
          iconColor="text-blue-600"
        />
        <StatCard
          title="En Cours"
          :value="statistiques.commandes_en_cours || 0"
          icon="🚚"
          iconColor="text-orange-600"
        />
        <StatCard
          title="Livrées"
          :value="statistiques.commandes_livrees || 0"
          icon="✅"
          iconColor="text-green-600"
        />
        <StatCard
          title="Total Dépensé"
          :value="formatPrice(statistiques.montant_total_depense || 0)"
          icon="💰"
          iconColor="text-purple-600"
        />
      </div>

      <!-- Panier actuel -->
      <div v-if="statistiques.panier_actuel > 0" class="bg-green-50 border border-green-200 rounded-lg p-4 mb-8">
        <div class="flex items-center justify-between">
          <div class="flex items-center">
            <span class="text-2xl mr-3">🛒</span>
            <div>
              <p class="font-semibold text-green-900">Vous avez {{ statistiques.panier_actuel }} article(s) dans votre panier</p>
              <p class="text-sm text-green-700">Finalisez votre commande maintenant</p>
            </div>
          </div>
          <router-link 
            to="/cart" 
            class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition"
          >
            Voir le panier
          </router-link>
        </div>
      </div>

      <!-- Commandes récentes -->
      <TableauCommandes
        title="Mes Commandes Récentes"
        :commandes="commandesRecentes"
        @view="voirCommande"
      />

      <!-- Actions rapides -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
        <router-link 
          to="/client/commandes"
          class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition text-center"
        >
          <div class="text-4xl mb-3">📋</div>
          <h3 class="font-semibold text-gray-900 mb-2">Toutes mes commandes</h3>
          <p class="text-sm text-gray-600">Voir l'historique complet</p>
        </router-link>

        <router-link 
          to="/client/profil"
          class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition text-center"
        >
          <div class="text-4xl mb-3">👤</div>
          <h3 class="font-semibold text-gray-900 mb-2">Mon Profil</h3>
          <p class="text-sm text-gray-600">Gérer mes informations</p>
        </router-link>

        <router-link 
          to="/products"
          class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition text-center"
        >
          <div class="text-4xl mb-3">🛍️</div>
          <h3 class="font-semibold text-gray-900 mb-2">Continuer mes achats</h3>
          <p class="text-sm text-gray-600">Découvrir nos produits</p>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useClientStore } from '@/stores/client'
import StatCard from '@/components/StatCard.vue'
import TableauCommandes from '@/components/TableauCommandes.vue'

const router = useRouter()
const clientStore = useClientStore()

const utilisateur = computed(() => clientStore.dashboard?.utilisateur)
const statistiques = computed(() => clientStore.dashboard?.statistiques || {})
const commandesRecentes = computed(() => clientStore.dashboard?.commandes_recentes || [])

onMounted(async () => {
  await clientStore.fetchDashboard()
})

const voirCommande = (id) => {
  router.push(`/client/commandes/${id}`)
}

const formatPrice = (price) => {
  return new Intl.NumberFormat('fr-FR', {
    style: 'currency',
    currency: 'EUR'
  }).format(price)
}
</script>
