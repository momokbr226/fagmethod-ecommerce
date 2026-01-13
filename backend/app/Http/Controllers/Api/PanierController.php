<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Panier;
use App\Models\ArticlePanier;
use App\Models\Produit;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class PanierController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'message' => 'Utilisateur non authentifié'
            ], 401);
        }

        $panier = $user->panier()->with('articles.produit')->first();

        if (!$panier) {
            return response()->json([
                'panier' => [
                    'articles' => [],
                    'sous_total' => 0,
                    'montant_tva' => 0,
                    'total' => 0,
                    'frais_livraison' => 0,
                    'remise' => 0,
                    'devise' => 'EUR'
                ]
            ]);
        }

        // Calculer les totaux si nécessaire
        if ($panier->articles->count() > 0) {
            $panier->calculerTotaux();
        }

        return response()->json([
            'panier' => $panier,
            'nombre_articles' => $panier->nombre_articles,
            'resume' => [
                'sous_total' => $panier->sous_total,
                'montant_tva' => $panier->montant_tva,
                'frais_livraison' => $panier->frais_livraison ?? 0,
                'remise' => $panier->remise ?? 0,
                'total' => $panier->total
            ]
        ]);
    }

    public function addToCart(Request $request): JsonResponse
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'message' => 'Utilisateur non authentifié'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'produit_id' => 'required|exists:produits,id',
            'quantite' => 'required|integer|min:1',
            'attributs_produit' => 'nullable|array',
            'notes_article' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erreur de validation',
                'erreurs' => $validator->errors()
            ], 422);
        }

        $produit = Produit::findOrFail($request->produit_id);

        // Vérifier si le produit est visible et en stock
        if (!$produit->est_visible) {
            return response()->json([
                'message' => 'Ce produit n\'est pas disponible'
            ], 400);
        }

        if ($produit->gestion_stock && $produit->quantite_stock < $request->quantite) {
            return response()->json([
                'message' => 'Stock insuffisant pour ce produit',
                'stock_disponible' => $produit->quantite_stock
            ], 400);
        }

        // Obtenir ou créer le panier de l'utilisateur
        $panier = $user->panier()->firstOrCreate([
            'utilisateur_id' => $user->id,
            'devise' => 'EUR'
        ]);

        // Vérifier si le produit est déjà dans le panier
        $articleExistant = $panier->articles()
            ->where('produit_id', $request->produit_id)
            ->where('attributs_produit', json_encode($request->attributs_produit ?? []))
            ->first();

        if ($articleExistant) {
            // Mettre à jour la quantité
            $nouvelleQuantite = $articleExistant->quantite + $request->quantite;
            
            if ($produit->quantite_stock < $nouvelleQuantite) {
                return response()->json([
                    'message' => 'Stock insuffisant pour cette quantité'
                ], 400);
            }

            $articleExistant->update([
                'quantite' => $nouvelleQuantite,
                'prix_unitaire' => $produit->prix_actuel,
                'notes_article' => $request->notes_article ?? $articleExistant->notes_article
            ]);
        } else {
            // Ajouter un nouvel article
            $panier->articles()->create([
                'produit_id' => $request->produit_id,
                'quantite' => $request->quantite,
                'prix_unitaire' => $produit->prix_actuel,
                'attributs_produit' => $request->attributs_produit,
                'notes_article' => $request->notes_article
            ]);
        }

        // Mettre à jour les totaux du panier
        $panier->calculerTotaux();

        return response()->json([
            'message' => 'Produit ajouté au panier avec succès',
            'panier' => $panier->fresh()->load('articles.produit'),
            'nombre_articles' => $panier->fresh()->nombre_articles
        ]);
    }

    public function updateCartItem(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'message' => 'Utilisateur non authentifié'
            ], 401);
        }

        $panier = $user->panier()->first();
        
        if (!$panier) {
            return response()->json([
                'message' => 'Panier introuvable'
            ], 404);
        }

        $article = $panier->articles()->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'quantite' => 'required|integer|min:1',
            'notes_article' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erreur de validation',
                'erreurs' => $validator->errors()
            ], 422);
        }

        $produit = $article->produit;
        $nouvelleQuantite = $request->quantite;

        if ($produit->gestion_stock && $produit->quantite_stock < $nouvelleQuantite) {
            return response()->json([
                'message' => 'Stock insuffisant pour cette quantité',
                'stock_disponible' => $produit->quantite_stock
            ], 400);
        }

        $article->update([
            'quantite' => $nouvelleQuantite,
            'notes_article' => $request->notes_article ?? $article->notes_article
        ]);

        $panier->calculerTotaux();

        return response()->json([
            'message' => 'Article du panier mis à jour avec succès',
            'panier' => $panier->fresh()->load('articles.produit')
        ]);
    }

    public function removeFromCart(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'message' => 'Utilisateur non authentifié'
            ], 401);
        }

        $panier = $user->panier()->first();
        
        if (!$panier) {
            return response()->json([
                'message' => 'Panier introuvable'
            ], 404);
        }

        $article = $panier->articles()->findOrFail($id);

        $article->delete();

        $panier->calculerTotaux();

        return response()->json([
            'message' => 'Article supprimé du panier avec succès',
            'panier' => $panier->fresh()->load('articles.produit')
        ]);
    }

    public function clearCart(Request $request): JsonResponse
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'message' => 'Utilisateur non authentifié'
            ], 401);
        }

        $panier = $user->panier()->first();
        
        if ($panier) {
            $panier->vider();
        }

        return response()->json([
            'message' => 'Panier vidé avec succès'
        ]);
    }

    public function appliquerCodePromo(Request $request): JsonResponse
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'message' => 'Utilisateur non authentifié'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'code_promo' => 'required|string|max:50'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erreur de validation',
                'erreurs' => $validator->errors()
            ], 422);
        }

        $panier = $user->panier()->first();
        
        if (!$panier) {
            return response()->json([
                'message' => 'Panier introuvable'
            ], 404);
        }

        // TODO: Implémenter la logique de validation du code promo
        // Pour l'instant, exemple simple avec 10% de réduction
        $codePromo = strtoupper($request->code_promo);
        
        if ($codePromo === 'PROMO10') {
            $panier->code_promo = $codePromo;
            $panier->remise = $panier->sous_total * 0.10;
            $panier->calculerTotaux();

            return response()->json([
                'message' => 'Code promo appliqué avec succès',
                'panier' => $panier->fresh()->load('articles.produit'),
                'remise' => $panier->remise
            ]);
        }

        return response()->json([
            'message' => 'Code promo invalide'
        ], 400);
    }

    public function retirerCodePromo(Request $request): JsonResponse
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'message' => 'Utilisateur non authentifié'
            ], 401);
        }

        $panier = $user->panier()->first();
        
        if (!$panier) {
            return response()->json([
                'message' => 'Panier introuvable'
            ], 404);
        }

        $panier->code_promo = null;
        $panier->remise = 0;
        $panier->calculerTotaux();

        return response()->json([
            'message' => 'Code promo retiré avec succès',
            'panier' => $panier->fresh()->load('articles.produit')
        ]);
    }

    public function getCartCount(Request $request): JsonResponse
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'nombre_articles' => 0
            ]);
        }

        $panier = $user->panier()->first();
        
        if (!$panier) {
            return response()->json([
                'nombre_articles' => 0
            ]);
        }

        $nombreArticles = $panier->articles()->sum('quantite');

        return response()->json([
            'nombre_articles' => $nombreArticles
        ]);
    }
}
