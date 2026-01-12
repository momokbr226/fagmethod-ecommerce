<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = Category::with(['children' => function ($query) {
                $query->active()->orderBy('sort_order', 'asc');
            }])
            ->root()
            ->active()
            ->orderBy('sort_order', 'asc')
            ->get();

        return response()->json([
            'data' => CategoryResource::collection($categories),
        ]);
    }

    public function show(Category $category): JsonResponse
    {
        $category->load(['children', 'products' => function ($query) {
            $query->active()->inStock()->orderBy('created_at', 'desc');
        }]);

        return response()->json([
            'data' => new CategoryResource($category),
        ]);
    }

    public function tree(): JsonResponse
    {
        $categories = Category::with(['children' => function ($query) {
                $query->with(['children' => function ($subQuery) {
                    $subQuery->active()->orderBy('sort_order', 'asc');
                }])
                ->active()
                ->orderBy('sort_order', 'asc');
            }])
            ->root()
            ->active()
            ->orderBy('sort_order', 'asc')
            ->get();

        return response()->json([
            'data' => CategoryResource::collection($categories),
        ]);
    }

    public function withProducts(): JsonResponse
    {
        $categories = Category::with(['products' => function ($query) {
                $query->active()->inStock()->take(6);
            }])
            ->root()
            ->active()
            ->orderBy('sort_order', 'asc')
            ->get();

        return response()->json([
            'data' => CategoryResource::collection($categories),
        ]);
    }
}
