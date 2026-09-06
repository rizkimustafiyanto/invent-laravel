<?php

namespace App\Modules\Category\Controllers;

use App\Models\Category;
use App\Modules\Category\DTOs\CreateCategoryDTO;
use App\Modules\Category\DTOs\UpdateCategoryDTO;
use App\Modules\Category\Requests\ListCategoryRequest;
use App\Modules\Category\Requests\StoreCategoryRequest;
use App\Modules\Category\Requests\UpdateCategoryRequest;
use App\Modules\Category\Resources\CategoryResource;
use App\Modules\Category\Services\CategoryService;
use App\Modules\Shared\Http\Controllers\Api\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class CategoryController extends BaseController
{
    public function __construct(
        protected CategoryService $service
    ) {
    }

    public function index(ListCategoryRequest $request): JsonResponse
    {
        Gate::authorize('viewAny', Category::class);

        $paginator = $this->service->paginate(
            perPage: $request->integer('limit', 10),
            filters: $request->validated()
        );

        return $this->paginatedResponse(
            CategoryResource::collection($paginator),
            $paginator,
            'Categorys retrieved successfully'
        );
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        Gate::authorize('create', Category::class);

        $category = $this->service->store(
            new CreateCategoryDTO(
                name: $request->validated()['name'],
            )
        );

        return $this->success(
            new CategoryResource($category),
            'Category created successfully',
            201
        );
    }

    public function show(string $category): JsonResponse
    {
        $category = $this->service->find($category);

        if (!$category) {
            return $this->error('Category not found', status: 404);
        }

        Gate::authorize('view', $category);

        return $this->success(new CategoryResource($category));
    }

    public function update(UpdateCategoryRequest $request, string $category): JsonResponse
    {
        $target = $this->service->find($category);

        if (!$target) {
            return $this->error('Category not found', status: 404);
        }

        Gate::authorize('update', $target);

        $updated = $this->service->update(
            $category,
            new UpdateCategoryDTO(
                name: $request->validated()['name'] ?? null,
            ),
        );

        return $this->success(
            new CategoryResource($updated),
            'Category updated successfully'
        );
    }

    public function destroy(string $category): JsonResponse
    {
        $target = $this->service->find($category);

        if (!$target) {
            return $this->error('Category not found', status: 404);
        }

        Gate::authorize('delete', $target);

        $deleted = $this->service->delete($category);

        if (!$deleted) {
            return $this->error('Category not found', status: 404);
        }

        return $this->success(message: 'Category deleted successfully');
    }

    public function optioncategory(ListCategoryRequest $request): JsonResponse
    {
        Gate::authorize('viewAny', Category::class);

        return $this->success(
            $this->service->optionCategory($request->validated()),
            'Category retrieved successfully'
        );
    }
}
