<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Commande;
use App\Models\ArticleCommande;
use App\Models\Panier;
use App\Models\Paiement;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CommandeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'message' => 'Utilisateur non authentifié'
            ], 401);
        }

        $commandes = Commande::where('utilisateur_id', $user->id)
            ->with(['articles.produit', 'paiements'])
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('par_page', 10));

        return response()->json([
            'commandes' => $commandes
        ]);
    }

    public function show(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'message' => 'Utilisateur non authentifié'
            ], 401);
        }

        $commande = Commande::where('utilisateur_id', $user->id)
            ->with(['articles.produit', 'paiements', 'utilisateur'])
            ->findOrFail($id);

        return response()->json([
            'commande' => $commande
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'message' => 'Utilisateur non authentifié'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'adresse_livraison' => 'required|array',
            'adresse_facturation' => 'nullable|array',
            'mode_paiement' => 'required|in:carte_credit,paypal,virement,cheque,espece',
            'notes_commande' => 'nullable|string|max:1000',
            'notes_livraison' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erreur de validation',
                'erreurs' => $validator->errors()
            ], 422);
        }

        return DB::transaction(function () use ($request, $user) {
            // Obtenir le panier de l'utilisateur
            $panier = $user->panier()->with('articles.produit')->first();
            
            if (!$panier || $panier->articles->isEmpty()) {
                return response()->json([
                    'message' => 'Le panier est vide'
                ], 400);
            }

            // Créer la commande
            $commande = Commande::create([
                'utilisateur_id' => $user->id,
                'numero_commande' => 'CMD-' . strtoupper(Str::random(8)),
                'sous_total' => $panier->sous_total,
                'montant_tva' => $panier->montant_tva,
                'frais_livraison' => $panier->frais_livraison,
                'montant_remise' => $panier->remise,
                'montant_total' => $panier->total,
                'statut' => 'en_attente',
                'statut_paiement' => 'en_attente',
                'mode_paiement' => $request->mode_paiement,
                'adresse_livraison' => $request->adresse_livraison,
                'adresse_facturation' => $request->adresse_facturation ?: $request->adresse_livraison,
                'notes_commande' => $request->notes_commande,
                'notes_livraison' => $request->notes_livraison
            ]);

            // Créer les articles de commande
            foreach ($panier->articles as $articlePanier) {
                ArticleCommande::create([
                    'commande_id' => $commande->id,
                    'produit_id' => $articlePanier->produit_id,
                    'nom_produit' => $articlePanier->produit->nom,
                    'reference_produit' => $articlePanier->produit->reference,
                    'prix_unitaire' => $articlePanier->prix_unitaire,
                    'quantite' => $articlePanier->quantite,
                    'total_ligne' => $articlePanier->total_ligne,
                    'attributs_produit' => $articlePanier->attributs_produit,
                    'notes_article' => $articlePanier->notes_article
                ]);
            }

            // Vider le panier
            $panier->articles()->delete();
            $panier->delete();

            return response()->json([
                'message' => 'Commande créée avec succès',
                'commande' => $commande->load(['articles.produit'])
            ], 201);
        });
    }

    public function updateStatus(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'message' => 'Utilisateur non authentifié'
            ], 401);
        }

        $commande = Commande::where('utilisateur_id', $user->id)->findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'statut' => 'required|in:en_attente,en_preparation,expedie,livre,annule',
            'notes_admin' => 'nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erreur de validation',
                'erreurs' => $validator->errors()
            ], 422);
        }

        $commande->update([
            'statut' => $request->statut,
            'date_expedition' => $request->statut === 'expedie' ? now() : $commande->date_expedition,
            'date_livraison' => $request->statut === 'livre' ? now() : $commande->date_livraison
        ]);

        return response()->json([
            'message' => 'Statut de la commande mis à jour avec succès',
            'commande' => $commande
        ]);
    }
}
