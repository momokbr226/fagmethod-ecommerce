<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Utilisateur;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:utilisateurs,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erreur de validation',
                'erreurs' => $validator->errors()
            ], 422);
        }

        $user = Utilisateur::create([
            'nom_complet' => $request->name,
            'email' => $request->email,
            'mot_de_passe' => Hash::make($request->password),
            'est_actif' => true,
        ]);
        
        $token = $user->createToken('auth_token')->plainTextToken;
        
        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->nom_complet,
                'email' => $user->email,
            ],
            'token' => $token,
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erreur de validation',
                'erreurs' => $validator->errors()
            ], 422);
        }

        $user = Utilisateur::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->mot_de_passe)) {
            return response()->json([
                'message' => 'Identifiants invalides',
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->nom_complet,
                'email' => $user->email,
            ],
            'token' => $token,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Déconnexion réussie',
        ]);
    }

    public function profile(Request $request): JsonResponse
    {
        $user = $request->user()->load(['adresses']);

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->nom_complet,
                'email' => $user->email,
                'telephone' => $user->telephone,
                'created_at' => $user->created_at,
                'adresses' => $user->adresses->map(function ($adresse) {
                    return [
                        'id' => $adresse->id,
                        'type' => $adresse->type_adresse,
                        'nom_complet' => $adresse->nom_destinataire,
                        'adresse_complete' => $adresse->adresse_complete,
                        'telephone' => $adresse->telephone,
                        'est_par_defaut' => $adresse->est_par_defaut,
                    ];
                }),
            ],
        ]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:utilisateurs,email,' . $request->user()->id,
            'telephone' => 'sometimes|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erreur de validation',
                'erreurs' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        
        $updateData = [];
        if ($request->has('name')) $updateData['nom_complet'] = $request->name;
        if ($request->has('email')) $updateData['email'] = $request->email;
        if ($request->has('telephone')) $updateData['telephone'] = $request->telephone;
        
        $user->update($updateData);

        return response()->json([
            'message' => 'Profil mis à jour avec succès',
            'user' => [
                'id' => $user->id,
                'name' => $user->nom_complet,
                'email' => $user->email,
            ],
        ]);
    }

    public function changePassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erreur de validation',
                'erreurs' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->mot_de_passe)) {
            return response()->json([
                'message' => 'Le mot de passe actuel est incorrect',
            ], 422);
        }

        $user->update([
            'mot_de_passe' => Hash::make($request->password),
        ]);

        return response()->json([
            'message' => 'Mot de passe changé avec succès',
        ]);
    }
}
