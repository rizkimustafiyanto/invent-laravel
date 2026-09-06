<?php

use App\Modules\Product\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function (): void {
    Route::apiResource('products', ProductController::class);
    Route::get('products-export', [ProductController::class, 'export']);
    Route::get('products-template', [ProductController::class, 'template']);
    Route::post('products-import', [ProductController::class, 'import']);
});
