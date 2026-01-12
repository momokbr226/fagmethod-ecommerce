<template>
  <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
          <h2 class="text-center text-3xl font-extrabold text-gray-900">
            Créez votre compte
          </h2>
          <p class="mt-2 text-center text-sm text-gray-600">
            Ou 
            <router-link to="/login" class="font-medium text-primary-600 hover:text-primary-500">
              connectez-vous à votre compte existant
            </router-link>
          </p>
        </div>

        <form class="mt-8 space-y-6" @submit.prevent="handleRegister">
          <!-- Name -->
          <div>
            <label for="name" class="block text-sm font-medium text-gray-700">
              Nom complet
            </label>
            <div class="mt-1">
              <input
                id="name"
                v-model="form.name"
                name="name"
                type="text"
                autocomplete="name"
                required
                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-gray-900"
                placeholder="Jean Dupont"
              >
              <div v-if="errors.name" class="mt-2 text-sm text-red-600">
                {{ errors.name }}
              </div>
            </div>
          </div>

          <!-- Email -->
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700">
              Adresse email
            </label>
            <div class="mt-1">
              <input
                id="email"
                v-model="form.email"
                name="email"
                type="email"
                autocomplete="email"
                required
                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-gray-900"
                placeholder="vous@exemple.com"
              >
              <div v-if="errors.email" class="mt-2 text-sm text-red-600">
                {{ errors.email }}
              </div>
            </div>
          </div>

          <!-- Password -->
          <div>
            <label for="password" class="block text-sm font-medium text-gray-700">
              Mot de passe
            </label>
            <div class="mt-1">
              <input
                id="password"
                v-model="form.password"
                name="password"
                type="password"
                autocomplete="new-password"
                required
                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-gray-900"
                placeholder="•••••••••"
              >
              <div v-if="errors.password" class="mt-2 text-sm text-red-600">
                {{ errors.password }}
              </div>
            </div>
          </div>

          <!-- Password Confirmation -->
          <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
              Confirmer le mot de passe
            </label>
            <div class="mt-1">
              <input
                id="password_confirmation"
                v-model="form.password_confirmation"
                name="password_confirmation"
                type="password"
                autocomplete="new-password"
                required
                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm text-gray-900"
                placeholder="•••••••••"
              >
              <div v-if="errors.password_confirmation" class="mt-2 text-sm text-red-600">
                {{ errors.password_confirmation }}
              </div>
            </div>
          </div>

          <!-- Terms -->
          <div class="flex items-center">
            <input
              id="agree-terms"
              name="agree-terms"
              type="checkbox"
              v-model="form.agreeTerms"
              class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
              required
            >
            <label for="agree-terms" class="ml-2 block text-sm text-gray-900">
              J'accepte les 
              <router-link to="/terms" class="text-indigo-600 hover:text-indigo-500">
                conditions d'utilisation
              </router-link>
              et la 
              <router-link to="/privacy" class="text-indigo-600 hover:text-indigo-500">
                politique de confidentialité
              </router-link>
            </label>
          </div>

          <!-- Submit Button -->
          <div>
            <button
              type="submit"
              :disabled="loading || !form.agreeTerms"
              class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <svg v-if="loading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V4a8 8 0 00-8 8v4a8 8 0 0018 8z"></path>
              </svg>
              {{ loading ? 'Inscription...' : 'S\'inscrire' }}
            </button>
          </div>

          <!-- Error Message -->
          <div v-if="error" class="mt-4 text-center text-sm text-red-600">
            {{ error }}
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const form = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  agreeTerms: false
})

const loading = ref(false)
const error = ref('')
const errors = ref({})

const handleRegister = async () => {
  loading.value = true
  error.value = ''
  errors.value = {}

  const result = await authStore.register({
    name: form.value.name,
    email: form.value.email,
    password: form.value.password,
    password_confirmation: form.value.password_confirmation
  })

  loading.value = false

  if (result.success) {
    router.push('/')
  } else {
    error.value = result.error
    // You could also handle validation errors here
  }
}
</script>
