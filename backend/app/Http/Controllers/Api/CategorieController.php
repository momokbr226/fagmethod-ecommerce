<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CategorieController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $categories = Categorie::active()
            ->orderByOrder()
            ->withCount('produits')
            ->get();

        return response()->json([
            'categories' => $categories
        ]);
    }

    public function show($slug): JsonResponse
    {
        $categorie = Categorie::where('slug', $slug)
            ->with(['produits' => function ($query) {
                $query->visible()->enStock()->take(12);
            }])
            ->firstOrFail();

        return response()->json([
            'categorie' => $categorie
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'couleur' => 'nullable|string|max:7',
            'ordre_affichage' => 'nullable|integer|min:0',
            'categorie_parente_id' => 'nullable|exists:categories,id',
            'meta_description' => 'nullable|string|max:160',
            'meta_titre' => 'nullable|string|max:60'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erreur de validation',
                'erreurs' => $validator->errors()
            ], 422);
        }

        $data = $request->all();
        $data['slug'] = Str::slug($data['nom']);

        $categorie = Categorie::create($data);

        return response()->json([
            'message' => 'Catégorie créée avec succès',
            'categorie' => $categorie
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $categorie = Categorie::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'couleur' => 'nullable|string|max:7',
            'ordre_affichage' => 'nullable|integer|min:0',
            'categorie_parente_id' => 'nullable|exists:categories,id',
            'meta_description' => 'nullable|string|max:160',
            'meta_titre' => 'nullable|string|max:60'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erreur de validation',
                'erreurs' => $validator->errors()
            ], 422);
        }

        $data = $request->all();
        if ($request->has('nom')) {
            $data['slug'] = Str::slug($request->nom);
        }

        $categorie->update($data);

        return response()->json([
            'message' => 'Catégorie mise à jour avec succès',
            'categorie' => $categorie
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $categorie = Categorie::findOrFail($id);
        
        // Vérifier si des sous-catégories existent
        if ($categorie->enfants()->count() > 0) {
            return response()->json([
                'message' => 'Impossible de supprimer cette catégorie car elle contient des sous-catégories'
            ], 400);
        }

        $categorie->delete();

        return response()->json([
            'message' => 'Catégorie supprimée avec succès'
        ]);
    }

    public function featured(): JsonResponse
    {
        $categories = Categorie::active()
            ->whereHas('produits', function ($query) {
                $query->where('est_vedette', true);
            })
            ->withCount(['produits' => function ($query) {
                $query->where('est_vedette', true);
            }])
            ->orderBy('ordre_affichage', 'asc')
            ->get();

        return response()->json([
            'categories' => $categories
        ]);
    }
}
