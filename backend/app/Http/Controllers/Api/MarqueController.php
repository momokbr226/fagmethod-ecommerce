<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Marque;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class MarqueController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Marque::query();

        if ($request->has('actives_seulement') && $request->actives_seulement) {
            $query->active();
        }

        $marques = $query->orderByOrder()->get();

        return response()->json([
            'marques' => $marques
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255|unique:marques,nom',
            'description' => 'nullable|string',
            'logo' => 'nullable|string',
            'site_web' => 'nullable|url',
            'pays_origine' => 'nullable|string|max:100',
            'est_active' => 'boolean',
            'ordre' => 'integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erreur de validation',
                'erreurs' => $validator->errors()
            ], 422);
        }

        $marque = Marque::create([
            'nom' => $request->nom,
            'slug' => Str::slug($request->nom),
            'description' => $request->description,
            'logo' => $request->logo,
            'site_web' => $request->site_web,
            'pays_origine' => $request->pays_origine,
            'est_active' => $request->est_active ?? true,
            'ordre' => $request->ordre ?? 0,
        ]);

        return response()->json([
            'message' => 'Marque créée avec succès',
            'marque' => $marque
        ], 201);
    }

    public function show($id): JsonResponse
    {
        $marque = Marque::with('produits')->findOrFail($id);

        return response()->json([
            'marque' => $marque
        ]);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $marque = Marque::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255|unique:marques,nom,' . $id,
            'description' => 'nullable|string',
            'logo' => 'nullable|string',
            'site_web' => 'nullable|url',
            'pays_origine' => 'nullable|string|max:100',
            'est_active' => 'boolean',
            'ordre' => 'integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erreur de validation',
                'erreurs' => $validator->errors()
            ], 422);
        }

        $marque->update([
            'nom' => $request->nom,
            'slug' => Str::slug($request->nom),
            'description' => $request->description,
            'logo' => $request->logo,
            'site_web' => $request->site_web,
            'pays_origine' => $request->pays_origine,
            'est_active' => $request->est_active ?? $marque->est_active,
            'ordre' => $request->ordre ?? $marque->ordre,
        ]);

        return response()->json([
            'message' => 'Marque mise à jour avec succès',
            'marque' => $marque
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $marque = Marque::findOrFail($id);
        $marque->delete();

        return response()->json([
            'message' => 'Marque supprimée avec succès'
        ]);
    }
}
