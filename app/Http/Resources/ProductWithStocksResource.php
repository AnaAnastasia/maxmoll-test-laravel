<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductWithStocksResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price,
            'stocks' => $this->stocks->map(function ($stock) {
                return [
                    'warehouse' => $stock->warehouse->name,
                    'stock' => $stock->stock,
                ];
            }),
        ];
    }
}
