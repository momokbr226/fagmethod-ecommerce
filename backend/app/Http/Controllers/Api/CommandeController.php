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

            // Valider le stock avant de créer la commande
            foreach ($panier->articles as $articlePanier) {
                $produit = $articlePanier->produit;
                
                if ($produit->gestion_stock && $produit->quantite_stock < $articlePanier->quantite) {
                    return response()->json([
                        'message' => "Stock insuffisant pour le produit: {$produit->nom}",
                        'produit' => $produit->nom,
                        'stock_disponible' => $produit->quantite_stock,
                        'quantite_demandee' => $articlePanier->quantite
                    ], 400);
                }
            }

            // Créer la commande
            $commande = Commande::create([
                'utilisateur_id' => $user->id,
                'numero_commande' => Commande::genererNumeroCommande(),
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

            // Créer les articles de commande et décrémenter le stock
            foreach ($panier->articles as $articlePanier) {
                $produit = $articlePanier->produit;
                
                ArticleCommande::create([
                    'commande_id' => $commande->id,
                    'produit_id' => $articlePanier->produit_id,
                    'nom_produit' => $produit->nom,
                    'reference_produit' => $produit->reference,
                    'prix_unitaire' => $articlePanier->prix_unitaire,
                    'quantite' => $articlePanier->quantite,
                    'total_ligne' => $articlePanier->total_ligne,
                    'attributs_produit' => $articlePanier->attributs_produit,
                    'notes_article' => $articlePanier->notes_article
                ]);
                
                // Décrémenter le stock et incrémenter les ventes
                $produit->incrementerVentes($articlePanier->quantite);
            }

            // Vider le panier
            $panier->vider();

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

        // Vérifier les permissions (admin uniquement pour changer le statut)
        if (!$user->hasRole(['admin', 'fournisseur'])) {
            return response()->json([
                'message' => 'Accès non autorisé'
            ], 403);
        }

        $commande = Commande::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'statut' => 'required|in:en_attente,en_preparation,expedie,livre,annule',
            'numero_suivi' => 'nullable|string|max:100',
            'transporteur' => 'nullable|string|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erreur de validation',
                'erreurs' => $validator->errors()
            ], 422);
        }

        $commande->changerStatut($request->statut);
        
        if ($request->has('numero_suivi')) {
            $commande->numero_suivi = $request->numero_suivi;
        }
        
        if ($request->has('transporteur')) {
            $commande->transporteur = $request->transporteur;
        }
        
        $commande->save();

        return response()->json([
            'message' => 'Statut de la commande mis à jour avec succès',
            'commande' => $commande->load(['articles.produit'])
        ]);
    }

    public function annuler(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'message' => 'Utilisateur non authentifié'
            ], 401);
        }

        $commande = Commande::where('utilisateur_id', $user->id)->findOrFail($id);
        
        if (!$commande->peutEtreAnnulee()) {
            return response()->json([
                'message' => 'Cette commande ne peut plus être annulée',
                'statut_actuel' => $commande->statut
            ], 400);
        }

        return DB::transaction(function () use ($commande) {
            // Remettre les produits en stock
            foreach ($commande->articles as $article) {
                $produit = $article->produit;
                if ($produit && $produit->gestion_stock) {
                    $produit->quantite_stock += $article->quantite;
                    $produit->ventes -= $article->quantite;
                    $produit->mettreAJourStatutStock();
                    $produit->save();
                }
            }

            // Changer le statut de la commande
            $commande->changerStatut('annule');

            return response()->json([
                'message' => 'Commande annulée avec succès',
                'commande' => $commande->load(['articles.produit'])
            ]);
        });
    }

    public function historique(Request $request): JsonResponse
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'message' => 'Utilisateur non authentifié'
            ], 401);
        }

        $query = Commande::where('utilisateur_id', $user->id)
            ->with(['articles.produit']);

        // Filtrer par statut
        if ($request->has('statut')) {
            $query->byStatus($request->statut);
        }

        // Filtrer par période
        if ($request->has('periode')) {
            $jours = (int) $request->periode;
            $query->recentes($jours);
        }

        $commandes = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('par_page', 10));

        return response()->json([
            'commandes' => $commandes,
            'statistiques' => [
                'total_commandes' => Commande::where('utilisateur_id', $user->id)->count(),
                'commandes_en_cours' => Commande::where('utilisateur_id', $user->id)->enCours()->count(),
                'montant_total_depense' => Commande::where('utilisateur_id', $user->id)
                    ->where('statut', '!=', 'annule')
                    ->sum('montant_total')
            ]
        ]);
    }
}
