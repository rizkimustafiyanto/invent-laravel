<?php

use App\Modules\Sale\Controllers\SaleController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function (): void {
    Route::apiResource('sales', SaleController::class);
    Route::get('sales-export', [SaleController::class, 'export']);
});
