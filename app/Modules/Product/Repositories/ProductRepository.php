<?php

namespace App\Modules\Product\Repositories;

use App\Models\Product;
use App\Modules\Product\Repositories\Contracts\ProductRepositoryInterface;
use App\Modules\Shared\Repositories\BaseRepository;

/**
 * @extends BaseRepository<Product>
 */
class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }

    public function findByCode(string $code): ?Product
    {
        return $this->model->newQuery()->where('code', $code)->first();
    }

    protected function searchableColumns(): array
    {
        return ['code', 'name'];
    }
}
