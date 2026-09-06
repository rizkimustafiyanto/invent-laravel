<?php

use App\Modules\SaleDetail\Controllers\SaleDetailController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function (): void {
    Route::apiResource('saledetail', SaleDetailController::class);
});
