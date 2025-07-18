<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function create(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            $order = Order::create([
                'customer' => $data['customer'],
                'warehouse_id' => $data['warehouse_id'],
                'status' => 'active',
                'created_at' => now(),
            ]);

            foreach ($data['items'] as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'count' => $item['count'],
                ]);
            }

            return $order;
        });
    }

    public function update(Order $order, array $data): void
    {
        if ($order->status !== 'active') {
            throw new \Exception('Only active orders can be updated');
        }

        DB::transaction(function () use ($order, $data) {
            $order->update(['customer' => $data['customer']]);
            $order->items()->delete();

            foreach ($data['items'] as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'count' => $item['count'],
                ]);
            }
        });
    }

    public function complete(Order $order): void
    {
        if ($order->status !== 'active') {
            throw new \Exception('Only active orders can be completed');
        }

        DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                $stock = Stock::where([
                    'product_id' => $item->product_id,
                    'warehouse_id' => $order->warehouse_id,
                ])->lockForUpdate()->first();

                if (!$stock || $stock->stock < $item->count) {
                    throw new \Exception("Not enough stock for product ID {$item->product_id}");
                }

                $stock->decrement('stock', $item->count);

                StockMovement::create([
                    'product_id' => $item->product_id,
                    'warehouse_id' => $order->warehouse_id,
                    'delta' => - $item->count,
                    'type' => 'order_complete',
                ]);
            }

            $order->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
        });
    }

    public function cancel(Order $order): void
    {
        if (!in_array($order->status, ['active', 'completed'])) {
            throw new \Exception('Only active or completed orders can be canceled');
        }

        DB::transaction(function () use ($order) {
            if ($order->status === 'completed') {
                foreach ($order->items as $item) {
                    $stock = Stock::where([
                        'product_id' => $item->product_id,
                        'warehouse_id' => $order->warehouse_id,
                    ])->lockForUpdate()->first();

                    if ($stock) {
                        $stock->increment('stock', $item->count);
                    } else {
                        Stock::create([
                            'product_id' => $item->product_id,
                            'warehouse_id' => $order->warehouse_id,
                            'stock' => $item->count,
                        ]);
                    }

                    StockMovement::create([
                        'product_id' => $item->product_id,
                        'warehouse_id' => $order->warehouse_id,
                        'delta' => $item->count,
                        'type' => 'order_cancel',
                    ]);
                }
            }

            $order->update(['status' => 'canceled']);
        });
    }

    public function resume(Order $order): void
    {
        if ($order->status !== 'canceled') {
            throw new \Exception('Only canceled orders can be resumed');
        }

        DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                $stock = Stock::where([
                    'product_id' => $item->product_id,
                    'warehouse_id' => $order->warehouse_id,
                ])->lockForUpdate()->first();

                if (!$stock || $stock->stock < $item->count) {
                    throw new \Exception("Not enough stock to resume order for product ID {$item->product_id}");
                }

                $stock->decrement('stock', $item->count);

                StockMovement::create([
                    'product_id' => $item->product_id,
                    'warehouse_id' => $order->warehouse_id,
                    'delta' => - $item->count,
                    'type' => 'order_resume',
                ]);
            }

            $order->update(['status' => 'active']);
        });
    }
}
