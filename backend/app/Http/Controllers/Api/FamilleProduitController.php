<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FamilleProduit;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class FamilleProduitController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = FamilleProduit::query();

        if ($request->has('actives_seulement') && $request->actives_seulement) {
            $query->active();
        }

        $familles = $query->orderByOrder()->get();

        return response()->json([
            'familles' => $familles
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255|unique:familles_produits,nom',
            'code' => 'required|string|max:50|unique:familles_produits,code',
            'description' => 'nullable|string',
            'attributs_specifiques' => 'nullable|array',
            'est_active' => 'boolean',
            'ordre' => 'integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erreur de validation',
                'erreurs' => $validator->errors()
            ], 422);
        }

        $famille = FamilleProduit::create([
            'nom' => $request->nom,
            'slug' => Str::slug($request->nom),
            'code' => strtoupper($request->code),
            'description' => $request->description,
            'attributs_specifiques' => $request->attributs_specifiques,
            'est_active' => $request->est_active ?? true,
            'ordre' => $request->ordre ?? 0,
        ]);

        return response()->json([
            'message' => 'Famille de produits créée avec succès',
            'famille' => $famille
        ], 201);
    }

    public function show($id): JsonResponse
    {
        $famille = FamilleProduit::with('produits')->findOrFail($id);

        return response()->json([
            'famille' => $famille
        ]);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $famille = FamilleProduit::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255|unique:familles_produits,nom,' . $id,
            'code' => 'required|string|max:50|unique:familles_produits,code,' . $id,
            'description' => 'nullable|string',
            'attributs_specifiques' => 'nullable|array',
            'est_active' => 'boolean',
            'ordre' => 'integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erreur de validation',
                'erreurs' => $validator->errors()
            ], 422);
        }

        $famille->update([
            'nom' => $request->nom,
            'slug' => Str::slug($request->nom),
            'code' => strtoupper($request->code),
            'description' => $request->description,
            'attributs_specifiques' => $request->attributs_specifiques,
            'est_active' => $request->est_active ?? $famille->est_active,
            'ordre' => $request->ordre ?? $famille->ordre,
        ]);

        return response()->json([
            'message' => 'Famille de produits mise à jour avec succès',
            'famille' => $famille
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $famille = FamilleProduit::findOrFail($id);
        $famille->delete();

        return response()->json([
            'message' => 'Famille de produits supprimée avec succès'
        ]);
    }
}
