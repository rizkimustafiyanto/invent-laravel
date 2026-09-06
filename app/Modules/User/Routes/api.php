<?php

use App\Modules\User\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function (): void {
    Route::get('users', [UserController::class, 'index'])->middleware('role:super_admin');
    Route::post('users', [UserController::class, 'store'])->middleware('role:super_admin');
    Route::get('users/role-option', [UserController::class, 'optionrole']);
    Route::get('users/{user}', [UserController::class, 'show']);
    Route::put('users/{user}', [UserController::class, 'update']);
    Route::patch('users/{user}', [UserController::class, 'update']);
    Route::delete('users/{user}', [UserController::class, 'destroy'])->middleware('role:super_admin');
});
