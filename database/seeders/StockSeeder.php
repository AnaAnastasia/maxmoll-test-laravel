<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::all();
        $warehouses = Warehouse::all();

        foreach ($products as $product) {
            foreach ($warehouses as $warehouse) {
                DB::table('stocks')->insert([
                    'product_id' => $product->id,
                    'warehouse_id' => $warehouse->id,
                    'stock' => rand(10, 100),
                ]);
            }
        }
    }
}
