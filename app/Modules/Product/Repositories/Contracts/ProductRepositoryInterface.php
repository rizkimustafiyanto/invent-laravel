<?php

namespace App\Modules\Product\Repositories\Contracts;

use App\Models\Product;
use App\Modules\Shared\Contracts\RepositoryInterface;

/**
 * @extends RepositoryInterface<Product>
 */
interface ProductRepositoryInterface extends RepositoryInterface
{
    public function findByCode(string $code): ?Product;
}
