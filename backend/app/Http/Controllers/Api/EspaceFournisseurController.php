<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Produit;
use App\Models\Commande;
use App\Models\ArticleCommande;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class EspaceFournisseurController extends Controller
{
    /**
     * Tableau de bord fournisseur
     */
    public function dashboard(Request $request): JsonResponse
    {
        $user = $request->user();

        // Statistiques produits
        $totalProduits = Produit::where('fournisseur_id', $user->id)->count();
        $produitsActifs = Produit::where('fournisseur_id', $user->id)
            ->where('est_visible', true)
            ->count();
        $produitsStockFaible = Produit::where('fournisseur_id', $user->id)
            ->where('statut_stock', 'stock_faible')
            ->count();
        $produitsRupture = Produit::where('fournisseur_id', $user->id)
            ->where('statut_stock', 'rupture')
            ->count();

        // Statistiques ventes (via les articles de commande)
        $ventesStats = ArticleCommande::whereHas('produit', function($q) use ($user) {
                $q->where('fournisseur_id', $user->id);
            })
            ->whereHas('commande', function($q) {
                $q->where('statut', '!=', 'annule');
            })
            ->select(
                DB::raw('COUNT(*) as total_ventes'),
                DB::raw('SUM(quantite) as quantite_vendue'),
                DB::raw('SUM(total_ligne) as chiffre_affaires')
            )
            ->first();

        // Produits les plus vendus
        $produitsPopulaires = Produit::where('fournisseur_id', $user->id)
            ->orderBy('ventes', 'desc')
            ->take(5)
            ->get(['id', 'nom', 'reference', 'ventes', 'quantite_stock']);

        return response()->json([
            'statistiques' => [
                'total_produits' => $totalProduits,
                'produits_actifs' => $produitsActifs,
                'produits_stock_faible' => $produitsStockFaible,
                'produits_rupture' => $produitsRupture,
                'total_ventes' => $ventesStats->total_ventes ?? 0,
                'quantite_vendue' => $ventesStats->quantite_vendue ?? 0,
                'chiffre_affaires' => $ventesStats->chiffre_affaires ?? 0,
            ],
            'produits_populaires' => $produitsPopulaires,
        ]);
    }

    /**
     * Liste des produits du fournisseur
     */
    public function mesProduits(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = Produit::where('fournisseur_id', $user->id)
            ->with(['categorie', 'marque', 'famille']);

        // Filtres
        if ($request->has('statut_stock')) {
            $query->where('statut_stock', $request->statut_stock);
        }

        if ($request->has('est_visible')) {
            $query->where('est_visible', $request->est_visible);
        }

        $produits = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('par_page', 20));

        return response()->json([
            'produits' => $produits
        ]);
    }

    /**
     * Produits en stock faible
     */
    public function produitsStockFaible(Request $request): JsonResponse
    {
        $user = $request->user();

        $produits = Produit::where('fournisseur_id', $user->id)
            ->where('statut_stock', 'stock_faible')
            ->with(['categorie', 'marque'])
            ->orderBy('quantite_stock', 'asc')
            ->get();

        return response()->json([
            'produits' => $produits
        ]);
    }

    /**
     * Produits en rupture
     */
    public function produitsRupture(Request $request): JsonResponse
    {
        $user = $request->user();

        $produits = Produit::where('fournisseur_id', $user->id)
            ->where('statut_stock', 'rupture')
            ->with(['categorie', 'marque'])
            ->get();

        return response()->json([
            'produits' => $produits
        ]);
    }

    /**
     * Mettre à jour le stock d'un produit
     */
    public function updateStock(Request $request, $id): JsonResponse
    {
        $user = $request->user();

        $produit = Produit::where('fournisseur_id', $user->id)->findOrFail($id);

        $produit->quantite_stock = $request->quantite_stock;
        if ($request->has('seuil_alerte_stock')) {
            $produit->seuil_alerte_stock = $request->seuil_alerte_stock;
        }

        $produit->mettreAJourStatutStock();
        $produit->save();

        return response()->json([
            'message' => 'Stock mis à jour avec succès',
            'produit' => $produit
        ]);
    }

    /**
     * Commandes contenant les produits du fournisseur
     */
    public function commandesProduits(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = Commande::whereHas('articles.produit', function($q) use ($user) {
                $q->where('fournisseur_id', $user->id);
            })
            ->with(['articles' => function($q) use ($user) {
                $q->whereHas('produit', function($q2) use ($user) {
                    $q2->where('fournisseur_id', $user->id);
                })->with('produit');
            }, 'utilisateur']);

        if ($request->has('statut')) {
            $query->where('statut', $request->statut);
        }

        $commandes = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('par_page', 10));

        return response()->json([
            'commandes' => $commandes
        ]);
    }

    /**
     * Statistiques de ventes
     */
    public function statistiquesVentes(Request $request): JsonResponse
    {
        $user = $request->user();

        $periode = $request->get('periode', 30); // 30 jours par défaut

        $ventes = ArticleCommande::whereHas('produit', function($q) use ($user) {
                $q->where('fournisseur_id', $user->id);
            })
            ->whereHas('commande', function($q) use ($periode) {
                $q->where('created_at', '>=', now()->subDays($periode))
                  ->where('statut', '!=', 'annule');
            })
            ->with('produit')
            ->get();

        $statistiques = [
            'total_ventes' => $ventes->count(),
            'quantite_totale' => $ventes->sum('quantite'),
            'chiffre_affaires' => $ventes->sum('total_ligne'),
            'panier_moyen' => $ventes->count() > 0 ? $ventes->sum('total_ligne') / $ventes->count() : 0,
        ];

        // Ventes par produit
        $ventesParProduit = $ventes->groupBy('produit_id')->map(function($items) {
            return [
                'produit' => $items->first()->produit->nom,
                'quantite' => $items->sum('quantite'),
                'montant' => $items->sum('total_ligne'),
            ];
        })->values();

        return response()->json([
            'periode' => $periode,
            'statistiques' => $statistiques,
            'ventes_par_produit' => $ventesParProduit,
        ]);
    }
}
