<?php

namespace App\QueryBuilders;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class StockMovementQueryBuilder
{
    /**
     * Применить фильтры к запросу движений товаров.
     *
     * @param Builder $query
     * @param Request $request
     * @return Builder
     */
    public static function apply(Builder $query, Request $request): Builder
    {
        return $query
            ->when($request->filled('product_id'), fn($q) => $q->where('product_id', $request->product_id))
            ->when($request->filled('warehouse_id'), fn($q) => $q->where('warehouse_id', $request->warehouse_id))
            ->when($request->filled('date_from'), fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn($q) => $q->whereDate('created_at', '<=', $request->date_to));
    }
}
