<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
      <h1 class="text-3xl font-bold text-gray-900 mb-8">Mon Profil</h1>

      <!-- Informations personnelles -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Informations Personnelles</h2>
        <form @submit.prevent="updateProfil">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Nom complet</label>
              <input
                v-model="form.nom_complet"
                type="text"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
              <input
                v-model="form.email"
                type="email"
                disabled
                class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Téléphone</label>
              <input
                v-model="form.telephone"
                type="tel"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Date de naissance</label>
              <input
                v-model="form.date_naissance"
                type="date"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
              />
            </div>

            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-2">Adresse</label>
              <input
                v-model="form.adresse"
                type="text"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Ville</label>
              <input
                v-model="form.ville"
                type="text"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Code postal</label>
              <input
                v-model="form.code_postal"
                type="text"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
              />
            </div>
          </div>

          <div class="mt-6 flex justify-end">
            <button
              type="submit"
              :disabled="loading"
              class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition disabled:opacity-50"
            >
              {{ loading ? 'Enregistrement...' : 'Enregistrer les modifications' }}
            </button>
          </div>
        </form>
      </div>

      <!-- Changement de mot de passe -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Changer le mot de passe</h2>
        <form @submit.prevent="changerMotDePasse">
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Mot de passe actuel</label>
              <input
                v-model="passwordForm.mot_de_passe_actuel"
                type="password"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Nouveau mot de passe</label>
              <input
                v-model="passwordForm.nouveau_mot_de_passe"
                type="password"
                required
                minlength="8"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Confirmer le mot de passe</label>
              <input
                v-model="passwordForm.nouveau_mot_de_passe_confirmation"
                type="password"
                required
                minlength="8"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
              />
            </div>
          </div>

          <div class="mt-6 flex justify-end">
            <button
              type="submit"
              :disabled="loadingPassword"
              class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition disabled:opacity-50"
            >
              {{ loadingPassword ? 'Modification...' : 'Changer le mot de passe' }}
            </button>
          </div>
        </form>
      </div>

      <!-- Messages -->
      <div v-if="successMessage" class="mt-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
        {{ successMessage }}
      </div>
      <div v-if="errorMessage" class="mt-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
        {{ errorMessage }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useClientStore } from '@/stores/client'

const authStore = useAuthStore()
const clientStore = useClientStore()

const loading = ref(false)
const loadingPassword = ref(false)
const successMessage = ref('')
const errorMessage = ref('')

const form = reactive({
  nom_complet: '',
  email: '',
  telephone: '',
  date_naissance: '',
  adresse: '',
  ville: '',
  code_postal: ''
})

const passwordForm = reactive({
  mot_de_passe_actuel: '',
  nouveau_mot_de_passe: '',
  nouveau_mot_de_passe_confirmation: ''
})

onMounted(async () => {
  await authStore.fetchProfile()
  if (authStore.user) {
    Object.assign(form, authStore.user)
  }
})

const updateProfil = async () => {
  loading.value = true
  successMessage.value = ''
  errorMessage.value = ''

  const result = await clientStore.updateProfil(form)
  
  if (result.success) {
    successMessage.value = 'Profil mis à jour avec succès'
    await authStore.fetchProfile()
  } else {
    errorMessage.value = result.error
  }
  
  loading.value = false
}

const changerMotDePasse = async () => {
  if (passwordForm.nouveau_mot_de_passe !== passwordForm.nouveau_mot_de_passe_confirmation) {
    errorMessage.value = 'Les mots de passe ne correspondent pas'
    return
  }

  loadingPassword.value = true
  successMessage.value = ''
  errorMessage.value = ''

  const result = await clientStore.changerMotDePasse(passwordForm)
  
  if (result.success) {
    successMessage.value = 'Mot de passe changé avec succès'
    passwordForm.mot_de_passe_actuel = ''
    passwordForm.nouveau_mot_de_passe = ''
    passwordForm.nouveau_mot_de_passe_confirmation = ''
  } else {
    errorMessage.value = result.error
  }
  
  loadingPassword.value = false
}
</script>
