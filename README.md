# FAGMETHOD E-commerce Platform

Plateforme e-commerce moderne pour la vente de produits informatiques et divers.

## Architecture

- **Backend**: Laravel 10+ (API REST)
- **Frontend**: Vue 3 + Vite + TailwindCSS
- **Database**: MySQL
- **Authentification**: Laravel Sanctum
- **State Management**: Pinia

## Structure du Projet

```
fagmethod-ecommerce/
├── backend/          # Application Laravel API
├── frontend/         # Application Vue.js
├── docs/            # Documentation
└── README.md
```

## Installation

### Prérequis

- PHP 8.2+
- Node.js 18+
- Composer
- MySQL
- Git

### Backend (Laravel)

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan serve
```

### Frontend (Vue.js)

```bash
cd frontend
npm install
npm run dev
```

## Fonctionnalités

- 📦 Catalogue de produits avec filtres
- 🛒 Panier d'achat persistant
- 👤 Gestion des comptes clients
- 💳 Système de paiement sécurisé
- 📱 Design responsive
- 🔍 Recherche avancée
- ⭐ Système d'avis et notations
- 📊 Panel d'administration

## API Documentation

L'API documentation sera disponible sur `/api/documentation` une fois le backend lancé.

## Développement

Ce projet suit une feuille de route structurée en 8 phases:

1. **Phase 1**: Configuration initiale ✅
2. **Phase 2**: Backend Laravel - Fondations
3. **Phase 3**: Backend Laravel - API et logique métier
4. **Phase 4**: Frontend Vue.js - Structure et navigation
5. **Phase 5**: Frontend Vue.js - Interface utilisateur
6. **Phase 6**: Intégration et fonctionnalités avancées
7. **Phase 7**: Tests et déploiement
8. **Phase 8**: Maintenance et optimisations

## Licence

© 2024 FAGMETHOD. Tous droits réservés.
