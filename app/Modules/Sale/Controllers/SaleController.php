<?php

namespace App\Modules\Sale\Controllers;

use App\Models\Sale;
use App\Modules\Sale\DTOs\CreateSaleDTO;
use App\Modules\Sale\DTOs\UpdateSaleDTO;
use App\Modules\Sale\Requests\ListSaleRequest;
use App\Modules\Sale\Requests\StoreSaleRequest;
use App\Modules\Sale\Requests\UpdateSaleRequest;
use App\Modules\Sale\Resources\SaleResource;
use App\Modules\Sale\Services\SaleService;
use App\Modules\Shared\Http\Controllers\Api\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class SaleController extends BaseController
{
    public function __construct(
        protected SaleService $service
    ) {
    }

    public function index(ListSaleRequest $request): JsonResponse
    {
        Gate::authorize('viewAny', Sale::class);

        $paginator = $this->service->paginate(
            perPage: $request->integer('limit', 10),
            filters: $request->validated()
        );

        return $this->paginatedResponse(
            SaleResource::collection($paginator),
            $paginator,
            'Sales retrieved successfully'
        );
    }

    public function store(StoreSaleRequest $request): JsonResponse
    {
        Gate::authorize('create', Sale::class);

        $sale = $this->service->store(
            new CreateSaleDTO(
                date: $request->validated()['date'],
                products: $request->validated()['products'],
            )
        );

        return $this->success(
            new SaleResource($sale),
            'Sale created successfully',
            201
        );
    }

    public function show(string $sale): JsonResponse
    {
        $sale = $this->service->find($sale);

        if (!$sale) {
            return $this->error('Sale not found', status: 404);
        }

        Gate::authorize('view', $sale);

        return $this->success(new SaleResource($sale));
    }

    public function update(UpdateSaleRequest $request, string $sale): JsonResponse
    {
        $target = $this->service->find($sale);

        if (!$target) {
            return $this->error('Sale not found', status: 404);
        }

        Gate::authorize('update', $target);

        $updated = $this->service->update(
            $sale,
            new UpdateSaleDTO(
                code: $request->validated()['code'] ?? null,
            ),
        );

        return $this->success(
            new SaleResource($updated),
            'Sale updated successfully'
        );
    }

    public function destroy(string $sale): JsonResponse
    {
        $target = $this->service->find($sale);

        if (!$target) {
            return $this->error('Sale not found', status: 404);
        }

        Gate::authorize('delete', $target);

        $deleted = $this->service->delete($sale);

        if (!$deleted) {
            return $this->error('Sale not found', status: 404);
        }

        return $this->success(message: 'Sale deleted successfully');
    }

    public function export(Request $request)
    {
        Gate::authorize('viewAny', Sale::class);

        return $this->service->exportCsv($request->only([
            'search',
            'status',
            'date_from',
            'date_to',
            'sort_by',
            'sort_direction',
        ]));
    }
}
