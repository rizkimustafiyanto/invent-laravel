<?php

namespace App\Modules\Payment\Repositories\Contracts;

use App\Models\Payment;
use App\Modules\Shared\Contracts\RepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends RepositoryInterface<Payment>
 */
interface PaymentRepositoryInterface extends RepositoryInterface
{
    /**
     * @return Collection<int, Payment>
     */
    public function findBySaleId(string $saleId): Collection;

    public function existsForSale(string $saleId): bool;

    public function firstForSale(string $saleId): ?Model;
}
