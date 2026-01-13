<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Fournisseur;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class FournisseurController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Fournisseur::query();

        if ($request->has('actifs_seulement') && $request->actifs_seulement) {
            $query->actif();
        }

        $fournisseurs = $query->orderBy('nom')->get();

        return response()->json([
            'fournisseurs' => $fournisseurs
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255|unique:fournisseurs,nom',
            'raison_sociale' => 'required|string|max:255',
            'code_fournisseur' => 'required|string|max:50|unique:fournisseurs,code_fournisseur',
            'siret' => 'nullable|string|size:14|unique:fournisseurs,siret',
            'numero_tva' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string',
            'ville' => 'nullable|string|max:100',
            'code_postal' => 'nullable|string|max:10',
            'pays' => 'nullable|string|max:100',
            'nom_contact' => 'nullable|string|max:255',
            'prenom_contact' => 'nullable|string|max:255',
            'fonction_contact' => 'nullable|string|max:100',
            'email_contact' => 'nullable|email',
            'telephone_contact' => 'nullable|string|max:20',
            'delai_livraison_moyen' => 'nullable|numeric|min:0',
            'montant_minimum_commande' => 'nullable|numeric|min:0',
            'conditions_paiement' => 'nullable|in:comptant,30_jours,60_jours,90_jours',
            'notes' => 'nullable|string',
            'est_actif' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erreur de validation',
                'erreurs' => $validator->errors()
            ], 422);
        }

        $fournisseur = Fournisseur::create($request->all());

        return response()->json([
            'message' => 'Fournisseur créé avec succès',
            'fournisseur' => $fournisseur
        ], 201);
    }

    public function show($id): JsonResponse
    {
        $fournisseur = Fournisseur::with('produits')->findOrFail($id);

        return response()->json([
            'fournisseur' => $fournisseur
        ]);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $fournisseur = Fournisseur::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255|unique:fournisseurs,nom,' . $id,
            'raison_sociale' => 'required|string|max:255',
            'code_fournisseur' => 'required|string|max:50|unique:fournisseurs,code_fournisseur,' . $id,
            'siret' => 'nullable|string|size:14|unique:fournisseurs,siret,' . $id,
            'numero_tva' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string',
            'ville' => 'nullable|string|max:100',
            'code_postal' => 'nullable|string|max:10',
            'pays' => 'nullable|string|max:100',
            'nom_contact' => 'nullable|string|max:255',
            'prenom_contact' => 'nullable|string|max:255',
            'fonction_contact' => 'nullable|string|max:100',
            'email_contact' => 'nullable|email',
            'telephone_contact' => 'nullable|string|max:20',
            'delai_livraison_moyen' => 'nullable|numeric|min:0',
            'montant_minimum_commande' => 'nullable|numeric|min:0',
            'conditions_paiement' => 'nullable|in:comptant,30_jours,60_jours,90_jours',
            'notes' => 'nullable|string',
            'est_actif' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erreur de validation',
                'erreurs' => $validator->errors()
            ], 422);
        }

        $fournisseur->update($request->all());

        return response()->json([
            'message' => 'Fournisseur mis à jour avec succès',
            'fournisseur' => $fournisseur
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $fournisseur = Fournisseur::findOrFail($id);
        $fournisseur->delete();

        return response()->json([
            'message' => 'Fournisseur supprimé avec succès'
        ]);
    }
}
