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

        return response()->json([
            'panier' => $panier
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

        // Vérifier si le produit est en stock
        if ($produit->quantite_stock < $request->quantite) {
            return response()->json([
                'message' => 'Stock insuffisant pour ce produit'
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
                'prix_unitaire' => $produit->prix
            ]);
        } else {
            // Ajouter un nouvel article
            $panier->articles()->create([
                'produit_id' => $request->produit_id,
                'quantite' => $request->quantite,
                'prix_unitaire' => $produit->prix,
                'attributs_produit' => $request->attributs_produit,
                'notes_article' => $request->notes_article
            ]);
        }

        // Mettre à jour les totaux du panier
        $panier->updateTotals();

        return response()->json([
            'message' => 'Produit ajouté au panier avec succès',
            'panier' => $panier->fresh()
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

        if ($produit->quantite_stock < $nouvelleQuantite) {
            return response()->json([
                'message' => 'Stock insuffisant pour cette quantité'
            ], 400);
        }

        $article->update([
            'quantite' => $nouvelleQuantite
        ]);

        $panier->updateTotals();

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

        $panier->updateTotals();

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
            $panier->articles()->delete();
            $panier->delete();
        }

        return response()->json([
            'message' => 'Panier vidé avec succès'
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
