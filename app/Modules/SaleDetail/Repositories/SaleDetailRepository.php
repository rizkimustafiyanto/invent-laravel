<?php

namespace App\Modules\SaleDetail\Repositories;

use App\Models\SaleDetail;
use App\Modules\SaleDetail\Repositories\Contracts\SaleDetailRepositoryInterface;
use App\Modules\Shared\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends BaseRepository<SaleDetail>
 */
class SaleDetailRepository extends BaseRepository implements SaleDetailRepositoryInterface
{
    public function __construct(SaleDetail $model)
    {
        parent::__construct($model);
    }

    /**
     * @return Collection<int, SaleDetail>
     */
    public function findBySaleId(string $saleId): Collection
    {
        return $this->model->newQuery()->where('sale_id', $saleId)->get();
    }

    public function sumBySaleId(string $saleId): array
    {
        $query = $this->model->newQuery()->where('sale_id', $saleId);

        return [
            'total_qty' => (float) $query->sum('qty'),
            'total_amount' => (float) $query->sum('total_price'),
        ];
    }

    public function findBySaleIdAndProductId(string $saleId, string $productId): ?Model
    {
        return $this->model->newQuery()
            ->where('sale_id', $saleId)
            ->where('product_id', $productId)
            ->first();
    }
}
