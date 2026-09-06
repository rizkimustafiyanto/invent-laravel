<?php

namespace App\Modules\SaleDetail\Controllers;

use App\Models\SaleDetail;
use App\Modules\SaleDetail\DTOs\CreateSaleDetailDTO;
use App\Modules\SaleDetail\DTOs\UpdateSaleDetailDTO;
use App\Modules\SaleDetail\Requests\StoreSaleDetailRequest;
use App\Modules\SaleDetail\Requests\UpdateSaleDetailRequest;
use App\Modules\SaleDetail\Resources\SaleDetailResource;
use App\Modules\SaleDetail\Services\SaleDetailService;
use App\Modules\Shared\Http\Controllers\Api\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class SaleDetailController extends BaseController
{
    public function __construct(
        protected SaleDetailService $service
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', SaleDetail::class);
        $paginator = $this->service->paginate($request->integer('limit', 10));

        return $this->paginatedResponse(
            SaleDetailResource::collection($paginator),
            $paginator,
            'Sales retrieved successfully'
        );
    }

    public function store(StoreSaleDetailRequest $request): JsonResponse
    {
        Gate::authorize('create', SaleDetail::class);
        $product = $this->service->store(
            new CreateSaleDetailDTO(
                sale_id: $request->validated()['sale_id'],
                product_id: $request->validated()['product_id'],
                qty: $request->validated()['qty'],
            )
        );

        return $this->success(
            new SaleDetailResource($product),
            'Sale created successfully',
            201
        );
    }

    public function show(string $product): JsonResponse
    {
        $product = $this->service->find($product);

        if (!$product) {
            return $this->error('Sale not found', status: 404);
        }

        Gate::authorize('view', $product);

        return $this->success(new SaleDetailResource($product));
    }

    public function update(UpdateSaleDetailRequest $request, string $product): JsonResponse
    {
        $target = $this->service->find($product);

        if (!$target) {
            return $this->error('Sale not found', status: 404);
        }

        Gate::authorize('update', $target);
        $updated = $this->service->update(
            $product,
            new UpdateSaleDetailDTO(
                sale_id: $request->validated()['sale_id'] ?? null,
                product_id: $request->validated()['product_id'] ?? null,
                qty: $request->validated()['qty'] ?? null,
            ),
        );

        return $this->success(
            new SaleDetailResource($updated),
            'Sale updated successfully'
        );
    }

    public function destroy(string $product): JsonResponse
    {
        $target = $this->service->find($product);

        if (!$target) {
            return $this->error('Sale not found', status: 404);
        }

        Gate::authorize('delete', $target);
        $deleted = $this->service->delete($product);

        if (!$deleted) {
            return $this->error('Sale not found', status: 404);
        }

        return $this->success(message: 'Sale deleted successfully');
    }
}
