<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Commande;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class EspaceClientController extends Controller
{
    /**
     * Tableau de bord client
     */
    public function dashboard(Request $request): JsonResponse
    {
        $user = $request->user();

        $commandesRecentes = Commande::where('utilisateur_id', $user->id)
            ->with(['articles.produit'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $statistiques = [
            'total_commandes' => Commande::where('utilisateur_id', $user->id)->count(),
            'commandes_en_cours' => Commande::where('utilisateur_id', $user->id)->enCours()->count(),
            'commandes_livrees' => Commande::where('utilisateur_id', $user->id)
                ->where('statut', 'livre')
                ->count(),
            'montant_total_depense' => Commande::where('utilisateur_id', $user->id)
                ->where('statut', '!=', 'annule')
                ->sum('montant_total'),
            'panier_actuel' => $user->panier ? $user->panier->nombre_articles : 0,
        ];

        return response()->json([
            'utilisateur' => [
                'nom_complet' => $user->nom_complet,
                'email' => $user->email,
                'type_profil' => $user->type_profil,
                'raison_sociale' => $user->raison_sociale,
            ],
            'statistiques' => $statistiques,
            'commandes_recentes' => $commandesRecentes,
        ]);
    }

    /**
     * Profil utilisateur
     */
    public function profil(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'utilisateur' => [
                'id' => $user->id,
                'nom_complet' => $user->nom_complet,
                'email' => $user->email,
                'telephone' => $user->telephone,
                'date_naissance' => $user->date_naissance,
                'sexe' => $user->sexe,
                'adresse' => $user->adresse,
                'ville' => $user->ville,
                'code_postal' => $user->code_postal,
                'pays' => $user->pays,
                'type_profil' => $user->type_profil,
                'raison_sociale' => $user->raison_sociale,
                'siret' => $user->siret,
                'numero_tva' => $user->numero_tva,
                'photo_profil' => $user->photo_profil,
                'created_at' => $user->created_at,
            ]
        ]);
    }

    /**
     * Mettre à jour le profil
     */
    public function updateProfil(Request $request): JsonResponse
    {
        $user = $request->user();

        $rules = [
            'nom_complet' => 'required|string|max:255',
            'telephone' => 'nullable|string|max:20',
            'date_naissance' => 'nullable|date',
            'sexe' => 'nullable|in:homme,femme,autre',
            'adresse' => 'nullable|string',
            'ville' => 'nullable|string|max:100',
            'code_postal' => 'nullable|string|max:10',
            'pays' => 'nullable|string|max:100',
        ];

        // Ajouter les règles pour personne morale
        if ($user->type_profil === 'morale') {
            $rules['raison_sociale'] = 'required|string|max:255';
            $rules['siret'] = 'nullable|string|max:14';
            $rules['numero_tva'] = 'nullable|string|max:20';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erreur de validation',
                'erreurs' => $validator->errors()
            ], 422);
        }

        $user->update($request->only([
            'nom_complet', 'telephone', 'date_naissance', 'sexe',
            'adresse', 'ville', 'code_postal', 'pays',
            'raison_sociale', 'siret', 'numero_tva'
        ]));

        return response()->json([
            'message' => 'Profil mis à jour avec succès',
            'utilisateur' => $user
        ]);
    }

    /**
     * Changer le mot de passe
     */
    public function changerMotDePasse(Request $request): JsonResponse
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'mot_de_passe_actuel' => 'required|string',
            'nouveau_mot_de_passe' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erreur de validation',
                'erreurs' => $validator->errors()
            ], 422);
        }

        if (!Hash::check($request->mot_de_passe_actuel, $user->mot_de_passe)) {
            return response()->json([
                'message' => 'Le mot de passe actuel est incorrect'
            ], 400);
        }

        $user->update([
            'mot_de_passe' => Hash::make($request->nouveau_mot_de_passe)
        ]);

        return response()->json([
            'message' => 'Mot de passe changé avec succès'
        ]);
    }

    /**
     * Mes commandes
     */
    public function mesCommandes(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = Commande::where('utilisateur_id', $user->id)
            ->with(['articles.produit']);

        // Filtrer par statut
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
     * Détails d'une commande
     */
    public function detailCommande(Request $request, $id): JsonResponse
    {
        $user = $request->user();

        $commande = Commande::where('utilisateur_id', $user->id)
            ->with(['articles.produit'])
            ->findOrFail($id);

        return response()->json([
            'commande' => $commande
        ]);
    }
}
