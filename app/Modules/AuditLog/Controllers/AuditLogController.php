<?php

namespace App\Modules\AuditLog\Controllers;

use App\Models\AuditLog;
use App\Modules\AuditLog\Resources\AuditLogResource;
use App\Modules\AuditLog\Services\AuditLogService;
use App\Modules\Shared\Http\Controllers\Api\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AuditLogController extends BaseController
{
    public function __construct(
        protected AuditLogService $service
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', AuditLog::class);

        $paginator = $this->service->paginate(
            $request->integer('limit', 10),
            $request->only(['action', 'auditable_type', 'user_id', 'date_from', 'date_to', 'search', 'sort_by', 'sort_direction'])
        );

        return $this->paginatedResponse(
            AuditLogResource::collection($paginator),
            $paginator,
            'Audit logs retrieved successfully'
        );
    }
}
