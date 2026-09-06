<?php

namespace App\Modules\Payment\Controllers;

use App\Enums\PaymentMethod;
use App\Models\Payment;
use App\Modules\Payment\DTOs\CreatePaymentDTO;
use App\Modules\Payment\DTOs\UpdatePaymentDTO;
use App\Modules\Payment\Requests\StorePaymentRequest;
use App\Modules\Payment\Requests\UpdatePaymentRequest;
use App\Modules\Payment\Resources\PaymentResource;
use App\Modules\Payment\Services\PaymentService;
use App\Modules\Shared\Http\Controllers\Api\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PaymentController extends BaseController
{
    public function __construct(
        protected PaymentService $service
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', Payment::class);
        $paginator = $this->service->paginate($request->integer('limit', 10));

        return $this->paginatedResponse(
            PaymentResource::collection($paginator),
            $paginator,
            'Payments retrieved successfully'
        );
    }

    public function store(StorePaymentRequest $request): JsonResponse
    {
        Gate::authorize('create', Payment::class);
        $product = $this->service->store(
            new CreatePaymentDTO(
                date: $request->validated()['date'],
                sale_id: $request->validated()['sale_id'],
                payment_method: PaymentMethod::from($request->validated()['payment_method']),
            )
        );

        if (!$product) {
            return $this->error('Sale not found', status: 404);
        }

        return $this->success(
            new PaymentResource($product),
            'Payment created successfully',
            201
        );
    }

    public function show(string $product): JsonResponse
    {
        $product = $this->service->find($product);

        if (!$product) {
            return $this->error('Payment not found', status: 404);
        }

        Gate::authorize('view', $product);

        return $this->success(new PaymentResource($product));
    }

    public function update(UpdatePaymentRequest $request, string $product): JsonResponse
    {
        $target = $this->service->find($product);

        if (!$target) {
            return $this->error('Payment not found', status: 404);
        }

        Gate::authorize('update', $target);
        $updated = $this->service->update(
            $product,
            new UpdatePaymentDTO(
                date: $request->validated()['date'] ?? null,
                payment_method: isset($request->validated()['payment_method']) ? PaymentMethod::from($request->validated()['payment_method']) : null,
            ),
        );

        return $this->success(
            new PaymentResource($updated),
            'Payment updated successfully'
        );
    }

    public function destroy(string $product): JsonResponse
    {
        $target = $this->service->find($product);

        if (!$target) {
            return $this->error('Payment not found', status: 404);
        }

        Gate::authorize('delete', $target);
        $deleted = $this->service->delete($product);

        if (!$deleted) {
            return $this->error('Payment not found', status: 404);
        }

        return $this->success(message: 'Payment deleted successfully');
    }
}
