<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\WarehouseResource;
use App\Models\Warehouse;
use Illuminate\Http\JsonResponse;

class WarehouseController extends Controller
{
    /**
     * Получить список всех складов.
     *
     * @return JsonResponse JSON-ответ с коллекцией складов.
     */
    public function index(): JsonResponse
    {
        $warehouses = Warehouse::all();

        return response()->json(WarehouseResource::collection($warehouses));
    }
}
