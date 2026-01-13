<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Utilisateur;
use App\Models\Produit;
use App\Models\Commande;
use App\Models\Categorie;
use App\Models\Marque;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DashboardAdminController extends Controller
{
    /**
     * Tableau de bord principal
     */
    public function index(Request $request): JsonResponse
    {
        $periode = $request->get('periode', 30); // 30 jours par défaut

        // Statistiques générales
        $statistiques = [
            'utilisateurs' => [
                'total' => Utilisateur::count(),
                'clients' => Utilisateur::role('client')->count(),
                'fournisseurs' => Utilisateur::role('fournisseur')->count(),
                'nouveaux_ce_mois' => Utilisateur::where('created_at', '>=', now()->startOfMonth())->count(),
            ],
            'produits' => [
                'total' => Produit::count(),
                'actifs' => Produit::where('est_visible', true)->count(),
                'stock_faible' => Produit::where('statut_stock', 'stock_faible')->count(),
                'rupture' => Produit::where('statut_stock', 'rupture')->count(),
            ],
            'commandes' => [
                'total' => Commande::count(),
                'en_attente' => Commande::where('statut', 'en_attente')->count(),
                'en_preparation' => Commande::where('statut', 'en_preparation')->count(),
                'expedie' => Commande::where('statut', 'expedie')->count(),
                'livre' => Commande::where('statut', 'livre')->count(),
                'annule' => Commande::where('statut', 'annule')->count(),
            ],
            'ventes' => [
                'chiffre_affaires_total' => Commande::where('statut', '!=', 'annule')->sum('montant_total'),
                'chiffre_affaires_periode' => Commande::where('statut', '!=', 'annule')
                    ->where('created_at', '>=', now()->subDays($periode))
                    ->sum('montant_total'),
                'nombre_ventes_periode' => Commande::where('created_at', '>=', now()->subDays($periode))
                    ->where('statut', '!=', 'annule')
                    ->count(),
                'panier_moyen' => $this->calculerPanierMoyen($periode),
            ],
        ];

        // Commandes récentes
        $commandesRecentes = Commande::with(['utilisateur', 'articles'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Produits populaires
        $produitsPopulaires = Produit::orderBy('ventes', 'desc')
            ->take(10)
            ->get(['id', 'nom', 'reference', 'ventes', 'prix', 'quantite_stock']);

        return response()->json([
            'statistiques' => $statistiques,
            'commandes_recentes' => $commandesRecentes,
            'produits_populaires' => $produitsPopulaires,
        ]);
    }

    /**
     * Statistiques de ventes détaillées
     */
    public function statistiquesVentes(Request $request): JsonResponse
    {
        $periode = $request->get('periode', 30);

        // Ventes par jour
        $ventesParJour = Commande::where('created_at', '>=', now()->subDays($periode))
            ->where('statut', '!=', 'annule')
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as nombre_commandes'),
                DB::raw('SUM(montant_total) as chiffre_affaires')
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        // Ventes par catégorie
        $ventesParCategorie = Categorie::withCount(['produits as total_ventes' => function($q) use ($periode) {
                $q->where('created_at', '>=', now()->subDays($periode));
            }])
            ->having('total_ventes', '>', 0)
            ->orderBy('total_ventes', 'desc')
            ->get(['id', 'nom', 'total_ventes']);

        // Top clients
        $topClients = Utilisateur::role('client')
            ->withCount(['commandes as total_commandes' => function($q) {
                $q->where('statut', '!=', 'annule');
            }])
            ->withSum(['commandes as montant_total' => function($q) {
                $q->where('statut', '!=', 'annule');
            }], 'montant_total')
            ->having('total_commandes', '>', 0)
            ->orderBy('montant_total', 'desc')
            ->take(10)
            ->get(['id', 'nom_complet', 'email', 'total_commandes', 'montant_total']);

        return response()->json([
            'periode' => $periode,
            'ventes_par_jour' => $ventesParJour,
            'ventes_par_categorie' => $ventesParCategorie,
            'top_clients' => $topClients,
        ]);
    }

    /**
     * Gestion des utilisateurs
     */
    public function utilisateurs(Request $request): JsonResponse
    {
        $query = Utilisateur::with('roles');

        // Filtrer par rôle
        if ($request->has('role')) {
            $query->role($request->role);
        }

        // Filtrer par statut
        if ($request->has('est_actif')) {
            $query->where('est_actif', $request->est_actif);
        }

        $utilisateurs = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('par_page', 20));

        return response()->json([
            'utilisateurs' => $utilisateurs
        ]);
    }

    /**
     * Gestion des produits
     */
    public function produits(Request $request): JsonResponse
    {
        $query = Produit::with(['categorie', 'marque', 'famille', 'fournisseur']);

        // Filtres
        if ($request->has('statut_stock')) {
            $query->where('statut_stock', $request->statut_stock);
        }

        if ($request->has('est_visible')) {
            $query->where('est_visible', $request->est_visible);
        }

        if ($request->has('categorie_id')) {
            $query->where('categorie_id', $request->categorie_id);
        }

        $produits = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('par_page', 20));

        return response()->json([
            'produits' => $produits
        ]);
    }

    /**
     * Gestion des commandes
     */
    public function commandes(Request $request): JsonResponse
    {
        $query = Commande::with(['utilisateur', 'articles.produit']);

        // Filtrer par statut
        if ($request->has('statut')) {
            $query->where('statut', $request->statut);
        }

        // Filtrer par statut de paiement
        if ($request->has('statut_paiement')) {
            $query->where('statut_paiement', $request->statut_paiement);
        }

        // Filtrer par période
        if ($request->has('date_debut')) {
            $query->where('created_at', '>=', $request->date_debut);
        }

        if ($request->has('date_fin')) {
            $query->where('created_at', '<=', $request->date_fin);
        }

        $commandes = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('par_page', 20));

        return response()->json([
            'commandes' => $commandes
        ]);
    }

    /**
     * Statistiques du catalogue
     */
    public function statistiquesCatalogue(): JsonResponse
    {
        $categories = Categorie::withCount('produits')
            ->orderBy('produits_count', 'desc')
            ->get(['id', 'nom', 'produits_count']);

        $marques = Marque::withCount('produits')
            ->orderBy('produits_count', 'desc')
            ->get(['id', 'nom', 'produits_count']);

        return response()->json([
            'categories' => $categories,
            'marques' => $marques,
            'total_categories' => Categorie::count(),
            'total_marques' => Marque::count(),
        ]);
    }

    /**
     * Alertes et notifications
     */
    public function alertes(): JsonResponse
    {
        $alertes = [
            'produits_stock_faible' => Produit::where('statut_stock', 'stock_faible')->count(),
            'produits_rupture' => Produit::where('statut_stock', 'rupture')->count(),
            'commandes_en_attente' => Commande::where('statut', 'en_attente')->count(),
            'commandes_a_expedier' => Commande::where('statut', 'en_preparation')->count(),
        ];

        return response()->json([
            'alertes' => $alertes
        ]);
    }

    /**
     * Calculer le panier moyen
     */
    private function calculerPanierMoyen($periode)
    {
        $commandes = Commande::where('created_at', '>=', now()->subDays($periode))
            ->where('statut', '!=', 'annule')
            ->get();

        if ($commandes->count() === 0) {
            return 0;
        }

        return $commandes->sum('montant_total') / $commandes->count();
    }
}
