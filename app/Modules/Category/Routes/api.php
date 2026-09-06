<?php

use App\Modules\Category\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function (): void {
    Route::apiResource('categories', CategoryController::class);
    Route::get('categories/category-option', [CategoryController::class, 'optioncategory']);
});
