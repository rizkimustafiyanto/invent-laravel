<?php

namespace App\Modules\Shared\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Shared\Traits\ApiResponse;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;

class BaseController extends Controller
{
    use ApiResponse;

    protected function paginatedResponse(
        mixed $data,
        LengthAwarePaginator $paginator,
        string $message = 'Success',
        int $status = 200
    ): JsonResponse {
        return $this->successWithMeta($data, $message, [
            'page' => $paginator->currentPage(),
            'limit' => $paginator->perPage(),
            'hasNext' => $paginator->hasMorePages(),
            'hasPrevious' => $paginator->currentPage() > 1,
            'totalData' => $paginator->total(),
            'totalPage' => $paginator->lastPage(),
        ], $status);
    }
}
