<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCartItemRequest;
use App\Http\Requests\UpdateCartItemRequest;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $cart = $this->getUserCart($request->user());
        
        return response()->json([
            'data' => new CartResource($cart),
        ]);
    }

    public function add(StoreCartItemRequest $request): JsonResponse
    {
        $user = $request->user();
        $product = Product::findOrFail($request->product_id);

        if (!$product->is_active || $product->is_out_of_stock) {
            return response()->json([
                'message' => 'Produit non disponible',
            ], 400);
        }

        if ($request->quantity > $product->stock_quantity) {
            return response()->json([
                'message' => 'Quantité demandée supérieure au stock disponible',
                'available_stock' => $product->stock_quantity,
            ], 400);
        }

        $cart = $this->getUserCart($user);

        $existingItem = $cart->items()
            ->where('product_id', $product->id)
            ->where('product_attributes', json_encode($request->get('attributes', [])))
            ->first();

        if ($existingItem) {
            $newQuantity = $existingItem->quantity + $request->quantity;
            
            if ($newQuantity > $product->stock_quantity) {
                return response()->json([
                    'message' => 'Quantité totale supérieure au stock disponible',
                    'available_stock' => $product->stock_quantity,
                    'current_quantity' => $existingItem->quantity,
                ], 400);
            }

            $existingItem->quantity = $newQuantity;
            $existingItem->save();
        } else {
            $cartItem = $cart->items()->create([
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'price' => $product->price,
                'product_attributes' => json_encode($request->get('attributes', [])),
            ]);
        }

        // Update cart totals
        $cart->updateTotals();

        $cart->refresh();

        return response()->json([
            'message' => 'Produit ajouté au panier',
            'data' => new CartResource($cart),
        ]);
    }

    public function update(UpdateCartItemRequest $request, CartItem $cartItem): JsonResponse
    {
        if ($cartItem->cart->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Non autorisé',
            ], 403);
        }

        $product = $cartItem->product;

        if ($request->quantity > $product->stock_quantity) {
            return response()->json([
                'message' => 'Quantité demandée supérieure au stock disponible',
                'available_stock' => $product->stock_quantity,
            ], 400);
        }

        $cartItem->update(['quantity' => $request->quantity]);
        $cartItem->cart->refresh();

        return response()->json([
            'message' => 'Quantité mise à jour',
            'data' => new CartResource($cartItem->cart),
        ]);
    }

    public function remove(Request $request, CartItem $cartItem): JsonResponse
    {
        if ($cartItem->cart->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Non autorisé',
            ], 403);
        }

        $cart = $cartItem->cart;
        $cartItem->delete();
        $cart->refresh();

        return response()->json([
            'message' => 'Produit retiré du panier',
            'data' => new CartResource($cart),
        ]);
    }

    public function clear(Request $request): JsonResponse
    {
        $cart = $this->getUserCart($request->user());
        $cart->items()->delete();
        $cart->refresh();

        return response()->json([
            'message' => 'Panier vidé',
            'data' => new CartResource($cart),
        ]);
    }

    public function count(Request $request): JsonResponse
    {
        $cart = $this->getUserCart($request->user());
        
        return response()->json([
            'count' => $cart->items_count,
            'total' => $cart->formatted_total,
        ]);
    }

    private function getUserCart($user): Cart
    {
        $cart = Cart::firstOrCreate([
            'user_id' => $user->id,
        ], [
            'subtotal' => 0,
            'tax_amount' => 0,
            'total' => 0,
            'currency' => 'EUR',
        ]);

        return $cart;
    }
}
