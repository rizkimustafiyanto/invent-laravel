<?php

namespace App\Modules\Category\Repositories;

use App\Models\Category;
use App\Modules\Category\Repositories\Contracts\CategoryRepositoryInterface;
use App\Modules\Shared\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends BaseRepository<Category>
 */
class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{
    public function __construct(Category $model)
    {
        parent::__construct($model);
    }

    /**
     * @return list<string>
     */
    protected function searchableColumns(): array
    {
        return ['name'];
    }

    /**
     * @return list<string>
     */
    protected function sortableColumns(): array
    {
        return ['name', 'created_at'];
    }

    protected function defaultSortColumn(): ?string
    {
        return 'name';
    }

    protected function query(array $filters = []): Builder
    {
        return parent::query($filters);
    }

    public function findByName(string $name): ?Model
    {
        return $this->model->newQuery()
            ->whereRaw('LOWER(name) = ?', [strtolower($name)])
            ->first();
    }

    /**
     * @return Collection<int, Category>
     */
    public function optionList(array $filters = []): Collection
    {
        return $this->query($filters)
            ->get(['id', 'name']);
    }
}
