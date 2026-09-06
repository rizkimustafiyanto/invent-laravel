<?php

namespace App\Modules\Product\Controllers;

use App\Models\Product;
use App\Modules\Product\DTOs\CreateProductDTO;
use App\Modules\Product\DTOs\UpdateProductDTO;
use App\Modules\Product\Requests\ListProductRequest;
use App\Modules\Product\Requests\StoreProductRequest;
use App\Modules\Product\Requests\UpdateProductRequest;
use App\Modules\Product\Resources\ProductResource;
use App\Modules\Product\Services\ProductService;
use App\Modules\Shared\Http\Controllers\Api\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;

class ProductController extends BaseController
{
    public function __construct(
        protected ProductService $service
    ) {
    }

    public function index(ListProductRequest $request): JsonResponse
    {
        Gate::authorize('viewAny', Product::class);

        $paginator = $this->service->paginate(
            perPage: $request->integer('limit', 10),
            filters: $request->validated()
            );

        return $this->paginatedResponse(
            ProductResource::collection($paginator),
            $paginator,
            'Products retrieved successfully'
        );
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        Gate::authorize('create', Product::class);
        $user = $request->user();

        if (!$user) {
            return $this->error('Unauthenticated', status: 401);
        }

        $product = $this->service->store(
            new CreateProductDTO(
                code: $request->validated()['code'],
                name: $request->validated()['name'],
                price: $request->validated()['price'],
                created_by: (int) $user->id,
                stock: $request->validated()['stock'] ?? 0,
                category_id: $request->validated()['category_id'] ?? null,
            ),
            $request->file('image')
        );

        return $this->success(
            new ProductResource($product),
            'Product created successfully',
            201
        );
    }

    public function show(string $product): JsonResponse
    {
        $product = $this->service->find($product);

        if (!$product) {
            return $this->error('Product not found', status: 404);
        }

        Gate::authorize('view', $product);

        return $this->success(new ProductResource($product));
    }

    public function update(UpdateProductRequest $request, string $product): JsonResponse
    {
        $target = $this->service->find($product);

        if (!$target) {
            return $this->error('Product not found', status: 404);
        }

        Gate::authorize('update', $target);

        $updated = $this->service->update(
            $product,
            new UpdateProductDTO(
                code: $request->validated()['code'] ?? null,
                name: $request->validated()['name'] ?? null,
                price: $request->validated()['price'] ?? null,
                stock: $request->validated()['stock'] ?? null,
                category_id: $request->validated()['category_id'] ?? null,
            ),
            $request->file('image')
        );

        return $this->success(
            new ProductResource($updated),
            'Product updated successfully'
        );
    }

    public function destroy(string $product): JsonResponse
    {
        $target = $this->service->find($product);

        if (!$target) {
            return $this->error('Product not found', status: 404);
        }

        Gate::authorize('delete', $target);

        $deleted = $this->service->delete($product);

        if (!$deleted) {
            return $this->error('Product not found', status: 404);
        }

        return $this->success(message: 'Product deleted successfully');
    }

    public function export(): mixed
    {
        Gate::authorize('viewAny', Product::class);

        return $this->service->exportCsv();
    }

    public function template(): mixed
    {
        Gate::authorize('create', Product::class);

        return $this->service->templateCsv();
    }

    public function import(Request $request): JsonResponse
    {
        Gate::authorize('create', Product::class);

        $user = $request->user();

        if (!$user) {
            return $this->error('Unauthenticated', status: 401);
        }

        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        /** @var UploadedFile $file */
        $file = $request->file('file');
        $result = $this->service->importCsv($file->getRealPath(), (int) $user->id);

        return $this->success($result, 'Products imported successfully');
    }
}
