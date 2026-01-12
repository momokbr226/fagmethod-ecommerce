<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $orders = $request->user()
            ->orders()
            ->with(['items.product', 'items.product.category'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'data' => OrderResource::collection($orders),
            'meta' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
            ],
        ]);
    }

    public function show(Request $request, Order $order): JsonResponse
    {
        if ($order->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Non autorisé',
            ], 403);
        }

        $order->load(['items.product', 'items.product.category']);

        return response()->json([
            'data' => new OrderResource($order),
        ]);
    }

    public function store(StoreOrderRequest $request): JsonResponse
    {
        $user = $request->user();

        // Vérifier que les adresses appartiennent à l'utilisateur
        $shippingAddress = Address::where('id', $request->shipping_address_id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $billingAddress = Address::where('id', $request->billing_address_id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        // Récupérer le panier
        $cart = Cart::with('items.product')->where('user_id', $user->id)->first();

        if (!$cart || $cart->items->isEmpty()) {
            return response()->json([
                'message' => 'Panier vide',
            ], 400);
        }

        // Vérifier le stock
        foreach ($cart->items as $item) {
            if ($item->quantity > $item->product->stock_quantity) {
                return response()->json([
                    'message' => 'Stock insuffisant pour le produit: ' . $item->product->name,
                    'product' => [
                        'id' => $item->product->id,
                        'name' => $item->product->name,
                        'available_stock' => $item->product->stock_quantity,
                        'requested_quantity' => $item->quantity,
                    ],
                ], 400);
            }
        }

        return DB::transaction(function () use ($request, $user, $cart, $shippingAddress, $billingAddress) {
            // Créer la commande
            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => 'ORD-' . strtoupper(Str::random(10)),
                'total_amount' => $cart->total,
                'status' => 'pending',
                'payment_status' => 'pending',
                'payment_method' => $request->payment_method,
                'shipping_address' => $shippingAddress->toArray(),
                'billing_address' => $billingAddress->toArray(),
                'notes' => $request->notes,
            ]);

            // Créer les items de commande
            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'product_sku' => $item->product->sku,
                    'price' => $item->price,
                    'quantity' => $item->quantity,
                    'total' => $item->total,
                    'product_attributes' => $item->product_attributes,
                ]);

                // Mettre à jour le stock
                $item->product->decrement('stock_quantity', $item->quantity);
            }

            // Vider le panier
            $cart->items()->delete();
            $cart->update([
                'subtotal' => 0,
                'tax_amount' => 0,
                'total' => 0,
            ]);

            $order->load(['items.product', 'items.product.category']);

            return response()->json([
                'message' => 'Commande créée avec succès',
                'data' => new OrderResource($order),
            ], 201);
        });
    }

    public function cancel(Request $request, Order $order): JsonResponse
    {
        if ($order->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Non autorisé',
            ], 403);
        }

        if (!in_array($order->status, ['pending', 'processing'])) {
            return response()->json([
                'message' => 'Impossible d\'annuler cette commande',
                'status' => $order->status,
            ], 400);
        }

        $order->update([
            'status' => 'cancelled',
            'payment_status' => 'refunded',
        ]);

        // Remettre les produits en stock
        foreach ($order->items as $item) {
            $item->product->increment('stock_quantity', $item->quantity);
        }

        return response()->json([
            'message' => 'Commande annulée avec succès',
            'data' => new OrderResource($order),
        ]);
    }
}
