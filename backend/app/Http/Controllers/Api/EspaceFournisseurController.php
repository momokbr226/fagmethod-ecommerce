<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Produit;
use App\Models\Commande;
use App\Models\ArticleCommande;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

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

    /**
     * Créer un nouveau produit
     */
    public function creerProduit(Request $request): JsonResponse
    {
        $user = $request->user();

        // Log des données reçues pour debug
        \Log::info('Données reçues pour création produit:', $request->all());

        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'reference' => 'required|string|max:100|unique:produits,reference',
            'description' => 'nullable|string',
            'description_courte' => 'nullable|string|max:500',
            'prix' => 'required|numeric|min:0',
            'prix_promo' => 'nullable|numeric|min:0',
            'quantite_stock' => 'required|integer|min:0',
            'seuil_alerte_stock' => 'nullable|integer|min:0',
            'categorie_id' => 'nullable|exists:categories,id',
            'marque_id' => 'nullable|exists:marques,id',
            'famille_id' => 'nullable|exists:familles_produits,id',
            'est_visible' => 'nullable|in:0,1,true,false',
            'est_vedette' => 'nullable|in:0,1,true,false',
            'est_nouveau' => 'nullable|in:0,1,true,false',
            'caracteristiques' => 'nullable|array',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erreur de validation',
                'erreurs' => $validator->errors()
            ], 422);
        }

        $data = $request->all();
        $data['slug'] = Str::slug($data['nom']);
        $data['fournisseur_id'] = $user->id;
        $data['seuil_alerte_stock'] = $data['seuil_alerte_stock'] ?? 5;

        // Convertir les booléens
        $data['est_visible'] = isset($data['est_visible']) ? in_array($data['est_visible'], ['1', 'true', true], true) : true;
        $data['est_vedette'] = isset($data['est_vedette']) ? in_array($data['est_vedette'], ['1', 'true', true], true) : false;
        $data['est_nouveau'] = isset($data['est_nouveau']) ? in_array($data['est_nouveau'], ['1', 'true', true], true) : false;

        // Gérer l'upload d'image
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . Str::slug($data['nom']) . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/produits'), $imageName);
            $data['image_principale'] = '/images/produits/' . $imageName;
        }

        // Déterminer le statut du stock
        if ($data['quantite_stock'] == 0) {
            $data['statut_stock'] = 'rupture';
        } elseif ($data['quantite_stock'] <= $data['seuil_alerte_stock']) {
            $data['statut_stock'] = 'stock_faible';
        } else {
            $data['statut_stock'] = 'en_stock';
        }

        $produit = Produit::create($data);

        return response()->json([
            'message' => 'Produit créé avec succès',
            'produit' => $produit->load(['categorie', 'marque', 'famille'])
        ], 201);
    }

    /**
     * Modifier un produit existant
     */
    public function modifierProduit(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        $produit = Produit::where('fournisseur_id', $user->id)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'reference' => 'required|string|max:100|unique:produits,reference,' . $id,
            'description' => 'nullable|string',
            'description_courte' => 'nullable|string|max:500',
            'prix' => 'required|numeric|min:0',
            'prix_promo' => 'nullable|numeric|min:0',
            'quantite_stock' => 'required|integer|min:0',
            'seuil_alerte_stock' => 'nullable|integer|min:0',
            'categorie_id' => 'nullable|exists:categories,id',
            'marque_id' => 'nullable|exists:marques,id',
            'famille_id' => 'nullable|exists:familles_produits,id',
            'est_visible' => 'nullable|in:0,1,true,false',
            'est_vedette' => 'nullable|in:0,1,true,false',
            'est_nouveau' => 'nullable|in:0,1,true,false',
            'caracteristiques' => 'nullable|array',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erreur de validation',
                'erreurs' => $validator->errors()
            ], 422);
        }

        $data = $request->all();
        if ($request->has('nom')) {
            $data['slug'] = Str::slug($request->nom);
        }

        // Gérer l'upload d'image
        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image si elle existe
            if ($produit->image_principale && file_exists(public_path($produit->image_principale))) {
                unlink(public_path($produit->image_principale));
            }
            
            $image = $request->file('image');
            $imageName = time() . '_' . Str::slug($request->nom ?? $produit->nom) . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/produits'), $imageName);
            $data['image_principale'] = '/images/produits/' . $imageName;
        }

        // Mettre à jour le statut du stock si nécessaire
        if ($request->has('quantite_stock')) {
            $seuil = $request->get('seuil_alerte_stock', $produit->seuil_alerte_stock);
            if ($data['quantite_stock'] == 0) {
                $data['statut_stock'] = 'rupture';
            } elseif ($data['quantite_stock'] <= $seuil) {
                $data['statut_stock'] = 'stock_faible';
            } else {
                $data['statut_stock'] = 'en_stock';
            }
        }

        $produit->update($data);

        return response()->json([
            'message' => 'Produit modifié avec succès',
            'produit' => $produit->load(['categorie', 'marque', 'famille'])
        ]);
    }

    /**
     * Supprimer un produit
     */
    public function supprimerProduit(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        $produit = Produit::where('fournisseur_id', $user->id)->findOrFail($id);

        // Vérifier si le produit a des commandes associées
        $hasOrders = $produit->articlesCommande()->exists();
        
        if ($hasOrders) {
            return response()->json([
                'message' => 'Impossible de supprimer ce produit car il est associé à des commandes. Vous pouvez le masquer à la place.'
            ], 400);
        }

        $produit->delete();

        return response()->json([
            'message' => 'Produit supprimé avec succès'
        ]);
    }
}
