<?php

namespace App\Modules\Shared\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

abstract class BaseService
{
    protected function transaction(callable $callback)
    {
        return DB::transaction($callback);
    }

    protected function audit(string $action, Model $model, array $oldValues = [], array $newValues = []): AuditLog
    {
        return AuditLog::query()->create([
            'auditable_type' => $model::class,
            'auditable_id' => $model->getKey(),
            'action' => $action,
            'old_values' => $oldValues ?: null,
            'new_values' => $newValues ?: null,
            'user_id' => Auth::id(),
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
        ]);
    }
}
