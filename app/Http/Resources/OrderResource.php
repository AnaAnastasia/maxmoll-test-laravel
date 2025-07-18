<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'customer' => $this->customer,
            'status' => $this->status,
            'created_at' => $this->created_at?->toDateTimeString(),
            'completed_at' => $this->completed_at?->toDateTimeString(),
            'warehouse' => new WarehouseResource($this->whenLoaded('warehouse')),
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
