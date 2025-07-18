<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductWithStocksResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function index(): JsonResponse
    {
        $products = Product::with(['stocks.warehouse'])->get();

        return response()->json(ProductWithStocksResource::collection($products));
    }
}
