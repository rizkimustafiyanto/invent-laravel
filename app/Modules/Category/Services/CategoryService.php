<?php

namespace App\Modules\Category\Services;

use App\Modules\Category\DTOs\CreateCategoryDTO;
use App\Modules\Category\DTOs\UpdateCategoryDTO;
use App\Modules\Category\Repositories\Contracts\CategoryRepositoryInterface;
use App\Modules\Shared\Services\BaseService;

class CategoryService extends BaseService
{
    public function __construct(
        protected CategoryRepositoryInterface $categories,
    ) {
    }

    public function paginate(int $perPage = 10, array $filters = [])
    {
        return $this->categories->paginate($perPage, $filters);
    }

    public function find(string $id)
    {
        return $this->categories->find($id);
    }

    public function store(CreateCategoryDTO $data)
    {
        return $this->transaction(function () use ($data) {
            $exist = $this->categories->findByName($data->name);
            if ($exist) {
                throw new \RuntimeException('Category is exists');
            }
            $category = $this->categories->create($data->toArray());
            $this->audit('create', $category, [], $category->toArray());

            return $this->categories->find($category->id);
        });
    }

    public function update(string $id, UpdateCategoryDTO $data)
    {
        return $this->transaction(function () use ($id, $data) {
            $category = $this->categories->find($id);

            if (!$category) {
                return null;
            }

            $payload = array_filter($data->toArray(), static fn ($value) => $value !== null);
            $updated = $this->categories->update($id, $payload);

            if ($updated) {
                $this->audit('update', $updated, $category->toArray(), $updated->toArray());
            }

            return $updated;
        });
    }

    public function delete(string $id)
    {
        return $this->transaction(function () use ($id) {
            $category = $this->categories->find($id);

            if (!$category) {
                return null;
            }

            $deleted = $this->categories->delete($id);

            if ($deleted) {
                $this->audit('delete', $category, $category->toArray(), []);
            }

            return $deleted;
        });
    }

    public function optionCategory(array $filters = []): array
    {
        $categories = $this->categories->optionList($filters);

        return $categories->map(fn ($item) => [
            'label' => $item->name,
            'value' => $item->id,
        ])->values()->all();

    }

}
