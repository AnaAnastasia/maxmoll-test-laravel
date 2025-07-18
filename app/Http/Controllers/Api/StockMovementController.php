<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StockMovementResource;
use App\Models\StockMovement;
use App\QueryBuilders\StockMovementQueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class StockMovementController extends Controller
{
    /**
     * Получить список движений товаров с фильтрацией и пагинацией.
     *
     * @param Request $request HTTP-запрос фильтрами
     * @return AnonymousResourceCollection Коллекция ресурсов StockMovement с постраничной навигацией
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = StockMovement::with(['product', 'warehouse']);

        $query = StockMovementQueryBuilder::apply($query, $request);

        $movements = $query->latest('created_at')->paginate($request->input('per_page', 10));

        return StockMovementResource::collection($movements);
    }
}
