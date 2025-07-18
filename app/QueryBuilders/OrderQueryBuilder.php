<?php

namespace App\QueryBuilders;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class OrderQueryBuilder
{
    public static function apply(Builder $query, Request $request): Builder
    {
        return $query
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->when($request->filled('warehouse_id'), fn($q) => $q->where('warehouse_id', $request->warehouse_id))
            ->when($request->filled('date_from'), fn($q) => $q->whereDate('created_at', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn($q) => $q->whereDate('created_at', '<=', $request->date_to));
    }
}
