<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Parametre;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class ParametreController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Parametre::query();

        if ($request->has('groupe')) {
            $query->byGroupe($request->groupe);
        }

        if ($request->has('actifs_seulement') && $request->actifs_seulement) {
            $query->actif();
        }

        $parametres = $query->orderBy('groupe')->orderBy('ordre')->get();

        return response()->json([
            'parametres' => $parametres
        ]);
    }

    public function groupes(): JsonResponse
    {
        $groupes = Parametre::actif()
            ->select('groupe')
            ->distinct()
            ->orderBy('groupe')
            ->pluck('groupe');

        return response()->json([
            'groupes' => $groupes
        ]);
    }

    public function byGroupe($groupe): JsonResponse
    {
        $parametres = Parametre::byGroupe($groupe)
            ->actif()
            ->orderBy('ordre')
            ->get();

        return response()->json([
            'parametres' => $parametres
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'cle' => 'required|string|max:255|unique:parametres,cle',
            'groupe' => 'required|string|max:255',
            'libelle' => 'required|string|max:255',
            'valeur' => 'nullable|string',
            'type' => 'required|in:string,integer,boolean,json',
            'description' => 'nullable|string',
            'ordre' => 'integer|min:0',
            'est_modifiable' => 'boolean',
            'est_actif' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erreur de validation',
                'erreurs' => $validator->errors()
            ], 422);
        }

        $parametre = Parametre::create($request->all());

        return response()->json([
            'message' => 'Paramètre créé avec succès',
            'parametre' => $parametre
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $parametre = Parametre::findOrFail($id);

        if (!$parametre->est_modifiable) {
            return response()->json([
                'message' => 'Ce paramètre n\'est pas modifiable'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'cle' => 'required|string|max:255|unique:parametres,cle,' . $id,
            'groupe' => 'required|string|max:255',
            'libelle' => 'required|string|max:255',
            'valeur' => 'nullable|string',
            'type' => 'required|in:string,integer,boolean,json',
            'description' => 'nullable|string',
            'ordre' => 'integer|min:0',
            'est_actif' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erreur de validation',
                'erreurs' => $validator->errors()
            ], 422);
        }

        $parametre->update($request->all());

        return response()->json([
            'message' => 'Paramètre mis à jour avec succès',
            'parametre' => $parametre
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $parametre = Parametre::findOrFail($id);

        if (!$parametre->est_modifiable) {
            return response()->json([
                'message' => 'Ce paramètre n\'est pas supprimable'
            ], 403);
        }

        $parametre->delete();

        return response()->json([
            'message' => 'Paramètre supprimé avec succès'
        ]);
    }

    public function getValue($cle): JsonResponse
    {
        $valeur = Parametre::get($cle);

        if ($valeur === null) {
            return response()->json([
                'message' => 'Paramètre non trouvé'
            ], 404);
        }

        return response()->json([
            'cle' => $cle,
            'valeur' => $valeur
        ]);
    }

    public function setValue(Request $request, $cle): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'valeur' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erreur de validation',
                'erreurs' => $validator->errors()
            ], 422);
        }

        $success = Parametre::set($cle, $request->valeur);

        if (!$success) {
            return response()->json([
                'message' => 'Impossible de modifier ce paramètre'
            ], 403);
        }

        return response()->json([
            'message' => 'Paramètre mis à jour avec succès',
            'cle' => $cle,
            'valeur' => $request->valeur
        ]);
    }
}
