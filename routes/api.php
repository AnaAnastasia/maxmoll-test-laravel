<?php

use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\StockMovementController;
use App\Http\Controllers\Api\WarehouseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('warehouses')->group(function () {
    Route::get('/', [WarehouseController::class, 'index']);
});

Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
});

Route::prefix('orders')->group(function () {
    Route::get('/', [OrderController::class, 'index']);
    Route::post('/', [OrderController::class, 'store']);
    Route::put('{order}', [OrderController::class, 'update']);

    Route::post('{order}/complete', [OrderController::class, 'complete']);
    Route::post('{order}/cancel', [OrderController::class, 'cancel']);
    Route::post('{order}/resume', [OrderController::class, 'resume']);
});

Route::get('stock_movements', [StockMovementController::class, 'index']);

