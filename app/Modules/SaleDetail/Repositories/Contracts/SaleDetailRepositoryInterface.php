<?php

namespace App\Modules\SaleDetail\Repositories\Contracts;

use App\Models\SaleDetail;
use App\Modules\Shared\Contracts\RepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends RepositoryInterface<SaleDetail>
 */
interface SaleDetailRepositoryInterface extends RepositoryInterface
{
    /**
     * @return Collection<int, SaleDetail>
     */
    public function findBySaleId(string $saleId): Collection;

    /**
     * @return array{total_qty: float, total_amount: float}
     */
    public function sumBySaleId(string $saleId): array;

    public function findBySaleIdAndProductId(string $saleId, string $productId): ?Model;
}
