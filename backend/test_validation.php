<?php

// Script de test pour diagnostiquer les erreurs de validation
require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Http\Request;
use App\Http\Controllers\Api\EspaceFournisseurController;
use App\Models\Utilisateur;

// Simuler une requête de création de produit
$request = new Request([
    'nom' => 'Test Produit',
    'reference' => 'TEST-001',
    'description' => 'Description test',
    'prix' => '99.99',
    'quantite_stock' => '10',
    'est_visible' => '1',
    'est_vedette' => '0',
    'est_nouveau' => '0'
]);

// Afficher les données
echo "Données envoyées:\n";
print_r($request->all());

// Tester la validation
$validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
    'nom' => 'required|string|max:255',
    'reference' => 'required|string|max:100|unique:produits,reference',
    'description' => 'nullable|string',
    'description_courte' => 'nullable|string|max:500',
    'prix' => 'required|numeric|min:0',
    'prix_promo' => 'nullable|numeric|min:0',
    'quantite_stock' => 'required|integer|min:0',
    'seuil_alerte_stock' => 'nullable|integer|min:0',
    'categorie_id' => 'nullable|exists:categories,id',
    'marque_id' => 'nullable|exists:marques,id',
    'famille_id' => 'nullable|exists:familles_produits,id',
    'est_visible' => 'nullable|in:0,1,true,false',
    'est_vedette' => 'nullable|in:0,1,true,false',
    'est_nouveau' => 'nullable|in:0,1,true,false',
    'caracteristiques' => 'nullable|array',
    'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
]);

echo "\nRésultat de la validation:\n";
if ($validator->fails()) {
    echo "ERREURS:\n";
    print_r($validator->errors()->toArray());
} else {
    echo "VALIDATION RÉUSSIE!\n";
}
