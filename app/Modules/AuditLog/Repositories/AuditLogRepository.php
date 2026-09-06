<?php

namespace App\Modules\AuditLog\Repositories;

use App\Models\AuditLog;
use App\Modules\AuditLog\Repositories\Contracts\AuditLogRepositoryInterface;
use App\Modules\Shared\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

/**
 * @extends BaseRepository<AuditLog>
 */
class AuditLogRepository extends BaseRepository implements AuditLogRepositoryInterface
{
    public function __construct(AuditLog $model)
    {
        parent::__construct($model);
    }

    /**
     * @return list<string>
     */
    protected function sortableColumns(): array
    {
        return ['action', 'auditable_type', 'created_at'];
    }

    protected function defaultSortColumn(): ?string
    {
        return 'created_at';
    }

    protected function applyCustomFilters(Builder $query, array $filters): Builder
    {
        if (!empty($filters['action'])) {
            $query->where('action', $filters['action']);
        }

        if (!empty($filters['auditable_type'])) {
            $query->where('auditable_type', $filters['auditable_type']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        return $this->applyDateRange($query, 'created_at', $filters);
    }
}
