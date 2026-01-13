<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Produit;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProduitControllerV2 extends Controller
{
    /**
     * Liste des produits avec recherche et filtrage avancés
     */
    public function index(Request $request): JsonResponse
    {
        $query = Produit::with(['categorie', 'marque', 'famille', 'fournisseur']);

        // Visibilité (pour clients)
        if (!$request->user() || !$request->user()->hasRole(['admin', 'fournisseur'])) {
            $query->visible()->enStock();
        }

        // Recherche textuelle
        if ($request->has('recherche')) {
            $query->recherche($request->recherche);
        }

        // Filtres
        if ($request->has('categorie_id')) {
            $query->parCategorie($request->categorie_id);
        }

        if ($request->has('marque_id')) {
            $query->parMarque($request->marque_id);
        }

        if ($request->has('famille_id')) {
            $query->parFamille($request->famille_id);
        }

        // Filtres de prix
        if ($request->has('prix_min')) {
            $query->where('prix', '>=', $request->prix_min);
        }

        if ($request->has('prix_max')) {
            $query->where('prix', '<=', $request->prix_max);
        }

        // Filtres spéciaux
        if ($request->has('vedette') && $request->vedette) {
            $query->vedette();
        }

        if ($request->has('nouveau') && $request->nouveau) {
            $query->nouveau();
        }

        if ($request->has('promo') && $request->promo) {
            $query->enPromo();
        }

        // Tri
        $sortBy = $request->get('tri_par', 'created_at');
        $sortOrder = $request->get('ordre', 'desc');
        
        $allowedSorts = ['nom', 'prix', 'created_at', 'vues', 'ventes', 'note_moyenne'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        // Pagination
        $produits = $query->paginate($request->get('par_page', 12));

        return response()->json([
            'produits' => $produits
        ]);
    }

    /**
     * Afficher un produit
     */
    public function show($slug): JsonResponse
    {
        $produit = Produit::where('slug', $slug)
            ->with(['categorie', 'marque', 'famille', 'fournisseur'])
            ->firstOrFail();

        // Incrémenter les vues
        $produit->incrementerVues();

        return response()->json([
            'produit' => $produit
        ]);
    }

    /**
     * Créer un produit (Admin/Fournisseur)
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'reference' => 'required|string|max:255|unique:produits,reference',
            'description' => 'nullable|string',
            'description_courte' => 'nullable|string|max:500',
            'categorie_id' => 'nullable|exists:categories,id',
            'marque_id' => 'nullable|exists:marques,id',
            'famille_id' => 'nullable|exists:familles_produits,id',
            'fournisseur_id' => 'nullable|exists:fournisseurs,id',
            'prix' => 'required|numeric|min:0',
            'prix_promo' => 'nullable|numeric|min:0|lt:prix',
            'date_debut_promo' => 'nullable|date',
            'date_fin_promo' => 'nullable|date|after:date_debut_promo',
            'prix_achat' => 'nullable|numeric|min:0',
            'quantite_stock' => 'integer|min:0',
            'seuil_alerte_stock' => 'integer|min:0',
            'gestion_stock' => 'boolean',
            'poids' => 'nullable|numeric|min:0',
            'longueur' => 'nullable|numeric|min:0',
            'largeur' => 'nullable|numeric|min:0',
            'hauteur' => 'nullable|numeric|min:0',
            'image_principale' => 'nullable|string',
            'images' => 'nullable|array',
            'est_visible' => 'boolean',
            'est_nouveau' => 'boolean',
            'est_vedette' => 'boolean',
            'est_promo' => 'boolean',
            'caracteristiques' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erreur de validation',
                'erreurs' => $validator->errors()
            ], 422);
        }

        $data = $request->all();
        $data['slug'] = Str::slug($request->nom);
        
        $produit = Produit::create($data);
        
        // Mettre à jour le statut du stock
        $produit->mettreAJourStatutStock();
        $produit->save();

        return response()->json([
            'message' => 'Produit créé avec succès',
            'produit' => $produit->load(['categorie', 'marque', 'famille', 'fournisseur'])
        ], 201);
    }

    /**
     * Mettre à jour un produit (Admin/Fournisseur)
     */
    public function update(Request $request, $id): JsonResponse
    {
        $produit = Produit::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'reference' => 'required|string|max:255|unique:produits,reference,' . $id,
            'description' => 'nullable|string',
            'description_courte' => 'nullable|string|max:500',
            'categorie_id' => 'nullable|exists:categories,id',
            'marque_id' => 'nullable|exists:marques,id',
            'famille_id' => 'nullable|exists:familles_produits,id',
            'fournisseur_id' => 'nullable|exists:fournisseurs,id',
            'prix' => 'required|numeric|min:0',
            'prix_promo' => 'nullable|numeric|min:0|lt:prix',
            'date_debut_promo' => 'nullable|date',
            'date_fin_promo' => 'nullable|date|after:date_debut_promo',
            'prix_achat' => 'nullable|numeric|min:0',
            'quantite_stock' => 'integer|min:0',
            'seuil_alerte_stock' => 'integer|min:0',
            'gestion_stock' => 'boolean',
            'poids' => 'nullable|numeric|min:0',
            'longueur' => 'nullable|numeric|min:0',
            'largeur' => 'nullable|numeric|min:0',
            'hauteur' => 'nullable|numeric|min:0',
            'image_principale' => 'nullable|string',
            'images' => 'nullable|array',
            'est_visible' => 'boolean',
            'est_nouveau' => 'boolean',
            'est_vedette' => 'boolean',
            'est_promo' => 'boolean',
            'caracteristiques' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erreur de validation',
                'erreurs' => $validator->errors()
            ], 422);
        }

        $data = $request->all();
        $data['slug'] = Str::slug($request->nom);
        
        $produit->update($data);
        
        // Mettre à jour le statut du stock
        $produit->mettreAJourStatutStock();
        $produit->save();

        return response()->json([
            'message' => 'Produit mis à jour avec succès',
            'produit' => $produit->load(['categorie', 'marque', 'famille', 'fournisseur'])
        ]);
    }

    /**
     * Supprimer un produit (Admin)
     */
    public function destroy($id): JsonResponse
    {
        $produit = Produit::findOrFail($id);
        $produit->delete();

        return response()->json([
            'message' => 'Produit supprimé avec succès'
        ]);
    }

    /**
     * Produits vedettes
     */
    public function vedettes(Request $request): JsonResponse
    {
        $produits = Produit::visible()
            ->vedette()
            ->enStock()
            ->with(['categorie', 'marque'])
            ->take($request->get('limite', 8))
            ->get();

        return response()->json([
            'produits' => $produits
        ]);
    }

    /**
     * Nouveaux produits
     */
    public function nouveaux(Request $request): JsonResponse
    {
        $produits = Produit::visible()
            ->nouveau()
            ->enStock()
            ->with(['categorie', 'marque'])
            ->orderBy('created_at', 'desc')
            ->take($request->get('limite', 8))
            ->get();

        return response()->json([
            'produits' => $produits
        ]);
    }

    /**
     * Produits en promotion
     */
    public function promotions(Request $request): JsonResponse
    {
        $produits = Produit::visible()
            ->enPromo()
            ->enStock()
            ->with(['categorie', 'marque'])
            ->orderBy('created_at', 'desc')
            ->take($request->get('limite', 12))
            ->get();

        return response()->json([
            'produits' => $produits
        ]);
    }

    /**
     * Mettre à jour le stock
     */
    public function updateStock(Request $request, $id): JsonResponse
    {
        $produit = Produit::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'quantite_stock' => 'required|integer|min:0',
            'seuil_alerte_stock' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erreur de validation',
                'erreurs' => $validator->errors()
            ], 422);
        }

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
     * Produits avec stock faible
     */
    public function stockFaible(): JsonResponse
    {
        $produits = Produit::where('gestion_stock', true)
            ->where('statut_stock', 'stock_faible')
            ->with(['categorie', 'marque', 'fournisseur'])
            ->orderBy('quantite_stock', 'asc')
            ->get();

        return response()->json([
            'produits' => $produits
        ]);
    }

    /**
     * Produits en rupture de stock
     */
    public function rupture(): JsonResponse
    {
        $produits = Produit::where('gestion_stock', true)
            ->where('statut_stock', 'rupture')
            ->with(['categorie', 'marque', 'fournisseur'])
            ->get();

        return response()->json([
            'produits' => $produits
        ]);
    }
}
