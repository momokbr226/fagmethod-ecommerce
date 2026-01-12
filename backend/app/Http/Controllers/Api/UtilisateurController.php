<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Utilisateur;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UtilisateurController extends Controller
{
    public function profile(Request $request): JsonResponse
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
                'photo_profil' => $user->photo_profil,
                'preferences' => $user->preferences,
                'notes' => $user->notes,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at
            ]
        ]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $validator = Validator::make($request->all(), [
            'nom_complet' => 'required|string|max:255',
            'telephone' => 'nullable|string|max:20',
            'date_naissance' => 'nullable|date',
            'sexe' => 'nullable|in:homme,femme,autre',
            'adresse' => 'nullable|string|max:500',
            'ville' => 'nullable|string|max:100',
            'code_postal' => 'nullable|string|max:10',
            'pays' => 'nullable|string|max:100',
            'photo_profil' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erreur de validation',
                'erreurs' => $validator->errors()
            ], 422);
        }

        $user->update($request->all());

        return response()->json([
            'message' => 'Profil mis à jour avec succès',
            'utilisateur' => $user
        ]);
    }

    public function changePassword(Request $request): JsonResponse
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

    public function getAddresses(Request $request): JsonResponse
    {
        $user = $request->user();
        $adresses = $user->adresses()->orderBy('est_par_defaut', 'desc')->get();

        return response()->json([
            'adresses' => $adresses
        ]);
    }

    public function storeAddress(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:livraison,facturation',
            'prenom' => 'required|string|max:100',
            'nom' => 'required|string|max:100',
            'entreprise' => 'nullable|string|max:255',
            'adresse_ligne_1' => 'required|string|max:255',
            'adresse_ligne_2' => 'nullable|string|max:255',
            'ville' => 'required|string|max:100',
            'code_postal' => 'required|string|max:10',
            'pays' => 'required|string|max:100',
            'telephone' => 'nullable|string|max:20',
            'instructions_livraison' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erreur de validation',
                'erreurs' => $validator->errors()
            ], 422);
        }

        $adresse = $user->adresses()->create($request->all());

        return response()->json([
            'message' => 'Adresse ajoutée avec succès',
            'adresse' => $adresse
        ], 201);
    }

    public function updateAddress(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        $adresse = $user->adresses()->findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:livraison,facturation',
            'prenom' => 'required|string|max:100',
            'nom' => 'required|string|max:100',
            'entreprise' => 'nullable|string|max:255',
            'adresse_ligne_1' => 'required|string|max:255',
            'adresse_ligne_2' => 'nullable|string|max:255',
            'ville' => 'required|string|max:100',
            'code_postal' => 'required|string|max:10',
            'pays' => 'required|string|max:100',
            'telephone' => 'nullable|string|max:20',
            'instructions_livraison' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erreur de validation',
                'erreurs' => $validator->errors()
            ], 422);
        }

        $adresse->update($request->all());

        return response()->json([
            'message' => 'Adresse mise à jour avec succès',
            'adresse' => $adresse
        ]);
    }

    public function deleteAddress(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        $adresse = $user->adresses()->findOrFail($id);
        
        $adresse->delete();

        return response()->json([
            'message' => 'Adresse supprimée avec succès'
        ]);
    }
}
