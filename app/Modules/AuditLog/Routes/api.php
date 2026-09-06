<?php

use App\Modules\AuditLog\Controllers\AuditLogController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function (): void {
    Route::get('audit-logs', [AuditLogController::class, 'index']);
});
