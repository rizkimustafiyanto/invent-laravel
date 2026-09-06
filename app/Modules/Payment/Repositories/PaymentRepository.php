<?php

namespace App\Modules\Payment\Repositories;

use App\Models\Payment;
use App\Modules\Payment\Repositories\Contracts\PaymentRepositoryInterface;
use App\Modules\Shared\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends BaseRepository<Payment>
 */
class PaymentRepository extends BaseRepository implements PaymentRepositoryInterface
{
    public function __construct(Payment $model)
    {
        parent::__construct($model);
    }

    /**
     * @return Collection<int, Payment>
     */
    public function findBySaleId(string $saleId): Collection
    {
        return $this->model->newQuery()->where('sale_id', $saleId)->get();
    }

    public function existsForSale(string $saleId): bool
    {
        return $this->model->newQuery()->where('sale_id', $saleId)->exists();
    }

    public function firstForSale(string $saleId): ?Model
    {
        return $this->model->newQuery()->where('sale_id', $saleId)->first();
    }
}
