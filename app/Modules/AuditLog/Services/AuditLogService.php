<?php

namespace App\Modules\AuditLog\Services;

use App\Modules\AuditLog\Repositories\Contracts\AuditLogRepositoryInterface;
use App\Modules\Shared\Services\BaseService;

class AuditLogService extends BaseService
{
    public function __construct(
        protected AuditLogRepositoryInterface $auditLogs
    ) {
    }

    public function paginate(int $perPage = 10, array $filters = [])
    {
        return $this->auditLogs->paginate($perPage, $filters);
    }

    public function find(string $id)
    {
        return $this->auditLogs->find($id);
    }
}
