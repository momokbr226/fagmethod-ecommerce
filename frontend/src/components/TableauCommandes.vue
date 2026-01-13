<template>
  <div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
      <h3 class="text-lg font-semibold text-gray-900">{{ title }}</h3>
    </div>
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">N° Commande</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="commande in commandes" :key="commande.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
              {{ commande.numero_commande }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ formatDate(commande.created_at) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span :class="getStatutClass(commande.statut)" class="px-2 py-1 text-xs font-semibold rounded-full">
                {{ getStatutLabel(commande.statut) }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
              {{ formatPrice(commande.montant_total) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm">
              <button 
                @click="$emit('view', commande.id)"
                class="text-green-600 hover:text-green-900 font-medium"
              >
                Voir détails
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div v-if="commandes.length === 0" class="px-6 py-12 text-center text-gray-500">
      Aucune commande trouvée
    </div>
  </div>
</template>

<script setup>
defineProps({
  title: {
    type: String,
    default: 'Commandes'
  },
  commandes: {
    type: Array,
    required: true
  }
})

defineEmits(['view'])

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const formatPrice = (price) => {
  return new Intl.NumberFormat('fr-FR', {
    style: 'currency',
    currency: 'EUR'
  }).format(price)
}

const getStatutClass = (statut) => {
  const classes = {
    'en_attente': 'bg-yellow-100 text-yellow-800',
    'en_preparation': 'bg-blue-100 text-blue-800',
    'expedie': 'bg-purple-100 text-purple-800',
    'livre': 'bg-green-100 text-green-800',
    'annule': 'bg-red-100 text-red-800'
  }
  return classes[statut] || 'bg-gray-100 text-gray-800'
}

const getStatutLabel = (statut) => {
  const labels = {
    'en_attente': 'En attente',
    'en_preparation': 'En préparation',
    'expedie': 'Expédiée',
    'livre': 'Livrée',
    'annule': 'Annulée'
  }
  return labels[statut] || statut
}
</script>
