<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Produit;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProduitController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Produit::active()->inStock();

        // Filtrage par catégorie
        if ($request->has('categorie_id')) {
            $query->byCategory($request->categorie_id);
        }

        // Recherche
        if ($request->has('recherche')) {
            $searchTerm = $request->recherche;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('nom', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('description', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('reference', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('marque', 'LIKE', "%{$searchTerm}%");
            });
        }

        // Tri
        $sortBy = $request->get('tri_par', 'created_at');
        $sortOrder = $request->get('ordre', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $produits = $query->with('categorie')
            ->paginate($request->get('par_page', 12));

        return response()->json([
            'produits' => $produits
        ]);
    }

    public function show($slug): JsonResponse
    {
        $produit = Produit::where('slug', $slug)
            ->with('categorie')
            ->firstOrFail();

        return response()->json([
            'produit' => $produit
        ]);
    }

    public function featured(Request $request): JsonResponse
    {
        $produits = Produit::active()
            ->featured()
            ->inStock()
            ->with('categorie')
            ->take($request->get('limite', 8))
            ->get();

        return response()->json([
            'produits' => $produits
        ]);
    }

    public function search(Request $request): JsonResponse
    {
        $searchTerm = $request->get('q');
        
        if (empty($searchTerm)) {
            return response()->json([
                'message' => 'Le terme de recherche est requis'
            ], 400);
        }

        $produits = Produit::active()
            ->inStock()
            ->where(function ($query) use ($searchTerm) {
                $query->where('nom', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('description', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('reference', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('marque', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('modele', 'LIKE', "%{$searchTerm}%");
            })
            ->with('categorie')
            ->paginate($request->get('limite', 20));

        return response()->json([
            'produits' => $produits
        ]);
    }

    public function byCategory(Request $request, $categoryId): JsonResponse
    {
        $produits = Produit::active()
            ->inStock()
            ->byCategory($categoryId)
            ->with('categorie')
            ->paginate($request->get('par_page', 12));

        return response()->json([
            'produits' => $produits
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'description' => 'required|string',
            'description_courte' => 'nullable|string|max:500',
            'reference' => 'required|string|unique:produits,reference',
            'prix' => 'required|numeric|min:0',
            'prix_compare' => 'nullable|numeric|min:0',
            'quantite_stock' => 'required|integer|min:0',
            'est_actif' => 'boolean',
            'est_en_rupture' => 'boolean',
            'est_mise_en_avant' => 'boolean',
            'image_principale' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'poids' => 'nullable|numeric|min:0',
            'attributs' => 'nullable|array',
            'specifications_techniques' => 'nullable|string',
            'categorie_id' => 'nullable|exists:categories,id',
            'marque' => 'nullable|string|max:100',
            'modele' => 'nullable|string|max:100',
            'garantie' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erreur de validation',
                'erreurs' => $validator->errors()
            ], 422);
        }

        $data = $request->all();
        $data['slug'] = Str::slug($data['nom']);

        $produit = Produit::create($data);

        return response()->json([
            'message' => 'Produit créé avec succès',
            'produit' => $produit
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $produit = Produit::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'description' => 'required|string',
            'description_courte' => 'nullable|string|max:500',
            'reference' => 'required|string|unique:produits,reference,' . $id,
            'prix' => 'required|numeric|min:0',
            'prix_compare' => 'nullable|numeric|min:0',
            'quantite_stock' => 'required|integer|min:0',
            'est_actif' => 'boolean',
            'est_en_rupture' => 'boolean',
            'est_mise_en_avant' => 'boolean',
            'image_principale' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'poids' => 'nullable|numeric|min:0',
            'attributs' => 'nullable|array',
            'specifications_techniques' => 'nullable|string',
            'categorie_id' => 'nullable|exists:categories,id',
            'marque' => 'nullable|string|max:100',
            'modele' => 'nullable|string|max:100',
            'garantie' => 'nullable|string|max:500'
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

        $produit->update($data);

        return response()->json([
            'message' => 'Produit mis à jour avec succès',
            'produit' => $produit
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $produit = Produit::findOrFail($id);
        
        $produit->delete();

        return response()->json([
            'message' => 'Produit supprimé avec succès'
        ]);
    }
}
