import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import Home from '../views/Home.vue'
import Products from '../views/Products.vue'
import ProductDetail from '../views/ProductDetail.vue'
import Categories from '../views/Categories.vue'
import Cart from '../views/Cart.vue'
import Login from '../views/Login.vue'
import Register from '../views/Register.vue'
import Profile from '../views/Profile.vue'
import Checkout from '../views/Checkout.vue'

// Espaces utilisateurs
import DashboardClient from '../views/client/DashboardClient.vue'
import ProfilClient from '../views/client/ProfilClient.vue'
import DashboardFournisseur from '../views/fournisseur/DashboardFournisseur.vue'
import ProduitsFournisseur from '../views/fournisseur/ProduitsFournisseur.vue'
import DashboardAdmin from '../views/admin/DashboardAdmin.vue'

const routes = [
  {
    path: '/',
    name: 'Home',
    component: Home
  },
  {
    path: '/products',
    name: 'Products',
    component: Products
  },
  {
    path: '/products/:slug',
    name: 'ProductDetail',
    component: ProductDetail,
    props: true
  },
  {
    path: '/categories',
    name: 'Categories',
    component: Categories
  },
  {
    path: '/categories/:slug',
    name: 'CategoryProducts',
    component: Products,
    props: route => ({ categorySlug: route.params.slug })
  },
  {
    path: '/cart',
    name: 'Cart',
    component: Cart
  },
  {
    path: '/login',
    name: 'Login',
    component: Login
  },
  {
    path: '/register',
    name: 'Register',
    component: Register
  },
  {
    path: '/profile',
    name: 'Profile',
    component: Profile,
    meta: { requiresAuth: true }
  },
  {
    path: '/checkout',
    name: 'Checkout',
    component: Checkout,
    meta: { requiresAuth: true }
  },
  
  // Routes Espace Client
  {
    path: '/client',
    meta: { requiresAuth: true, requiresRole: 'client' },
    children: [
      {
        path: 'dashboard',
        name: 'ClientDashboard',
        component: DashboardClient
      },
      {
        path: 'profil',
        name: 'ClientProfil',
        component: ProfilClient
      }
    ]
  },

  // Routes Espace Fournisseur
  {
    path: '/fournisseur',
    meta: { requiresAuth: true, requiresRole: 'fournisseur' },
    children: [
      {
        path: 'dashboard',
        name: 'FournisseurDashboard',
        component: DashboardFournisseur
      },
      {
        path: 'produits',
        name: 'FournisseurProduits',
        component: ProduitsFournisseur
      }
    ]
  },

  // Routes Espace Admin
  {
    path: '/admin',
    meta: { requiresAuth: true, requiresRole: 'admin' },
    children: [
      {
        path: 'dashboard',
        name: 'AdminDashboard',
        component: DashboardAdmin
      }
    ]
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    // Si l'utilisateur utilise les boutons précédent/suivant du navigateur
    if (savedPosition) {
      return savedPosition
    }
    // Sinon, toujours remonter en haut de la page
    return { top: 0, behavior: 'smooth' }
  }
})

// Navigation guards
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()
  
  // Vérifier si la route nécessite une authentification
  if (to.meta.requiresAuth && !authStore.isLoggedIn) {
    next({ name: 'Login', query: { redirect: to.fullPath } })
    return
  }
  
  // Vérifier le rôle requis
  if (to.meta.requiresRole) {
    const requiredRole = to.meta.requiresRole
    const userRoles = authStore.userRoles
    
    if (!userRoles.includes(requiredRole)) {
      // Rediriger vers le dashboard approprié selon le rôle
      if (authStore.isAdmin) {
        next({ name: 'AdminDashboard' })
      } else if (authStore.isFournisseur) {
        next({ name: 'FournisseurDashboard' })
      } else if (authStore.isClient) {
        next({ name: 'ClientDashboard' })
      } else {
        next({ name: 'Home' })
      }
      return
    }
  }
  
  next()
})

export default router
